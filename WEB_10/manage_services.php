<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=lab9', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_service') {
    $service_name = trim($_POST['service_name']);
    $service_description = trim($_POST['service_description']);

    if (!empty($service_name) && !empty($service_description)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO services (name, description) VALUES (:name, :description)");
            $stmt->execute([
                'name' => $service_name,
                'description' => $service_description,
            ]);
            echo "<p style='color: green;'>Услуга добавлена успешно!</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Ошибка при добавлении услуги: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Все поля должны быть заполнены.</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'assign_doctor') {
    $doctor_id = intval($_POST['doctor_id']);
    $service_id = intval($_POST['service_id']);

    if ($doctor_id > 0 && $service_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE doctors SET service_id = :service_id WHERE id = :doctor_id");
            $stmt->execute([
                'service_id' => $service_id,
                'doctor_id' => $doctor_id,
            ]);
            echo "<p style='color: green;'>Врач успешно назначен на услугу!</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Ошибка при назначении врача: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Выберите врача и услугу.</p>";
    }
}

try {
    $stmt = $pdo->query("SELECT id, name, description FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка при получении списка услуг: " . $e->getMessage());
}

try {
    $stmt_doctors = $pdo->query("
        SELECT 
            doctors.id AS doctor_id, 
            doctors.full_name, 
            services.name AS service_name 
        FROM doctors 
        LEFT JOIN services ON doctors.service_id = services.id
    ");
    $doctors = $stmt_doctors->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка при получении списка врачей: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление услугами</title>
    <style>
        body {
            margin: 20px;
            background-color:rgba(25, 85, 27, 0.53);
        }
        form, table {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color:rgba(242, 242, 242, 0.86);
        }
        td {
            background-color:rgba(242, 242, 242, 0.65);
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
    <h1 style="text-align:center">Управление услугами</style></h1>

    <form method="POST">
        <input type="hidden" name="action" value="add_service">
        <label for="service_name">Название услуги:</label>
        <input type="text" id="service_name" name="service_name" required>
        <br>
        <label for="service_description">Описание услуги:</label>
        <textarea id="service_description" name="service_description" required></textarea>
        <br>
        <button type="submit">Добавить услугу</button>
    </form>

    <h2>Назначение врача на услугу</h2>
    <form method="POST">
        <input type="hidden" name="action" value="assign_doctor">
        <label for="doctor_id">Врач:</label>
        <select id="doctor_id" name="doctor_id" required>
            <option value="">Выберите врача</option>
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?php echo $doctor['doctor_id']; ?>">
                    <?php echo htmlspecialchars($doctor['full_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        <label for="service_id">Услуга:</label>
        <select id="service_id" name="service_id" required>
            <option value="">Выберите услугу</option>
            <?php foreach ($services as $service): ?>
                <option value="<?php echo $service['id']; ?>">
                    <?php echo htmlspecialchars($service['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        <button type="submit">Назначить врача</button>
    </form>

    <h2>Список услуг</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Описание</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($service['id']); ?></td>
                        <td><?php echo htmlspecialchars($service['name']); ?></td>
                        <td><?php echo htmlspecialchars($service['description']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Услуги отсутствуют.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Список врачей</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Имя врача</th>
                <th>Связанная услуга</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($doctors)): ?>
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doctor['doctor_id']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['service_name'] ?? 'Не назначена'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Врачи отсутствуют.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="book_management.php">Управление записями</a>
    <a href="manage_users.php">Управление пользователями</a>
    <a href="index.php">На главную</a>
</body>
</html>
