<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "product";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($action == 'delete' && $id) {
        $stmt = $conn->prepare("DELETE FROM product WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pname = $_POST["pname"];
    $pspec = $_POST["pspec"];
    $price = $_POST["price"];
    $pdate = $_POST["pdate"];
    $content = $_POST["content"];

    if (isset($_POST['id']) && $_POST['id'] != '') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE product SET pname=?, pspec=?, price=?, pdate=?, content=? WHERE id=?");
        $stmt->bind_param("ssisii", $pname, $pspec, $price, $pdate, $content, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO product (pname, pspec, price, pdate, content) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $pname, $pspec, $price, $pdate, $content);
    }

    if ($stmt->execute()) {
        echo "<p>儲存成功！</p>";
    } else {
        echo "<p>操作失敗: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_result = $conn->query("SELECT COUNT(*) AS total FROM product");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

$sql = "SELECT * FROM product LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

function getProduct($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    return $product;
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>產品管理</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .pagination a { margin: 0 5px; text-decoration: none; padding: 5px 10px; border: 1px solid #000; }
        .pagination a.active { background-color: #000; color: #fff; }
    </style>
</head>
<body>

<h2>新增/修改產品</h2>
<form method="post">
    <input type="hidden" name="id" value="<?php echo isset($_GET['edit']) ? htmlspecialchars($_GET['edit']) : ''; ?>">
    <label>產品名稱: <input type="text" name="pname" required value="<?php if (isset($_GET['edit'])) { echo htmlspecialchars(getProduct($conn, $_GET['edit'])['pname']); } ?>"></label><br>
    <label>產品規格: <input type="text" name="pspec" required value="<?php if (isset($_GET['edit'])) { echo htmlspecialchars(getProduct($conn, $_GET['edit'])['pspec']); } ?>"></label><br>
    <label>產品定價: <input type="number" name="price" required value="<?php if (isset($_GET['edit'])) { echo htmlspecialchars(getProduct($conn, $_GET['edit'])['price']); } ?>"></label><br>
    <label>製作日期: <input type="date" name="pdate" required value="<?php if (isset($_GET['edit'])) { echo htmlspecialchars(getProduct($conn, $_GET['edit'])['pdate']); } ?>"></label><br>
    <label>內容說明: <textarea name="content" required><?php if (isset($_GET['edit'])) { echo htmlspecialchars(getProduct($conn, $_GET['edit'])['content']); } ?></textarea></label><br>
    <button type="submit">儲存</button>
</form>

<h2>產品列表</h2>
<table>
    <tr>
        <th>ID</th>
        <th>名稱</th>
        <th>規格</th>
        <th>定價</th>
        <th>製作日期</th>
        <th>操作</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["id"]); ?></td>
                <td><?php echo htmlspecialchars($row["pname"]); ?></td>
                <td><?php echo htmlspecialchars($row["pspec"]); ?></td>
                <td><?php echo htmlspecialchars($row["price"]); ?></td>
                <td><?php echo htmlspecialchars($row["pdate"]); ?></td>
                <td>
                    <a href="?id=<?php echo $row['id']; ?>">查看</a>
                    <a href="?edit=<?php echo $row['id']; ?>">編輯</a>
                    <a href="?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('確定刪除?');">刪除</a>
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

<?php
if (isset($_GET['id'])) {
    $product = getProduct($conn, $_GET['id']);
    if ($product) {
        echo "<h2>產品詳細內容</h2>";
        echo "<p>產品名稱: " . htmlspecialchars($product['pname']) . "</p>";
        echo "<p>產品規格: " . htmlspecialchars($product['pspec']) . "</p>";
        echo "<p>產品定價: " . htmlspecialchars($product['price']) . "</p>";
        echo "<p>製作日期: " . htmlspecialchars($product['pdate']) . "</p>";
        echo "<p>內容說明: " . nl2br(htmlspecialchars($product['content'])) . "</p>";
    } else {
        echo "<p>找不到該產品。</p>";
    }
}

$conn->close();
?>

</body>
</html>
