<?php

require 'aws-autoloader.php';

use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

$client = new SecretsManagerClient([
    'region' => 'us-east-1', // Replace with the AWS region where your secret is stored
    'version' => 'latest',
]);


$result = $client->getSecretValue([
    'SecretId' => 'rds!db-b02ffb3a-9f99-44ba-8407-8e247e9f8198',
]);


$myJSON = json_decode($result['SecretString']);

echo $myJSON->username;
echo $myJSON->password;

?>
