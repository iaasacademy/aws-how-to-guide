#!/bin/bash
yum update -y
yum install -y httpd
systemctl start httpd
systemctl enable httpd

# Pick a random background and message for fun
if [ $((RANDOM % 2)) -eq 0 ]; then
    BG_COLOR="linear-gradient(135deg, #FF5733, #FFC300)"   # A warm orange-yellow gradient
    MSG="You are viewing a fiery server instance behind our Load Balancer!"
else
    BG_COLOR="linear-gradient(135deg, #33C3FF, #A8E6CF)"   # A cool blue-green gradient
    MSG="Welcome to a cool server instance showcasing our Load Balancer!"
fi

# Create the custom index.html page with dynamic content
cat << EOF > /var/www/html/index.html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome Session 10 Students</title>
  <!-- Google Font for a modern, bold look -->
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
    <h1>Hello all Session 10 Students!</h1>
    <p>$MSG</p>
    <p>This instance: $(hostname)</p>
    <p class="note">Loaded via AWS Load Balancer</p>
  </div>
</body>
</html>
EOF

systemctl restart httpd
