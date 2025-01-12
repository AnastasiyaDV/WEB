<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование профиля</title>
    <style>
        body {
            background: url(med_center.jpg);
        }

        input {
            width: 100%;
            height: 25px;
        }

        form {
            text-align: center;
            background-color: rgba(239, 238, 238, 0.82);
            
            display: flex;
            flex-direction: column;
            align-items: center;

            margin-bottom: 20px;
            margin: 10px auto;
            max-width: 400px;
            padding: 10px;
            border-radius: 10px;
        }

    </style>
</head>
<body>
    <?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    $user_id = $_SESSION['user_id'];

    $pdo = new PDO('mysql:host=localhost;dbname=lab9', 'root', 'root');
    $stmt = $pdo->prepare("SELECT full_name, login, date_of_birth FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<p>Ошибка: пользователь не найден.</p>";
        exit;
    }

    $user_full_name = $user['full_name'];
    $user_login = $user['login'];
    $user_dob = $user['date_of_birth'];
    ?>

    <form method="POST" action="update_profile.php">
        <h1>Редактирование профиля</h1>
        <div>
            <label for="full_name"><font size="5">Полное имя</font></label><br>
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user_full_name); ?>" required>
        </div>
        <br>
        <div>
            <label for="login"><font size="5">Логин</font></label><br>
            <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($user_login); ?>" required>
        </div>
        <br>
        <div>
            <label for="dob"><font size="5">Дата рождения</font></label><br>
            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user_dob); ?>" required>
        </div>
        <br>
        <div>
            <label for="password"><font size="5">Новый пароль</font></label><br>
            <input type="password" id="password" name="password" placeholder="Введите новый пароль">
        </div>
        <br>
        <div>
            <label for="confirm_password"><font size="5">Подтверждение пароля</font></label><br>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Повторите новый пароль">
        </div>
        <br>
        <button type="submit" style="padding: 10px 20px; background-color: rgba(66, 155, 24, 0.69); color: white; border: none; border-radius: 4px;">
            Сохранить изменения
        </button>
    </form>

    <form method="POST" style="text-align: center;">
        <button type="submit" name="logout" style="padding: 10px 20px; background-color: rgba(157, 21, 21, 0.77); color: white; border: none; border-radius: 4px;">
            Выйти из аккаунта
        </button>
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php" style="padding: 10px 20px; background-color: rgba(18, 60, 126, 0.72); color: white; text-decoration: none; border-radius: 4px;">Вернуться на главную</a>
    </div>
</body>
</html>
