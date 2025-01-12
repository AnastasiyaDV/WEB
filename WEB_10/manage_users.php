<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=lab9', 'root', 'root');

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $delete_id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $login = trim($_POST['login']);
    $date_of_birth = $_POST['date_of_birth'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (isset($_POST['id']) && $_POST['id'] != '') {

        $id = intval($_POST['id']);
        $stmt = $pdo->prepare("UPDATE users 
                               SET full_name = :full_name, login = :login, date_of_birth = :date_of_birth, is_admin = :is_admin 
                               WHERE id = :id");
        $stmt->execute([
            'full_name' => $full_name,
            'login' => $login,
            'date_of_birth' => $date_of_birth,
            'is_admin' => $is_admin,
            'id' => $id,
        ]);
    } else {

        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, login, password_hash, date_of_birth, is_admin) 
                               VALUES (:full_name, :login, :password_hash, :date_of_birth, :is_admin)");
        $stmt->execute([
            'full_name' => $full_name,
            'login' => $login,
            'password_hash' => $password,
            'date_of_birth' => $date_of_birth,
            'is_admin' => $is_admin,
        ]);
    }
}

$stmt = $pdo->query("SELECT id, full_name, login, date_of_birth, is_admin FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями</title>
    <style>
        body {
            background-color:rgba(25, 85, 27, 0.53);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color:rgba(242, 242, 242, 0.86);
        }
        td {
            background-color:rgba(242, 242, 242, 0.65);
        }
        form {
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            background-color:rgba(46, 39, 122, 0.68);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        a {
            text-decoration: none;
            padding: 10px 20px;
            background-color:rgba(10, 76, 7, 0.68);
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center">Управление пользователями</style></h1>

    <form method="POST">
        <h2>Добавить или редактировать пользователя</h2>
        <input type="hidden" name="id" id="id">
        <label>Полное имя:</label>
        <input type="text" name="full_name" id="full_name" required>
        <br>
        <label>Логин:</label>
        <input type="text" name="login" id="login" required>
        <br>
        <label>Дата рождения:</label>
        <input type="date" name="date_of_birth" id="date_of_birth" required>
        <br>
        <label>Пароль:</label>
        <input type="password" name="password" id="password">
        <small>* Необязательно для редактирования</small>
        <br>
        <label>
            <input type="checkbox" name="is_admin" id="is_admin"> Администратор
        </label>
        <br>
        <button type="submit">Сохранить</button>
    </form>

    <h2>Список пользователей</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Полное имя</th>
            <th>Логин</th>
            <th>Дата рождения</th>
            <th>Админ</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['login']); ?></td>
                <td><?php echo htmlspecialchars($user['date_of_birth']); ?></td>
                <td><?php echo $user['is_admin'] ? 'Да' : 'Нет'; ?></td>
                <td>
                    <button onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name']); ?>', '<?php echo htmlspecialchars($user['login']); ?>', '<?php echo htmlspecialchars($user['date_of_birth']); ?>', <?php echo $user['is_admin']; ?>)">
                        Редактировать
                    </button>
                    <a href="?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Вы уверены?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="index.php">На главную</a>
    <a href="Book_management.php">Управление записями</a>
    <a href="manage_services.php">Управление услугами</a>

    <script>
        function editUser(id, fullName, login, dateOfBirth, isAdmin) {
            document.getElementById('id').value = id;
            document.getElementById('full_name').value = fullName;
            document.getElementById('login').value = login;
            document.getElementById('date_of_birth').value = dateOfBirth;
            document.getElementById('is_admin').checked = isAdmin;
            document.getElementById('password').value = '';
        }
    </script>
</body>
</html>
