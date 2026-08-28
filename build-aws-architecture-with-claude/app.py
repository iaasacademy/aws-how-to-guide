"""
Ask the Architect - a tiny AWS Solutions Architect advisor (DynamoDB edition).

    Browser -> ALB -> Flask (on EC2) -> Claude on Bedrock (returns structured JSON)
                                     -> DynamoDB (stores the design as a document)

Why DynamoDB: each design is a self-contained document (title, a list of
components, a branching flow, pillars) that we only ever write and read back by
key. That is a document / key-value workload, which is exactly what DynamoDB is
for. No VPC database tier, no Secrets Manager, no schema.

Security: no AWS access keys anywhere - boto3 uses the EC2 instance role, which
grants DynamoDB, S3 and Bedrock access and nothing else.
"""

import os
import json
import uuid
import datetime

import boto3
from flask import Flask, request, jsonify, render_template

app = Flask(__name__)

# ----------------------------------------------------------------- Config (env)
AWS_REGION = os.environ.get("AWS_REGION", "us-east-1")
BEDROCK_MODEL_ID = os.environ.get("BEDROCK_MODEL_ID", "us.anthropic.claude-sonnet-4-6")
DDB_TABLE = os.environ.get("DDB_TABLE", "AskArchitectDesigns")

bedrock = boto3.client("bedrock-runtime", region_name=AWS_REGION)
table = boto3.resource("dynamodb", region_name=AWS_REGION).Table(DDB_TABLE)

SYSTEM_PROMPT = """You are a senior AWS Solutions Architect. The user describes a \
system they need to build. Respond with ONLY a JSON object - no markdown, no code \
fences, no text before or after it - matching exactly this shape:
{
  "title": "short name of the solution",
  "summary": "one sentence describing the design",
  "components": [
    {"category": "Compute | Database | Networking | Security | Storage | Frontend | Auth | Integration | Monitoring", "service": "the AWS service", "reason": "one short sentence on why"}
  ],
  "flow": {
    "main": ["Users", "first service", "next service", "..."],
    "branches": [
      {"from": "a service that appears in main", "to": "a supporting service", "label": "short verb e.g. authorize, logs, async"}
    ]
  },
  "well_architected": [
    {"pillar": "Reliability | Security | Cost Optimization | Performance Efficiency | Operational Excellence | Sustainability", "note": "one short sentence"}
  ]
}
Rules:
- "main" is the primary request path, front to back, and MUST start with "Users".
- Use the SAME service names in "flow" as in "components" so they line up.
- Put supporting services that hang off one step - authentication, logging or \
monitoring, queues, caches - in "branches", each attached to the main service it \
supports.
- Include 4 to 8 components and 3 to 5 well_architected entries.
- Keep every reason, note and label to a few words."""


def parse_design(text):
    """Turn the model's reply into a design dict. Robust to stray fences or prose;
    guarantees a 'flow' exists; falls back to a raw block if all else fails."""
    cleaned = text.strip()
    if cleaned.startswith("```"):
        cleaned = cleaned.strip("`")
        if cleaned[:4].lower() == "json":
            cleaned = cleaned[4:]
    start, end = cleaned.find("{"), cleaned.rfind("}")
    if start != -1 and end != -1:
        cleaned = cleaned[start:end + 1]
    try:
        data = json.loads(cleaned)
        if isinstance(data, dict) and isinstance(data.get("components"), list):
            data.setdefault("title", "Suggested architecture")
            data.setdefault("summary", "")
            data.setdefault("well_architected", [])
            flow = data.get("flow")
            if (not isinstance(flow, dict)
                    or not isinstance(flow.get("main"), list)
                    or not flow.get("main")):
                services = [c.get("service", "") for c in data["components"] if c.get("service")]
                flow = {"main": ["Users"] + services, "branches": []}
            flow.setdefault("branches", [])
            data["flow"] = flow
            return data
    except Exception:  # noqa: BLE001
        pass
    return {"title": "Suggested architecture", "summary": "", "components": [],
            "flow": {"main": [], "branches": []}, "well_architected": [], "raw": text}


@app.route("/")
def index():
    return render_template("index.html")


@app.route("/healthz")
def healthz():
    return "ok", 200


@app.route("/api/ask", methods=["POST"])
def ask():
    data = request.get_json(silent=True) or {}
    scenario = (data.get("scenario") or "").strip()
    if not scenario:
        return jsonify({"error": "Please describe a scenario first."}), 400

    try:
        result = bedrock.converse(
            modelId=BEDROCK_MODEL_ID,
            system=[{"text": SYSTEM_PROMPT}],
            messages=[{"role": "user", "content": [{"text": scenario}]}],
            inferenceConfig={"maxTokens": 2000, "temperature": 0.3},
        )
        answer = result["output"]["message"]["content"][0]["text"]
    except Exception as exc:  # noqa: BLE001
        app.logger.exception("Bedrock call failed")
        return jsonify({"error": f"Bedrock error: {exc}"}), 502

    design = parse_design(answer)
    created_at = datetime.datetime.utcnow().isoformat() + "Z"
    item_id = uuid.uuid4().hex

    try:
        # The design goes in as a native document - a nested map, not a JSON string.
        table.put_item(Item={
            "id": item_id,
            "scenario": scenario,
            "design": design,
            "created_at": created_at,
        })
    except Exception as exc:  # noqa: BLE001
        app.logger.exception("DynamoDB write failed")
        return jsonify({"id": None, "scenario": scenario, "design": design,
                        "warning": f"Answer generated but not saved: {exc}"}), 200

    return jsonify({"id": item_id, "scenario": scenario, "design": design,
                    "created_at": created_at})


@app.route("/api/history")
def history():
    try:
        # Scan is fine for a small demo table. At scale you'd use a partition key
        # plus a sort key on created_at and Query the most recent, or a GSI.
        resp = table.scan(Limit=50)
        items = resp.get("Items", [])
    except Exception as exc:  # noqa: BLE001
        app.logger.exception("DynamoDB read failed")
        return jsonify({"error": str(exc)}), 502

    items.sort(key=lambda x: x.get("created_at", ""), reverse=True)
    out = [{"id": i.get("id"), "scenario": i.get("scenario"),
            "design": i.get("design", {}), "created_at": i.get("created_at")}
           for i in items[:10]]
    return jsonify(out)


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=8000, debug=False)
