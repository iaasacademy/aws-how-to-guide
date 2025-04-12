#!/bin/bash
yum update -y
yum install -y httpd
systemctl start httpd
systemctl enable httpd

# For Server 2: Pick a random background and a custom message to emphasize its unique role.
if [ $((RANDOM % 2)) -eq 0 ]; then
    BG_COLOR="linear-gradient(135deg, #8E44AD, #3498DB)"   # A vibrant purple-to-blue gradient
    MSG="This is Server 2: Where creativity meets technology!"
else
    BG_COLOR="linear-gradient(135deg, #27AE60, #2ECC71)"   # A fresh green gradient
    MSG="Welcome to Server 2: Powering the Load Balancer with flair!"
fi

# Create a custom index.html page with dynamic content indicating this is Server 2.
cat << EOF > /var/www/html/index.html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome Session 10 Students - Server 2</title>
  <!-- Using Montserrat for a modern bold look -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      background: $BG_COLOR;
      font-family: 'Montserrat', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      color: #fff;
      text-align: center;
    }
    h1 {
      font-size: 3em;
      font-weight: 700;
      margin-bottom: 20px;
    }
    p {
      font-size: 1.5em;
      font-weight: bold;
      margin: 5px;
    }
    .note {
      font-size: 1em;
      margin-top: 20px;
      opacity: 0.8;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Welcome Session 10 Students - Server 2</h1>
    <p>$MSG</p>
    <p>This instance: $(hostname)</p>
    <p class="note">Served exclusively via AWS Load Balancer</p>
  </div>
</body>
</html>
EOF

systemctl restart httpd
