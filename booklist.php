<head>
    <title>資料列表</titie>
</head>
<body>
<?php
$servername = "localhost"; // 資料庫主機
$username = "root"; // 資料庫使用者
$password = ""; // 資料庫密碼
$dbname = "school"; // 資料庫名稱

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

// 設定編碼
$conn->set_charset("utf8mb4");

// 處理表單提交
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
    } else {
        echo "<p>新增失敗: " . $stmt->error . "</p>";
    }
    
    $stmt->close();
}

// 設定每頁顯示筆數
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 獲取總筆數
$total_result = $conn->query("SELECT COUNT(*) AS total FROM book");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// 查詢資料（分頁）
$sql = "SELECT id, bookname, author, publisher, pubdate, price, content FROM book LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>書籍列表</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .pagination { margin-top: 20px; }
        .pagination a { margin: 0 5px; text-decoration: none; padding: 5px 10px; border: 1px solid #000; }
        .pagination a.active { background-color: #000; color: #fff; }
    </style>
</head>
<body>
    <h2>新增書籍</h2>
    <form method="post">
        <label>書名: <input type="text" name="bookname" required></label><br>
        <label>作者: <input type="text" name="author" required></label><br>
        <label>出版社: <input type="text" name="publisher" required></label><br>
        <label>出版日期: <input type="date" name="pubdate" required></label><br>
        <label>定價: <input type="number" name="price" required></label><br>
        <label>內容說明: <textarea name="content" required></textarea></label><br>
        <button type="submit">新增</button>
    </form>

    <h2>書籍列表</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>書名</th>
            <th>作者</th>
            <th>出版社</th>
            <th>出版日期</th>
            <th>定價</th>
            <th>內容說明</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["id"]); ?></td>
                    <td><?php echo htmlspecialchars($row["bookname"]); ?></td>
                    <td><?php echo htmlspecialchars($row["author"]); ?></td>
                    <td><?php echo htmlspecialchars($row["publisher"]); ?></td>
                    <td><?php echo htmlspecialchars($row["pubdate"]); ?></td>
                    <td><?php echo htmlspecialchars($row["price"]); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row["content"])); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">沒有資料</td></tr>
        <?php endif; ?>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
</body>
</html>

<?php
// 關閉連線
$conn->close();
?>
