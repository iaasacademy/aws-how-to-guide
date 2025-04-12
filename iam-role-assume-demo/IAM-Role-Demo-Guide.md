**Demo: Creating an IAM Role with Permissions Boundary (Printable Guide)**

---

## 🌟 Goal
Create an IAM role that:
- Can be assumed by a specific IAM user
- Grants permissions (e.g., manage EC2)
- Is limited by a permissions boundary (e.g., block S3 access)

---

## ✅ Step-by-Step Guide

### Step 1: Create Permissions Boundary Policy

1. Go to IAM Console → Policies → **Create Policy**
2. Choose **JSON** and paste:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "DenyS3Access",
      "Effect": "Deny",
      "Action": "s3:*",
      "Resource": "*"
    },
    {
      "Sid": "AllowAllOther",
      "Effect": "Allow",
      "Action": "*",
      "Resource": "*"
    }
  ]
}
```

3. Click **Next** → Name it `DenyS3-PermissionsBoundary` → **Create policy**

---

### Step 2: Create IAM Role

1. IAM Console → Roles → **Create Role**
2. **Trusted entity type**: Another AWS Account
3. Enter your Account ID
4. Click **Next**

---

### Step 3: Attach Permissions to Role

Example EC2 Access Policy:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": "ec2:*",
      "Resource": "*"
    }
  ]
}
```

- Create this policy as `AllowEC2Access`
- Attach it to the role

---

### Step 4: Set Permissions Boundary

- During role creation, choose **Use a permissions boundary**
- Select `DenyS3-PermissionsBoundary`
- Name the role `AssumableEC2Role` and **Create Role**

---

### Step 5: Trust Policy (Allow User to Assume Role)

1. Go to Role → **Trust Relationships** → **Edit**
2. Paste:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Principal": {
        "AWS": "arn:aws:iam::<ACCOUNT-ID>:user/developer-user"
      },
      "Action": "sts:AssumeRole"
    }
  ]
}
```

---

### Step 6: IAM User Permissions to Assume Role

1. Go to IAM → Users → `developer-user` → **Permissions**
2. Attach Inline Policy:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": "sts:AssumeRole",
      "Resource": "arn:aws:iam::<ACCOUNT-ID>:role/AssumableEC2Role"
    }
  ]
}
```

---

### 🔮 Test It (AWS CLI)

```bash
aws sts assume-role \
  --role-arn arn:aws:iam::<ACCOUNT-ID>:role/AssumableEC2Role \
  --role-session-name testsession
```

Export returned credentials and test:

```bash
export AWS_ACCESS_KEY_ID=...
export AWS_SECRET_ACCESS_KEY=...
export AWS_SESSION_TOKEN=...

aws ec2 describe-instances     # ✅ Allowed
aws s3 ls                      # ❌ Denied
```

---

## 📄 Summary Table

| Component            | Purpose                                    |
|---------------------|--------------------------------------------|
| IAM Role            | Grants EC2 access                          |
| Trust Policy        | Allows specific IAM user to assume it      |
| Permissions Boundary| Blocks any S3 actions                      |
| Inline User Policy  | Lets the user assume the specific role     |

---

Need this as Terraform/CloudFormation? Just ask ✨
