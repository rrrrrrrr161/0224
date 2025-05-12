<?php
session_start();

function loginOK() {
    return (isset($_SESSION["loggedin"]) && ($_SESSION["loggedin"]===true));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <title>Document</title>
</head>
<body>
    <h1>彈出式對話視窗</h1>

<!-- Button trigger modal -->

<?php if (loginOK()) { ?>
        <?= $_SESSION["username"]; ?>
        <a class="btn btn-success" href="#" id="logout">Logout</a>
<?php } else { ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
            登入管理
        </button>
<?php } ?>




<!-- Modal -->
<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">登入管理</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="#" method="post">
                    <div class="form-floating m-1">
                        <input type="text" class="form-control" name="username" id="username" placeholder="User Name" required="required">
                        <label for="username">User Name</label>
                    </div>
                    <div class="form-floating m-1">
                        <input type="password" class="form-control" name="userpass" id="userpass" placeholder="Password" required="required">
                        <label for="userpass">Password</label>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="login_button">登入系統</button>
                
            </div>

        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 透過 CDN 載入 jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {

    // 執行登入認證
    $('#login_button').click(function () {

        // 取出登入表單中，使用者帳號密碼的輸入值
        var username = $('#username').val();
        var userpass = $('#userpass').val();

        // alert("username"+username+ " userpass"+userpass);

        if (username != '' && userpass != '') {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: {
                    "action": "login",
                    "username": username,
                    "userpass": userpass
                },

                success: function (data) {
                    if (data == 'Yes') {
                        location.reload();
                        alert("成功登入系統...");
                    } else {
                        // location.reload();
                        alert('帳密無法使用!');
                    }
                },

                error: function (data) {
                    alter('無法登入');
                }
            });
        } else {
            alert("兩個欄位都要填寫!");
        }
    });

    // 執行登出
    $('#logout').click(function () {
        $.ajax({
            url: "action.php",
            method: "POST",
            data: {
                "action": "logout",
            },
            success: function () {
                location.reload();
                alert("您已登出本系統...");
            }
        });
    });
});
</script>

</body>
</html>