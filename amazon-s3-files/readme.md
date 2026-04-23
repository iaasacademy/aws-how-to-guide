<h1>Amazon S3 Files – Mount S3 Like a File System (Step-by-Step Guide)</h1>

<h2>Stop using API calls to access S3. Mount it like a filesystem instead.</h2>

This project demonstrates how to use Amazon S3 Files to mount an S3 bucket directly onto a Linux-based EC2 instance and interact with it using standard file system commands like ls, cat, and echo.

📺 Based on the full walkthrough video:

🔥 What is Amazon S3 Files?

Amazon S3 Files is a new feature that allows you to:

Mount an S3 bucket as a file system
Use standard file operations instead of API calls
Access S3 data like a local drive

👉 No more:

get_object
SDK complexity
Manual file transfers
🧠 Why This Matters

Traditionally, Amazon S3 is an object storage service, meaning:

Data is accessed via API calls
Not designed for native file system operations

Example:

s3.get_object(Bucket="my-bucket", Key="file.txt")

Or CLI:

aws s3 cp s3://my-bucket/file.txt ./file.txt

👉 You couldn’t simply do:

cat /mnt/s3/file.txt
⚖️ The Old Approach (S3 vs EFS)
📦 Amazon S3
✅ Infinite scale
✅ Cost-effective
❌ API-based access
❌ Higher latency for small files
📁 Amazon EFS
✅ POSIX-compliant filesystem
✅ Native Linux support
❌ Expensive at scale

👉 Many teams used EFS just to bridge the gap to S3.

💡 What S3 Files Solves

With S3 Files:

cat /mnt/s3/file.txt
echo "hello world" > /mnt/s3/file.txt

👉 You get:

File system interface
S3 scalability
Reduced complexity
🏗️ Architecture Overview
S3 bucket (with versioning enabled)
S3 Files file system linked to bucket
EC2 instance in VPC
Mount targets across AZs
IAM roles for access control
Optional VPC Endpoint for private access
⚙️ Prerequisites

Before starting:

AWS Account
EC2 instance (Linux)
S3 bucket with versioning enabled
IAM roles configured
Security groups allowing:
NFS (port 2049)
🔐 IAM Policy

You can find the required IAM policy here:

👉 https://github.com/iaasacademy/aws-how-to-guide/tree/main/amazon-s3-files

🚀 Step-by-Step Setup
1. Create S3 Bucket
Enable versioning
Create folders (prefixes)
2. Create S3 Files File System
Link to S3 bucket
Select VPC
Wait for provisioning
3. Create IAM Role

Attach:

AmazonS3FullAccess (or scoped policy)
AmazonElasticFileSystemUtils
Custom inline policy (from GitHub)
4. Launch EC2 Instance
Use Amazon Linux
Attach IAM role
Place in same VPC
5. Configure Security Groups

Allow:

NFS (2049) from EC2 → mount targets
6. Connect to EC2 and Setup

Install dependencies:

sudo yum install -y amazon-efs-utils

Create mount directory:

mkdir -p /mnt/s3files

Mount file system:

sudo mount -t efs <file-system-id>:/ /mnt/s3files
7. Test It 🎉
cd /mnt/s3files
echo "Hello World" > test.txt
ls
cat test.txt

👉 Files will automatically sync with S3.

⚡ Performance Insight (Important)

S3 Files uses smart caching:

Files < 128 KB → cached locally (low latency)
Large files → streamed from S3 (high throughput)

👉 Best of both worlds:

Speed for small files
Scale for large files
⚠️ When NOT to Use S3 Files

S3 Files is NOT a replacement for EFS.

Use Amazon EFS when:

Low latency is critical
Heavy write workloads
File locking is required

Use S3 Files when:

Data analytics
Large datasets
Read-heavy workloads
🎯 Key Takeaways
S3 = scalable object storage
EFS = POSIX file system
S3 Files = bridge between both

👉 “S3 Files doesn’t change what S3 does… it changes how you use it.”

👨‍💻 Who This Is For
AWS learners & certification students
Solutions Architects
DevOps Engineers
Data Engineers
⭐ Support

If this helped you:

⭐ Star this repo
🍴 Fork it
💬 Share feedback
🚀 Want to Go Deeper?

Master AWS with real-world projects 👇

👉 https://iaas-academy1.teachable.com/p/aws-certified-solutions-architect-course-saa-c03

✔ Build 3 Capstone Projects
✔ Design production-ready architectures
✔ Pass the SAA-C03 exam
