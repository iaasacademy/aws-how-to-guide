Amazon S3 CORS Configuration - YouTube Tutorial

This repository contains the source code and configuration files for the YouTube video tutorial on configuring an Amazon S3 bucket with Cross-Origin Resource Sharing (CORS). The tutorial demonstrates how to enable CORS on an S3 bucket and deploy a simple static website that interacts with an external JavaScript file hosted in another S3 bucket.

Repository Contents

Root Directory - Contains the source code files for a basic static website (HTML, CSS, Images, etc.).

javascriptbucket/ - Contains:

cors-config.json - CORS configuration file for the second S3 bucket.

countdown.js - JavaScript file hosted in the second S3 bucket.

A modified version of index.html that performs a fetch operation to request the JavaScript file.

README.md - This file, providing an overview of the repository and instructions for setup.

Prerequisites

Before using the files in this repository, ensure you have:

An AWS account

Two S3 buckets:

One for hosting the main website.

One for hosting the JavaScript file (CORS-enabled).

AWS CLI installed (optional, but helpful for configuring S3).

Watch the YouTube Tutorial

Watch the full step-by-step tutorial on our YouTube channel:


Contributing

Feel free to fork this repository, submit pull requests, or report issues to improve the content.

License

This repository is licensed under the MIT License. See the LICENSE file for more details.
