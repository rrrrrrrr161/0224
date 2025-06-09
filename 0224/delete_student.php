<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "school";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

$id = $_GET['id'];

$sql = "DELETE FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<p>學生已刪除！</p>";
    header("Location: list.php");
    exit();
} else {
    echo "<p>刪除失敗: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>
