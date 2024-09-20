#!/bin/bash

# Update OS and Install Apache Web Services
sudo yum update
sudo yum install httpd -y
sudo systemctl start httpd
sudo systemctl enable httpd
chkconfig httpd on 

# Define permissions for ec2-user
usermod -a -G apache ec2-user
chown -R ec2-user:apache /var/www
chmod 2775 /var/www
find /var/www -type d -exec chmod 2775 {} \;
find /var/www -type f -exec chmod 0664 {} \;

# Grab an IMDSv2 token
TOKEN=`curl -s -X PUT "http://169.254.169.254/latest/api/token" -H "X-aws-ec2-metadata-token-ttl-seconds: 21600"`
# Using that token, get the AZ.  Note the use of back ticks to call curl, and the variable name
INSTANCE_AZ=`curl -s -H "X-aws-ec2-metadata-token: $TOKEN" http://169.254.169.254/latest/meta-data/placement/availability-zone`

# Create an index.html file
cat > /var/www/html/index.html <<EOF
<html>
<head>
    <title>AWS Auto Scaling Demo</title>
    <style>
        body {
            background-color: #ffa200;
            color: white;
            font-size: 24px;
            display: flex;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
    </style>
    <body>
        <h1>This instance is located in Availabity Zone: $INSTANCE_AZ
    </body>
</html>
EOF

# Create a health check file
echo "This is the health check file" > /var/www/html/health.html