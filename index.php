<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>活動報名表</title>
</head>
<body>
    <ht>活動報名表</h1>
    <form action="">

<fieldset>
    <legend>基本資料</legend>
    <p>
        <libxml for="name">姓名</label>
        <input type="text" name="name" id="name" value="" placeholder="請用中文">
    <p>
    <p>
       <label for="">性別</label>
       <input type="radio" name="gender" id="gender1" value="1">
       <label for="gender1">男生</label>
       <input type="radio" name="gender" id="gender2" value="2">
       <label for="gender2">女生</label>
    </p>
    <p>
    <label for="bday">生日</label>
    <input type="date" name="bday" id="bday" value="<?= date("Y-m-d") ?>">
</p>
<p>
    <label for="bday">電話</label>
    <input type="text" name="phone" id="phone" id="phone">
</p>
    
<p>
<label for="place">居住區域</label>
    <select name="place" id="place">
        <option value="1">北部</option>
        <option value="2">中部</option>
        <option value="3">南部</option>
        <option value="4">東部</option>
        <option value="5">外島</option>
</select>
</p>
</fieldset>

<fieldset>
    <legend>使用行為</legend>
</fieldset>

<fieldset>
    <legend>滿意度</legend>
</fieldset>

<fieldset>
    <legend>資料上傳</legend>
</fieldset>

    
</body>
</html>