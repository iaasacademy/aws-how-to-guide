# Route 53 DNS Setup

This document explains how to set up Route 53 to route traffic to your CloudFront distribution using your custom domain.

## Steps:

1. **Create or Use an Existing Hosted Zone**:
   - Ensure your domain’s hosted zone exists in Route 53.
2. **Create an Alias Record**:
   - Create an A record (alias) that points to your CloudFront distribution.
3. **Validation**:
   - Verify that your custom domain routes traffic correctly to your AWS infrastructure.
