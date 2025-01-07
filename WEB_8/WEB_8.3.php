<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
</head>
<body>
    <form method="POST" style="text-align: center; margin-bottom: 20px;">
        <h2>Регистрация</h2>
        <label for="fullname">Полное имя:</label><br>
        <input type="text" name="fullname" id="fullname" placeholder="Полное имя" required><br><br>

        <label for="username">Логин:</label><br>
        <input type="text" name="username" id="username" placeholder="Логин" required><br><br>

        <label for="password">Пароль:</label><br>
        <input type="password" name="password" id="password" placeholder="Пароль" required><br><br>

        <label for="dob">Дата рождения:</label><br>
        <input type="date" name="dob" id="dob" required><br><br>

        <button type="submit">Зарегистрироваться</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullname = isset($_POST['fullname']);
        $username = isset($_POST['username']);
        $password = isset($_POST['password']);
        $dob = isset($_POST['dob']);

        if ($fullname && $username && $password && $dob) {
            echo "<h3 style='text-align: center; color: green;'>Вы зарегистрированы!</h3>";
            echo "<p style='text-align: center;'>.</p>";
        } else {
            echo "<h3 style='text-align: center; color: red;'>Заполнены не все поля.</h3>";
        }
    }
    ?>
</body>
</html>
