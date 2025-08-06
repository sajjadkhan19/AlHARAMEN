<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$host = "localhost";
$user = "root";
$pass = "";
$db   = "alharamain";

// Connect to database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// If form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name         = htmlspecialchars($_POST["name"]);
    $phone        = htmlspecialchars($_POST["phone"]);
    $address        = htmlspecialchars($_POST["address"]);
    $cartSummary  = $_POST["cart_summary"];

    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES["screenshot"];
    $fileName = time() . "_" . basename($file["name"]);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO orders (name, number, file_path, cart_summary) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            die("❌ SQL Error: " . $conn->error);
        }

        $stmt->bind_param("ssss", $name, $phone, $address, $fileName, $cartSummary);
        if ($stmt->execute()) {
            echo "<script>
                alert('We will confirm your order after verification of your payment');
                window.location.href = 'index.html';
            </script>";
            exit;
        } else {
            die("❌ Insert failed: " . $stmt->error);
        }
    } else {
        echo "❌ Screenshot upload failed.";
    }
}

$conn->close();
?>


