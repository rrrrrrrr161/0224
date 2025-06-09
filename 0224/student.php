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

// 新增資料
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $schid = $_POST["schid"];
    $name = $_POST["name"];
    $gender = $_POST["gender"];
    $birthday = $_POST["birthday"];
    $email = $_POST["email"];
    $address = $_POST["address"];

    $stmt = $conn->prepare("INSERT INTO student (schid, name, gender, birthday, email, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $schid, $name, $gender, $birthday, $email, $address);
    
    if ($stmt->execute()) {
        echo "<p>新增成功！</p>";
    } else {
        echo "<p>新增失敗: " . $stmt->error . "</p>";
    }
    
    $stmt->close();
}

// 修改資料
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $id = $_POST["id"];
    $schid = $_POST["schid"];
    $name = $_POST["name"];
    $gender = $_POST["gender"];
    $birthday = $_POST["birthday"];
    $email = $_POST["email"];
    $address = $_POST["address"];

    $stmt = $conn->prepare("UPDATE student SET schid=?, name=?, gender=?, birthday=?, email=?, address=? WHERE id=?");
    $stmt->bind_param("ssssssi", $schid, $name, $gender, $birthday, $email, $address, $id);

    if ($stmt->execute()) {
        echo "<p>修改成功！</p>";
    } else {
        echo "<p>修改失敗: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// 刪除資料
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM student WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<p>刪除成功！</p>";
    } else {
        echo "<p>刪除失敗: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// 分頁設定
$limit = 10; // 每頁顯示筆數
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 獲取總筆數
$total_result = $conn->query("SELECT COUNT(*) AS total FROM student");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// 查詢資料（分頁）
$sql = "SELECT id, schid, name, gender, birthday, email, address FROM student LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// 查看單筆資料
if (isset($_GET['view'])) {
    $id = $_GET['view'];
    $stmt = $conn->prepare("SELECT * FROM student WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $student_result = $stmt->get_result();
    $student = $student_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>學生資料管理</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .pagination { margin-top: 20px; }
        .pagination a { margin: 0 5px; text-decoration: none; padding: 5px 10px; border: 1px solid #000; }
        .pagination a.active { background-color: #000; color: #fff; }
        form { margin-top: 20px; }
        input[type="text"], input[type="email"], input[type="date"], select, textarea { width: 100%; padding: 8px; margin: 5px 0; }
        button { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h2>學生資料列表</h2>
    <table>
        <tr>
            <th>學號</th>
            <th>姓名</th>
            <th>性別</th>
            <th>生日</th>
            <th>電子郵件</th>
            <th>住址</th>
            <th>操作</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["schid"]); ?></td>
                    <td><?php echo htmlspecialchars($row["name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["gender"]); ?></td>
                    <td><?php echo htmlspecialchars($row["birthday"]); ?></td>
                    <td><?php echo htmlspecialchars($row["email"]); ?></td>
                    <td><?php echo htmlspecialchars($row["address"]); ?></td>
                    <td>
                        <a href="?view=<?php echo $row['id']; ?>">查看</a> | 
                        <a href="?edit=<?php echo $row['id']; ?>">修改</a> | 
                        <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</a>
                    </td>
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

    <?php if (isset($_GET['view']) && $student): ?>
        <h2>查看學生資料</h2>
        <table>
            <tr><th>學號</th><td><?php echo htmlspecialchars($student["schid"]); ?></td></tr>
            <tr><th>姓名</th><td><?php echo htmlspecialchars($student["name"]); ?></td></tr>
            <tr><th>性別</th><td><?php echo htmlspecialchars($student["gender"]); ?></td></tr>
            <tr><th>生日</th><td><?php echo htmlspecialchars($student["birthday"]); ?></td></tr>
            <tr><th>電子郵件</th><td><?php echo htmlspecialchars($student["email"]); ?></td></tr>
            <tr><th>住址</th><td><?php echo htmlspecialchars($student["address"]); ?></td></tr>
        </table>
    <?php endif; ?>

    <?php if (isset($_GET['edit'])): ?>
        <?php 
            $id = $_GET['edit'];
            $stmt = $conn->prepare("SELECT * FROM student WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $edit_result = $stmt->get_result();
            $edit_student = $edit_result->fetch_assoc();
        ?>
        <h2>修改學生資料</h2>
        <form method="post">
            <input type="hidden" name="id" value="<?php echo $edit_student['id']; ?>">
            <label>學號: <input type="text" name="schid" value="<?php echo htmlspecialchars($edit_student['schid']); ?>" required></label><br>
            <label>姓名: <input type="text" name="name" value="<?php echo htmlspecialchars($edit_student['name']); ?>" required></label><br>
            <label>性別: 
                <select name="gender" required>
                    <option value="M" <?php echo ($edit_student['gender'] == 'M') ? 'selected' : ''; ?>>男</option>
                    <option value="F" <?php echo ($edit_student['gender'] == 'F') ? 'selected' : ''; ?>>女</option>
                </select>
            </label><br>
            <label>生日: <input type="date" name="birthday" value="<?php echo htmlspecialchars($edit_student['birthday']); ?>" required></label><br>
            <label>電子郵件: <input type="email" name="email" value="<?php echo htmlspecialchars($edit_student['email']); ?>" required></label><br>
            <label>住址: <input type="text" name="address" value="<?php echo htmlspecialchars($edit_student['address']); ?>" required></label><br>
            <button type="submit" name="edit">更新資料</button>
        </form>
    <?php endif; ?>

    <h2>新增學生資料</h2>
    <form method="post">
        <label>學號: <input type="text" name="schid" required></label><br>
        <label>姓名: <input type="text" name="name" required></label><br>
        <label>性別: 
            <select name="gender" required>
                <option value="M">男</option>
                <option value="F">女</option>
            </select>
        </label><br>
        <label>生日: <input type="date" name="birthday" required></label><br>
        <label>電子郵件: <input type="email" name="email" required></label><br>
        <label>住址: <input type="text" name="address" required></label><br>
        <button type="submit" name="add">新增資料</button>
    </form>

</body>
</html>

<?php
// 關閉連線
$conn->close();
?>
