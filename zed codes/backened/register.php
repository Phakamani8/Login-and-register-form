<?php

header("Content-Type: application/json");

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["message" => "Invalid request method."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$full_name = trim($data["full_name"] ?? "");
$username = trim($data["username"] ?? "");
$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";

if (!$full_name || !$username || !$email || !$password) {
    http_response_code(400);
    echo json_encode(["message" => "All fields are required."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid email address."]);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(["message" => "Password must be at least 8 characters."]);
    exit;
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {

    $sql = "INSERT INTO users
            (full_name, username, email, password)
            VALUES (:full_name, :username, :email, :password)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ":full_name" => $full_name,
        ":username" => $username,
        ":email" => $email,
        ":password" => $hashed_password
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Registration successful!"
    ]);

} catch (PDOException $e) {

    if ($e->getCode() == "23000") {

        http_response_code(409);

        echo json_encode([
            "success" => false,
            "message" => "Username or email already exists."
        ]);

    } else {

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Registration failed."
        ]);

    }

}

?>