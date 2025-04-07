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

// 處理刪除資料
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->query("DELETE FROM `movie` WHERE `id` = $delete_id");
    header("Location: movie.php");
    exit();
}

// 設定每頁顯示筆數
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 獲取總筆數
$total_result = $conn->query("SELECT COUNT(*) AS total FROM `movie`");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// 查詢資料（分頁）
$sql = "SELECT * FROM `movie` LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// 新增資料
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_movie'])) {
    $title = $_POST["title"];
    $year = $_POST["year"];
    $director = $_POST["director"];
    $mtype = $_POST["mtype"];
    $mdate = $_POST["mdate"];
    $content = $_POST["content"];

    $stmt = $conn->prepare("INSERT INTO `movie` (`title`, `year`, `director`, `mtype`, `mdate`, `content`) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $title, $year, $director, $mtype, $mdate, $content);

    if ($stmt->execute()) {
        echo "<p>新增成功！</p>";
    } else {
        echo "<p>新增失敗: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// 修改資料
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_movie'])) {
    $id = $_POST['id'];
    $title = $_POST["title"];
    $year = $_POST["year"];
    $director = $_POST["director"];
    $mtype = $_POST["mtype"];
    $mdate = $_POST["mdate"];
    $content = $_POST["content"];

    $stmt = $conn->prepare("UPDATE `movie` SET `title` = ?, `year` = ?, `director` = ?, `mtype` = ?, `mdate` = ?, `content` = ? WHERE `id` = ?");
    $stmt->bind_param("sissssi", $title, $year, $director, $mtype, $mdate, $content, $id);

    if ($stmt->execute()) {
        echo "<p>修改成功！</p>";
    } else {
        echo "<p>修改失敗: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// 查看單筆資料
if (isset($_GET['view'])) {
    $view_id = $_GET['view'];
    $result = $conn->query("SELECT * FROM `movie` WHERE `id` = $view_id");
    $movie = $result->fetch_assoc();
    ?>
    <h2>電影詳細資料</h2>
    <p>電影名稱: <?php echo htmlspecialchars($movie['title']); ?></p>
    <p>發行年份: <?php echo htmlspecialchars($movie['year']); ?></p>
    <p>導演: <?php echo htmlspecialchars($movie['director']); ?></p>
    <p>類型: <?php echo htmlspecialchars($movie['mtype']); ?></p>
    <p>首映日期: <?php echo htmlspecialchars($movie['mdate']); ?></p>
    <p>電影簡介: <?php echo nl2br(htmlspecialchars($movie['content'])); ?></p>
    <a href="movie.php">返回列表</a>
    <?php
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>電影資料管理</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .pagination { margin-top: 20px; }
        .pagination a { padding: 8px 16px; text-decoration: none; color: black; border: 1px solid #ddd; margin: 0 4px; }
        .pagination a.active { background-color: #4CAF50; color: white; }
        .form-container { max-width: 400px; margin: auto; }
        label { display: block; margin: 5px 0; }
        input[type="text"], input[type="number"], input[type="date"], textarea { width: 100%; padding: 8px; margin: 5px 0; }
        button { background-color: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>

<h2>電影資料列表</h2>

<table>
    <tr>
        <th>ID</th>
        <th>電影名稱</th>
        <th>發行年份</th>
        <th>導演</th>
        <th>類型</th>
        <th>操作</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["id"]); ?></td>
                <td><?php echo htmlspecialchars($row["title"]); ?></td>
                <td><?php echo htmlspecialchars($row["year"]); ?></td>
                <td><?php echo htmlspecialchars($row["director"]); ?></td>
                <td><?php echo htmlspecialchars($row["mtype"]); ?></td>
                <td>
                    <a href="movie.php?view=<?php echo $row['id']; ?>">查看</a> |
                    <a href="movie_edit.php?id=<?php echo $row['id']; ?>">修改</a> |
                    <a href="movie.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('確定刪除這筆資料嗎?');">刪除</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6">沒有資料</td></tr>
    <?php endif; ?>
</table>

<div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>

<h2>新增電影資料</h2>
<div class="form-container">
    <form method="POST">
        <label for="title">電影名稱</label>
        <input type="text" name="title" required>
        <label for="year">發行年份</label>
        <input type="number" name="year" required>
        <label for="director">導演</label>
        <input type="text" name="director" required>
        <label for="mtype">類型</label>
        <input type="text" name="mtype" required>
        <label for="mdate">首映日期</label>
        <input type="date" name="mdate" required>
        <label for="content">電影簡介</label>
        <textarea name="content" required></textarea>
        <button type="submit" name="add_movie">新增電影</button>
    </form>
</div>

</body>
</html>

<?php
// 關閉連線
$conn->close();
?>
