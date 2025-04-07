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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $age = $_POST["age"];
    $class = $_POST["class"];
    $email = $_POST["email"];

    $stmt = $conn->prepare("UPDATE students SET name = ?, age = ?, class = ?, email = ? WHERE id = ?");
    $stmt->bind_param("siss", $name, $age, $class, $email, $id);

    if ($stmt->execute()) {
        echo "<p>學生資料已更新！</p>";
        header("Location: list.php");
        exit();
    } else {
        echo "<p>更新失敗: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯學生資料</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f4f7f6; color: #333; }
        .form-container { width: 50%; margin: 0 auto; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .form-container h2 { text-align: center; color: #00bcd4; }
        label { display: block; margin: 10px 0 5px; }
        input, select { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #00bcd4; border-radius: 4px; }
        button { background-color: #00bcd4; color: white; padding: 10px 20px; border: none; cursor: pointer; width: 100%; }
        button:hover { background-color: #008c9e; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>編輯學生資料</h2>
    <form method="post">
        <label>姓名:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        
        <label>年齡:</label>
        <input type="number" name="age" value="<?php echo htmlspecialchars($student['age']); ?>" required>
        
        <label>班級:</label>
        <input type="text" name="class" value="<?php echo htmlspecialchars($student['class']); ?>" required>
        
        <label>電子郵件:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        
        <button type="submit">更新學生</button>
    </form>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
