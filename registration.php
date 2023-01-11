<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__ . '/classes/Database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();
$availableEventsApiCall = "https://enos.itcollege.ee/~edtoom/billity/clearskyevents.php";
$availableEvents = file_get_contents($availableEventsApiCall);
$availableEvents = json_decode($availableEvents, true);
function msg($success, $status, $message, $extra = [])
{
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ], $extra);
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = strip_tags($data);
    return $data;
}
 $data = json_decode(file_get_contents("php://input"));
 $returnData = [];
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $returnData = msg(0, 404, 'Page Not Found!');
}
elseif (
    !isset($data->name)
    || !isset($data->email)
    || !isset($data->town)
    || !isset($data->dateandtime)
    || empty(trim($data->name))
    || empty(trim($data->email))
    || empty(trim($data->town))
    || empty(trim($data->dateandtime))
){
    $fields = ['fields' => ['name', 'email', 'town', 'dateandtime']];
    $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);
}
else {
    $name = test_input($data->name);
    $email = test_input($data->email);
    $town = test_input($data->town);
    $dateandtime = test_input($data->dateandtime);
    $comment = '';
    if(isset($data->comment) && !empty(trim($data->comment))) {
        $comment = test_input($data->comment);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $returnData = msg(0, 422, 'Invalid Email Address!');
    }
    if(strlen($name) < 3 || strlen($name) > 50){
        $returnData = msg(0, 422, 'Name must be shorter than 50 characters!');
    }
    if(!preg_match('/^[A-za-z\s]+$/', $name)) {
        $returnData = msg(0, 422, 'Name must be letters and spaces only!');
    }
    $eventTimeExists = $eventTownexists = false;

    # Check that event time and town exists
    foreach ($availableEvents as $event) {
        if ($event['Date and Time'] == $dateandtime) {
            $eventTimeExists = true;
        }
        if($event['city'] == $town) {
            $eventTownexists = true;
        }
    }
    if (!$eventTimeExists) {
        $returnData = msg(0, 422, 'Event time does not exist!');
    }
    if(!$eventTownexists) {
        $returnData = msg(0, 422, 'Event town does not exist!');
    }

    # Check that email is not already registered to same event
    $check_email = "SELECT * FROM `visitors` WHERE `email`=:email AND `dateandtime`=:dateandtime AND `town`=:town";
    $check_email_stmt = $conn->prepare($check_email);
    $check_email_stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $check_email_stmt->bindValue(':dateandtime', $dateandtime, PDO::PARAM_STR);
    $check_email_stmt->bindValue(':town', $town, PDO::PARAM_STR);
    $check_email_stmt->execute();
    if ($check_email_stmt->rowCount()) {
        $returnData = msg(0, 422, 'Email already registered for event!');
    }

    # If no errors, insert into database
    if(!isset($returnData['success'])){
        try {
            $insert_query = "INSERT INTO `visitors`(`name`,`email`,`town`,`dateandtime`,`comment`) VALUES(:name,:email,:town,:dateandtime,:comment)";
            $insert_stmt = $conn->prepare($insert_query);
            // DATA BINDING AND CLEANING
            $insert_stmt->bindValue(':name', htmlspecialchars(strip_tags($name)),PDO::PARAM_STR);
            $insert_stmt->bindValue(':email', htmlspecialchars(strip_tags($email)),PDO::PARAM_STR);
            $insert_stmt->bindValue(':town', htmlspecialchars(strip_tags($town)),PDO::PARAM_STR);
            $insert_stmt->bindValue(':dateandtime', htmlspecialchars(strip_tags($dateandtime)),PDO::PARAM_STR);
            $insert_stmt->bindValue(':comment', htmlspecialchars(strip_tags($comment)),PDO::PARAM_STR);
            $insert_stmt->execute();
            $returnData = msg(1, 201, 'You have successfully registered for the event!');
        }
        catch (PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    }

}

echo json_encode($returnData);
?>
