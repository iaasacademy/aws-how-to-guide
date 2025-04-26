import json
import boto3
import urllib3
import os
from datetime import datetime

http = urllib3.PoolManager()

SLACK_WEBHOOK_URL = os.environ['SLACK_WEBHOOK_URL']
SNS_TOPIC_ARN = os.environ['SNS_TOPIC_ARN']
DDB_TABLE_NAME = os.environ['DDB_TABLE_NAME']

def lambda_handler(event, context):
    s3_info = event['Records'][0]['s3']
    bucket_name = s3_info['bucket']['name']
    object_key = s3_info['object']['key']
    timestamp = datetime.utcnow().isoformat()

    message = f"🚨 Critical Upload Detected 🚨\nBucket: {bucket_name}\nFile: {object_key}"

    # Slack Notification
    slack_data = {"text": message}
    http.request('POST', SLACK_WEBHOOK_URL, body=json.dumps(slack_data).encode('utf-8'), headers={'Content-Type': 'application/json'})

    # SNS Notification
    sns = boto3.client('sns')
    sns.publish(TopicArn=SNS_TOPIC_ARN, Message=message)

    # DynamoDB Logging
    ddb = boto3.resource('dynamodb')
    table = ddb.Table(DDB_TABLE_NAME)
    table.put_item(Item={'fileName': object_key, 'uploadTimestamp': timestamp, 'bucketName': bucket_name})

    return {'statusCode': 200, 'body': json.dumps('Success')}
