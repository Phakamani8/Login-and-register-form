<?php

session_start();

header("Content-Type: application/json");

require_once "db.php";

// Receive information from JavaScript
$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";

// Check whether fields are empty
if (empty($email) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter your email and password."
    ]);
    exit;
}

// Search for the user in the database
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check whether the account exists
if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "Account does not exist. Please register first."
    ]);
    exit;
}

// Verify password
if (!password_verify($password, $user["password"])) {
    echo json_encode([
        "success" => false,
        "message" => "Incorrect password."
    ]);
    exit;
}

// Create login session
session_regenerate_id(true);

$_SESSION["user_id"] = $user["id"];
$_SESSION["username"] = $user["username"];

// Successful login
echo json_encode([
    "success" => true,
    "message" => "Welcome back, " . $user["username"] . "!",
    "username" => $user["username"]
]);

?>