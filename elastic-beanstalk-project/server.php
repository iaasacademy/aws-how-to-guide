<?php

// session_start();

// Include the aws php sdk
// Follow the below instructions for deep dive
// https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_installation.html
require 'vendor/aws-autoloader.php';

// Import SecretsManagerClient and AwsException to be used
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

// Create a SecretsManagerClient object
$client = new SecretsManagerClient([
    'region' => 'us-east-1',
    'version' => 'latest',
]);


// Get the Secret value from AWS Secret Manager
// Note: Ensure proper IAM role is attached to EC2 instance
$result = $client->getSecretValue([
    'SecretId' => $_ENV["SECRET_NAME"],
]);


// result contains a few key-value about that secret.
// The key named SecretString has the username and password encoded as a plain json string
// Decode the json using json_decode function to get username and password
$myJSON = json_decode($result['SecretString']);

define('DB_SERVER', $_ENV["DB_ENDPOINT"]);

define('DB_USERNAME', $myJSON->username);

define('DB_PASSWORD', $myJSON->password);

define('DB_DATABASE', $_ENV["DB_NAME"]);


$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$table = "user";

function isTableExists($connection, $tableName, $db_name)
{
    $t = mysqli_real_escape_string($connection, $tableName);
    $d = mysqli_real_escape_string($connection, $db_name);

    $checktable = mysqli_query(
        $connection,
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'"
    );

    if (mysqli_num_rows($checktable) > 0) {
        return true;
    }

    return false;
}

if (!isTableExists($db, $table, DB_DATABASE)) {
    $query = "CREATE TABLE $table (id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, task_type VARCHAR(50), task_description VARCHAR(50), statues VARCHAR(100), due_date TEXT,  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP);";
    if (!mysqli_query($db, $query)) {
        echo json_encode([
            "message" => " Error on create table!",
            "status" => "error",
            "sql_error" => mysqli_error($db),
        ]);
        die();
    }
}

$task_type = "";
$task_description = "";
$statues = "";
$due_date = "";
$id = 0;
$update = false;

if (isset($_GET['type'])) {

    $type = $_GET['type'];

} else {

    $type = "22";

}

//$type="";
// POST Add new Todo
//if (isset($_POST['save'])) {
$type = isset($_POST['type']) ? $_POST['type'] : '';
//    if(count($_POST)>0){
//       if($_POST['type']==1){
//      $task_type = $_POST['task_type'];
//      $task_description = $_POST['task_description'];
//      $statues = $_POST['statues'];
//      $due_date = $_POST['due_date'];
//      mysqli_query($db, "INSERT INTO user (task_type,task_description,due_date,statues) VALUES ('$task_type', '$task_description','$due_date','$statues')");
//      $_SESSION['alert'] = "To Do saved!";
//      //header("location: index.php");
//       }
//  }

if (count($_POST) > 0) {
    if ($type == 1) {
        //    if (isset($_POST['save'])) {
        // var_dump('hai');
        $task_type = $_POST['task_type'];
        $task_description = $_POST['task_description'];
        $statues = $_POST['statues'];
        $due_date = $_POST['due_date'];
        $sql = "INSERT INTO `user`( `task_type`, `task_description`,`due_date`,`statues`)
         VALUES ('$task_type','$task_description','$due_date','$statues')";
        if (mysqli_query($db, $sql)) {
            echo json_encode(array("statusCode" => 200));
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($db);
        }
        mysqli_close($db);
    }
}

// POST Todo Update data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $task_type = $_POST['task_type'];
    $task_description = $_POST['task_description'];
    $due_date = $_POST['due_date'];
    $statues = $_POST['statues'];
    mysqli_query($db, "UPDATE user SET task_type='$task_type',due_date='$due_date',task_description='$task_description' WHERE id=$id");
    $_SESSION['alert'] = "Data updated!";
    header("location: index.php");
}

// GET  Delete Todo data
if (isset($_GET['del'])) {
    $id = $_GET['del'];

    mysqli_query($db, "DELETE FROM user WHERE id=$id");
    $_SESSION['alert'] = "Data deleted!";
    header("location: index.php");
}

//Todo status change
if (isset($_POST['ids'])) {
    $ids = $_POST['ids'];
    $statues1 = $_POST['statues1'];
    $query = "UPDATE user SET statues='$statues1' WHERE id='$ids'";
    //header("location: index.php");
    if ($db->query($query) === true) {
        //  echo "DATA updated";
        echo json_encode('Your To Do List Status Updated Successfully');
        //  header("location: index.php");
    }

    $result = mysqli_query($db, $query);
}


