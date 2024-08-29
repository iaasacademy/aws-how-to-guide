#!/bin/bash
yum update -y
yum install -y httpd
systemctl start httpd
systemctl enable httpd
usermod -a -G apache ec2-user
chown -R ec2-user:apache /var/www
chmod 2775 /var/www
find /var/www -type d -exec chmod 2775 {} \;
find /var/www -type f -exec chmod 0664 {} \;
echo "<H1><Center><BODY BGCOLOR=#ffa200/#46acf8>This is WebServer One/Two in AZ 1A/1B (Change This) </Center></H1>" > /var/www/html/index.html
echo "This is the health check file" > /var/www/html/health.html