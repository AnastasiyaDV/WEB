<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <style>
        body {
            background: url(med_center.jpg);
        }

        input {
            width: 80%;
            height: 30px;
        }

        button {
            height: 35px;
            width: 60%;
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
    <form method="POST">
        <h1>Регистрация</h1>
        <label for="fullname">
            <font size="5">Полное имя </font>
        </label><br>
        <input type="text" name="fullname" id="fullname" placeholder="Полное имя" required><br><br>

        <label for="username">
            <font size="5">Логин</font>
        </label><br>
        <input type="text" name="username" id="username" placeholder="Логин" required><br><br>

        <label for="password">
            <font size="5">Пароль</font>
        </label><br>
        <input type="password" name="password" id="password" placeholder="Пароль" required><br><br>

        <label for="dob">
            <font size="5">Дата рождения</font>
        </label><br>
        <input type="date" name="dob" id="dob" required><br><br>

        <button type="submit" style="background-color: rgba(66, 155, 24, 0.5);">Зарегистрироваться</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullname = isset($_POST['fullname']) ? htmlspecialchars(trim($_POST['fullname'])) : '';
        $username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '';
        $password = isset($_POST['password']) ? htmlspecialchars(trim($_POST['password'])) : '';
        $dob = isset($_POST['dob']) ? htmlspecialchars(trim($_POST['dob'])) : '';

        if ($fullname && $username && $password && $dob) {
            try {
                $pdo = new PDO('mysql:host=localhost;dbname=lab9', 'root', 'root', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE login = :username");
                $stmt->execute(['username' => $username]);
                $userExists = $stmt->fetchColumn();

                if ($userExists) {
                    echo "<h3 style='text-align: center; color: red;'>Пользователь с таким логином уже существует!</h3>";
                } else {
                    $pwd_hash = password_hash($password, PASSWORD_DEFAULT);
                    $dob = date('Y-m-d', strtotime($dob));

                    $stmt = $pdo->prepare("INSERT INTO users (full_name, login, password_hash, date_of_birth, is_admin) VALUES (:fullname, :username, :pwd_hash, :dob, 0)");
                    $stmt->execute([
                        'fullname' => $fullname,
                        'username' => $username,
                        'pwd_hash' => $pwd_hash,
                        'dob' => $dob
                    ]);

                    header("Location: index.php");
                    exit();
                }
            } catch (PDOException $e) {
                echo "<h3 style='text-align: center; color: red;'>Ошибка: " . htmlspecialchars($e->getMessage()) . "</h3>";
            }
        } else {
            echo "<h3 style='text-align: center; color: red;'>Заполнены не все поля.</h3>";
        }
    }
    ?>
</body>

</html>