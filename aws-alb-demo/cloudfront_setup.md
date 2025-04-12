# CloudFront Setup

AWS CloudFront is a Content Delivery Network (CDN) that improves your website’s performance by caching content at edge locations around the world.

## Steps:

1. **Create a CloudFront Distribution**:
   - Set the ALB DNS as the origin.
   - Adjust the viewer protocol policies and caching behaviors as needed.
   - Configure Alternate Domain Names (CNAMEs) and SSL certificates if using a custom domain.
2. **Testing**:
   - Access your CloudFront URL to ensure content is being delivered properly.
