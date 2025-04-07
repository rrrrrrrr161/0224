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

// 設定每頁顯示筆數
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 獲取總筆數
$total_result = $conn->query("SELECT COUNT(*) AS total FROM students");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// 查詢資料（分頁）
$sql = "SELECT id, name, age, class, email FROM students LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>學生列表</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f4f7f6; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #00bcd4; padding: 10px; text-align: left; }
        th { background-color: #00bcd4; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .pagination { margin-top: 20px; text-align: center; }
        .pagination a { margin: 0 5px; text-decoration: none; padding: 5px 10px; border: 1px solid #00bcd4; color: #00bcd4; }
        .pagination a.active { background-color: #00bcd4; color: white; }
        .btn { background-color: #00bcd4; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .btn:hover { background-color: #008c9e; }
    </style>
</head>
<body>
    <h2>學生列表</h2>
    <a href="add_student.php" class="btn">新增學生</a>

    <table>
        <tr>
            <th>ID</th>
            <th>姓名</th>
            <th>年齡</th>
            <th>班級</th>
            <th>電子郵件</th>
            <th>操作</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["id"]); ?></td>
                    <td><?php echo htmlspecialchars($row["name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["age"]); ?></td>
                    <td><?php echo htmlspecialchars($row["class"]); ?></td>
                    <td><?php echo htmlspecialchars($row["email"]); ?></td>
                    <td>
                        <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="btn">編輯</a>
                        <a href="delete_student.php?id=<?php echo $row['id']; ?>" class="btn">刪除</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">沒有資料</td></tr>
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
