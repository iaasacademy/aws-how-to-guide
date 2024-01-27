import os
import base64
import boto3 # type: ignore
from urllib import parse
from typing import Any, Dict, Optional

ddb: Any = boto3.client("dynamodb") # type: ignore
# Explicit region must be set to address a bug in S3/boto3.
bucket_region: str = os.environ.get('BOOKSTORE_BUCKET_REGION', '')
s3: Any = boto3.client('s3', region_name=bucket_region, endpoint_url=f'https://s3.{bucket_region}.amazonaws.com') # type: ignore
ses: Any = boto3.client("sesv2") # type: ignore


def extract_profile_from_event(event):
    """Extract body which may be in base64 encoding, from API Gateway event."""
    body_raw = event.get("body", "")
    is_base64_encoded = event.get("isBase64Encoded", False)

    if is_base64_encoded:
        body = base64.b64decode(body_raw).decode("ascii")
    else:
        body = body_raw

    payload = parse.parse_qs(body, False)

    # The payload dict is formatted as:
    # {
    #   'name': ["Jane"],
    #   'email': ["jane@example.com"]
    # }
    # The array exists because HTML forms can have multiple <input /> tags with the same "name".
    # In our case we will only use one <input> per field, so let's use the first element.

    return {
        'name': payload.get("name", [""])[0],
        'email': payload.get("email", [""])[0]
    }


def create_response(status, body, headers):
    """Generate a API Gateway V2 response."""
    return {
        "statusCode": status,
        "body": body,
        "headers": headers
    }


def perform_workflow(profile):
    """Perform workflow.

    This function takes the given profile (name, email, etc), persists it,
    and sends a email with a download link.
    """
    # Step 1:
    #
    # Persist profile to database.
    #
    # The following command will store our profile data (name, email) to
    # our DynamoDB table.
    # In our case, the primary (hash) key is the email, and there is no sort key.
    ddb.put_item(
        Item={
            'Email': {
                'S': profile.get("email", "")
            },
            'Name': {
                'S': profile.get("name", "")
            }
        },
        ReturnConsumedCapacity="TOTAL",
        TableName=os.environ.get("PROFILE_TABLE_NAME"),
    )

    # Step 2:
    #
    # Generate download URL.
    #
    # We need to be careful when generating presigned URLs since this does not make
    # any validation attempts before generating.
    presigned_url: str = s3.generate_presigned_url(
        ClientMethod="get_object",
        Params={
            'Bucket': os.environ.get("BOOKSTORE_BUCKET"),
            'Key': os.environ.get("BOOKSTORE_BOOK_KEY")
        },
        ExpiresIn=3600
    )

    # Step 3:
    #
    # Send transactional email with presigned URL.
    #
    # The From address must be validated in the AWS SES Console before
    # attempting to use it.
    # Note that AWS accounts start with a Sandbox stage, which only lets you send emails
    # to verified addresses to prevent spam/fraud.
    ses.send_email(
        FromEmailAddress=os.environ.get("FROM_EMAIL_ADDRESS"),
        Destination={
            'ToAddresses': [
                profile.get("email", "")
            ]
        },
        Content={
            'Simple': {
                'Subject': {
                    'Data': "Get you copy of the book"
                },
                'Body': {
                    'Html': {
                        'Data': f"""
                                Hello {profile.get("name", "")},
                                <br><br>
                                Here is your copy of the requested book:
                                <a href="{presigned_url}" target="_blank">Link to PDF</a>
                                <br><br>
                                Have a nice day.
                            """.strip()
                    }
                }
            }
        }
    )


def lambda_handler(event, context):
    """AWS Lambda Handler to respond to the API Gateway event."""
    try:
        # Verify content type (we're only dealing with POST forms at the moment).
        # content_type: str = event.get("headers", {}).get("content-type", "")

        # if "application/x-www-form-urlencoded" not in content_type:
        #     return generate_response(415, "Not implemented")

        profile: dict[str, str] = extract_profile_from_event(event)

        if not profile.get("name") or not profile.get("email"):
            return create_response(400, "One or more required fields are empty.", headers={
                "Access-Control-Allow-Origin": event.get("headers", {}).get("origin", "*"),
                "Access-Control-Allow-Credentials": "true"
            })

        perform_workflow(profile)

        # We are responding with a 201 status (Created) to indicate that
        # the profile was persisted and an email was dispatched.
        return create_response(201, "", headers={
            "Access-Control-Allow-Origin": event.get("headers", {}).get("origin", "*"),
            "Access-Control-Allow-Credentials": "true"
        })
    except Exception as exception:
        print(f"An unknown error occurred: {exception}")

        return create_response(500, "Cannot process your request.", headers={
            "Access-Control-Allow-Origin": event.get("headers", {}).get("origin", "*"),
            "Access-Control-Allow-Credentials": "true"
        })
