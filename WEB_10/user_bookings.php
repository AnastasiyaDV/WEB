<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: avtorisation.php");
    exit;
}

$dsn = 'mysql:host=localhost;dbname=lab9;charset=utf8mb4';
$dbUser = 'root';
$dbPass = 'root';

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT b.id, s.name AS service_name, d.full_name AS doctor_name, b.date
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN doctors d ON b.doctor_id = d.id
        WHERE b.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$deleteId]);
        header("Location: user_bookings.php");
        exit;
    } catch (PDOException $e) {
        die("Ошибка удаления записи: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои записи</title>
    <style>
        body {
            background: url(med_center.jpg);
        }

        .services-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 20px auto;
            max-width: 800px;
            padding: 20px;
            background-color: rgba(239, 238, 238, 0.82);
            border-radius: 10px;
        }

        form,
        .bookings {
            text-align: center;
            background-color: rgba(239, 238, 238, 0.82);

            display: flex;
            flex-direction: column;
            align-items: center;

            margin-bottom: 20px;
            margin: 10px auto;
            max-width: 800px;
            padding: 10px;
            border-radius: 10px;
        }

        a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: rgba(10, 76, 7, 0.68);
            color: white;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        button {
            background-color: rgba(198, 46, 41, 0.69);
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <header>
        <a href="index.php">Главная</a>
        <a href="profile.php">Личный кабинет</a>
    </header>

    <main>

        <?php if (empty($bookings)): ?>
            <p>У вас пока нет записей.</p>
        <?php else: ?>
            <div class="bookings">
                <h2>Мои записи</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Услуга</th>
                            <th>Доктор</th>
                            <th>Дата</th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['doctor_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['date']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="delete_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>