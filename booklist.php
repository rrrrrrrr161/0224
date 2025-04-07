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

// 設定編碼
$conn->set_charset("utf8mb4");

// 新增資料處理
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $bookname = $_POST['bookname'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $pubdate = $_POST['pubdate'];
    $price = $_POST['price'];
    $content = $_POST['content'];

    $sql = "INSERT INTO book (bookname, author, publisher, pubdate, price, content) 
            VALUES ('$bookname', '$author', '$publisher', '$pubdate', '$price', '$content')";
    
    if ($conn->query($sql) === TRUE) {
        echo "新增成功！<br>";
    } else {
        echo "錯誤: " . $conn->error;
    }
}

// 刪除資料處理
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM book WHERE id = $delete_id";
    if ($conn->query($delete_sql) === TRUE) {
        echo "<p>刪除成功！</p>";
    } else {
        echo "<p>刪除失敗: " . $conn->error . "</p>";
    }
}

// 編輯資料處理
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
        $bookname = $_POST['bookname'];
        $author = $_POST['author'];
        $publisher = $_POST['publisher'];
        $pubdate = $_POST['pubdate'];
        $price = $_POST['price'];
        $content = $_POST['content'];

        $sql = "UPDATE book SET bookname='$bookname', author='$author', publisher='$publisher', 
                pubdate='$pubdate', price='$price', content='$content' WHERE id=$edit_id";
        if ($conn->query($sql) === TRUE) {
            echo "<p>修改成功！</p>";
        } else {
            echo "<p>修改失敗: " . $conn->error . "</p>";
        }
    } else {
        $result = $conn->query("SELECT * FROM book WHERE id = $edit_id");
        $row = $result->fetch_assoc();
    }
}

// 查看單筆資料處理
if (isset($_GET['view_id'])) {
    $view_id = $_GET['view_id'];
    $result = $conn->query("SELECT * FROM book WHERE id = $view_id");
    $book = $result->fetch_assoc();
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
$sql = "SELECT * FROM book LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>書籍管理系統</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .pagination { margin-top: 20px; }
        .pagination a { margin: 0 5px; text-decoration: none; padding: 5px 10px; border: 1px solid #000; }
        .pagination a.active { background-color: #000; color: #fff; }
        .form-container { background-color: #f4f4f4; padding: 20px; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .form-container label { display: block; margin: 10px 0 5px; }
        .form-container input, .form-container textarea { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; }
    </style>
</head>
<body>

<h2>書籍管理系統</h2>

<!-- 新增書籍 -->
<h3>新增書籍</h3>
<div class="form-container">
    <form method="POST">
        <input type="hidden" name="add" value="1">
        <label>書名: <input type="text" name="bookname" required></label><br>
        <label>作者: <input type="text" name="author" required></label><br>
        <label>出版社: <input type="text" name="publisher" required></label><br>
        <label>出版日期: <input type="date" name="pubdate" required></label><br>
        <label>定價: <input type="number" name="price" required></label><br>
        <label>內容說明: <textarea name="content" required></textarea></label><br>
        <button type="submit">新增</button>
    </form>
</div>

<!-- 顯示書籍列表 -->
<h3>書籍列表</h3>
<table>
    <tr>
        <th>ID</th>
        <th>書名</th>
        <th>作者</th>
        <th>出版社</th>
        <th>出版日期</th>
        <th>定價</th>
        <th>內容說明</th>
        <th>操作</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["id"]); ?></td>
                <td><a href="?view_id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row["bookname"]); ?></a></td>
                <td><?php echo htmlspecialchars($row["author"]); ?></td>
                <td><?php echo htmlspecialchars($row["publisher"]); ?></td>
                <td><?php echo htmlspecialchars($row["pubdate"]); ?></td>
                <td><?php echo htmlspecialchars($row["price"]); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row["content"])); ?></td>
                <td>
                    <a href="?edit_id=<?php echo $row['id']; ?>">編輯</a> | 
                    <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('確定要刪除嗎？')">刪除</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8">沒有資料</td></tr>
    <?php endif; ?>
</table>

<!-- 分頁 -->
<div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>

<!-- 顯示單筆書籍詳細資料 -->
<?php if (isset($book)): ?>
    <h3>書籍詳細資料</h3>
    <div class="form-container">
        <p><strong>書名:</strong> <?php echo htmlspecialchars($book['bookname']); ?></p>
        <p><strong>作者:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
        <p><strong>出版社:</strong> <?php echo htmlspecialchars($book['publisher']); ?></p>
        <p><strong>出版日期:</strong> <?php echo htmlspecialchars($book['pubdate']); ?></p>
        <p><strong>定價:</strong> <?php echo htmlspecialchars($book['price']); ?></p>
        <p><strong>內容說明:</strong><br><?php echo nl2br(htmlspecialchars($book['content'])); ?></p>
    </div>
<?php endif; ?>

</body>
</html>

<?php
// 關閉連線
$conn->close();
?>
