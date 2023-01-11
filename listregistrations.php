<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require __DIR__ . '/classes/Database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

$get_all_visitors = "SELECT * FROM visitors";
$stmt = $conn->prepare($get_all_visitors);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($visitors);
} else {
    echo json_encode(['message' => "No visitors found"]);
}
?>
