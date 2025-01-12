<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=lab9', 'root', 'root');

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = :id");
    $stmt->execute(['id' => $delete_id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $doctor_id = intval($_POST['doctor_id']);
    $service_id = intval($_POST['service_id']);
    $date = $_POST['date'];

    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, doctor_id, service_id, date) 
                           VALUES (:user_id, :doctor_id, :service_id, :date)");
    $stmt->execute([
        'user_id' => $user_id,
        'doctor_id' => $doctor_id,
        'service_id' => $service_id,
        'date' => $date
    ]);
}

$stmt = $pdo->query("SELECT bookings.id, users.full_name AS user_name, doctors.full_name AS doctor_name, 
                            services.name AS service_name, bookings.date 
                     FROM bookings
                     JOIN users ON bookings.user_id = users.id
                     JOIN doctors ON bookings.doctor_id = doctors.id
                     JOIN services ON bookings.service_id = services.id");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$users = $pdo->query("SELECT id, full_name FROM users")->fetchAll(PDO::FETCH_ASSOC);
$services = $pdo->query("SELECT id, name FROM services")->fetchAll(PDO::FETCH_ASSOC);
$doctors = $pdo->query("SELECT id, full_name FROM doctors")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление записями</title>
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
            background-color: rgba(46, 39, 122, 0.68);;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: rgba(10, 76, 7, 0.68);
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center">Управление записями</style></h1>
    <form method="POST">
        <label>Пользователь:</label>
        <select name="user_id" required>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['full_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label>Услуга:</label>
        <select name="service_id" required>
            <?php foreach ($services as $service): ?>
                <option value="<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label>Врач:</label>
        <select name="doctor_id" required>
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?php echo $doctor['id']; ?>"><?php echo htmlspecialchars($doctor['full_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label>Дата:</label>
        <input type="datetime-local" name="date" required>
        <br>
        <button type="submit">Добавить запись</button>
    </form>

    <h2>Список записей</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Пользователь</th>
            <th>Врач</th>
            <th>Услуга</th>
            <th>Дата</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?php echo $booking['id']; ?></td>
                <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['date']); ?></td>
                <td>
                    <a href="?delete_id=<?php echo $booking['id']; ?>">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="index.php">На главную</a>
    <a href="manage_users.php">Управление пользователями</a>
    <a href="manage_services.php">Управление услугами</a>
</body>
</html>
