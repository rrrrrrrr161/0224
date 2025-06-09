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

// 刪除資料
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->query("DELETE FROM book WHERE id = $delete_id");
    header("Location: index.php");
    exit();
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
        body { font-family: Arial, sans-serif; background-color: #f4f7fb; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #009688; color: white; }
        .pagination { margin-top: 20px; }
        .pagination a { margin: 0 5px; text-decoration: none; padding: 5px 10px; border: 1px solid #ddd; }
        .pagination a.active { background-color: #009688; color: white; }
        .action-links a { color: #009688; text-decoration: none; margin-right: 10px; }
        .action-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

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
        <th>操作</th>
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
                <td class="action-links">
                    <a href="view.php?id=<?php echo $row['id']; ?>">查看</a> |
                    <a href="edit.php?id=<?php echo $row['id']; ?>">修改</a> |
                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('確定刪除這筆資料嗎?');">刪除</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8">沒有資料</td></tr>
    <?php endif; ?>
</table>

<div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>

<br>
<a href="add.php">新增書籍</a>

</body>
</html>

<?php
$conn->close();
?>
