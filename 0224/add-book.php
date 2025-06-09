<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "school";

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookname = $_POST["bookname"];
    $author = $_POST["author"];
    $publisher = $_POST["publisher"];
    $pubdate = $_POST["pubdate"];
    $price = $_POST["price"];
    $content = $_POST["content"];

    $stmt = $conn->prepare("INSERT INTO book (bookname, author, publisher, pubdate, price, content) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $bookname, $author, $publisher, $pubdate, $price, $content);

    if ($stmt->execute()) {
        echo "<p>新增成功！</p>";
        echo "<a href='index.php'>返回書籍列表</a>";
    } else {
        echo "<p>新增失敗: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增書籍</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fb; color: #333; }
        label { display: block; margin: 10px 0 5px; }
        input, textarea { width: 100%; padding: 10px; margin: 5px 0 20px; border: 1px solid #ddd; }
        button { background-color: #009688; color: white; border: none; padding: 10px 20px; cursor: pointer; }
        button:hover { background-color: #00796b; }
    </style>
</head>
<body>

<h2>新增書籍</h2>

<form method="post">
    <label for="bookname">書名</label>
    <input type="text" name="bookname" required>

    <label for="author">作者</label>
    <input type="text" name="author" required>

    <label for="publisher">出版社</label>
    <input type="text" name="publisher" required>

    <label for="pubdate">出版日期</label>
    <input type="date" name="pubdate" required>

    <label for="price">定價</label>
    <input type="number" name="price" required>

    <label for="content">內容說明</label>
    <textarea name="content" required></textarea>

    <button type="submit">新增書籍</button>
</form>

</body>
</html>

<?php
$conn->close();
?>
