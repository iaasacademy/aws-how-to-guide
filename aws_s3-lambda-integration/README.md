# AWS S3 ➜ Lambda ➜ CloudWatch ➜ Slack + SNS + DynamoDB Integration

This project demonstrates a serverless architecture where:
- S3 uploads trigger a Lambda
- Lambda sends alerts to Slack and SNS
- Upload metadata is logged into DynamoDB
- API Gateway allows query of metadata

## Deployment Notes
- Store secrets like Slack Webhook in AWS SSM or Lambda environment variables.
- Use IAM roles with least privilege.
