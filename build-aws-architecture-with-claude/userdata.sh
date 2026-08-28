#!/bin/bash
# Launch-template user data (DynamoDB edition). Runs as root on every instance the
# Auto Scaling group starts: pulls the app from S3 and starts it as a service.
# No database setup at all - DynamoDB is serverless and the table already exists.
# No SSH, no SSM, no manual steps.
set -euxo pipefail

# ---------------- edit these four values before pasting in ----------------
CODE_BUCKET="REPLACE_WITH_CODE_BUCKET"
AWS_REGION="us-east-1"
BEDROCK_MODEL_ID="us.anthropic.claude-sonnet-4-6"
DDB_TABLE="AskArchitectDesigns"
# --------------------------------------------------------------------------

APP_DIR="/home/ec2-user/ask-the-architect"

dnf update -y
dnf install -y python3-pip

# Pull the application code from the S3 "repository" (via the S3 gateway endpoint)
mkdir -p "$APP_DIR"
aws s3 cp --recursive "s3://${CODE_BUCKET}/app/" "$APP_DIR/" --region "$AWS_REGION"
chown -R ec2-user:ec2-user "$APP_DIR"

# Python environment
python3 -m venv "$APP_DIR/venv"
"$APP_DIR/venv/bin/pip" install --upgrade pip
"$APP_DIR/venv/bin/pip" install -r "$APP_DIR/requirements.txt"

# Runtime config
cat > /etc/ask-architect.env <<ENV
AWS_REGION=${AWS_REGION}
BEDROCK_MODEL_ID=${BEDROCK_MODEL_ID}
DDB_TABLE=${DDB_TABLE}
ENV

# systemd service so the app starts on boot and restarts on crash
cat > /etc/systemd/system/ask-architect.service <<'UNIT'
[Unit]
Description=Ask the Architect
After=network-online.target
Wants=network-online.target

[Service]
User=ec2-user
WorkingDirectory=/home/ec2-user/ask-the-architect
EnvironmentFile=/etc/ask-architect.env
ExecStart=/home/ec2-user/ask-the-architect/venv/bin/gunicorn -w 2 -b 0.0.0.0:8000 app:app
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
UNIT

systemctl daemon-reload
systemctl enable --now ask-architect
