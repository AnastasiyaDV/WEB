<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <style>
        body {
            background: url(med_center.jpg);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-form {
            background-color: rgba(239, 238, 238, 0.82);
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            text-align: center;
        }
        .login-form h2 {
            margin-bottom: 20px;
            font-size: 1.5em;
            color: #333;
        }
        .login-form input {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-form button {
            width: 100%;
            padding: 10px;
            background-color: rgba(66, 155, 24, 0.57);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
        }

        .error {
            color: red;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Авторизация</h2>
        <?php
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login']);
            $password = trim($_POST['password']);
            
            $dsn = 'mysql:host=localhost;dbname=lab9;charset=utf8mb4';
            $dbUser = 'root';
            $dbPass = 'root';

            try {
                $pdo = new PDO($dsn, $dbUser, $dbPass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $pdo->prepare("SELECT id, password_hash, is_admin FROM users WHERE login = :login");
                $stmt->execute(['login' => $login]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['is_admin'] = $user['is_admin'];

                    if ($user['is_admin'] == 1) {
                        header('Location: manage_services.php'); 
                        exit;
                    } else {
                        header('Location: index.php'); 
                        exit;
                    }
                } else {
                    echo "<p class='error'>Неверный логин или пароль.</p>";
                }
            } catch (PDOException $e) {
                echo "<p class='error'>Ошибка подключения к базе данных.</p>";
            }
        }
        ?>
        <form method="POST">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
        <p>Нет аккаунта? <a href="registration.php">Зарегистрироваться</a></p>
    </div>
</body>
</html>
