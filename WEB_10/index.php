<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = false;

if ($isLoggedIn) {

    $dsn = 'mysql:host=localhost;dbname=lab9;charset=utf8mb4';
    $dbUser = 'root';
    $dbPass = 'root';
    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $user['is_admin'] == 1) {
            $isAdmin = true;
        }
    } catch (PDOException $e) {
        die("Ошибка подключения к базе данных: " . $e->getMessage());
    }
}

$dsn = 'mysql:host=localhost;dbname=lab9;charset=utf8mb4';
$dbUser = 'root';
$dbPass = 'root';
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Медицинский центр</title>
    <style>
        body { background: url(med_center.jpg); }
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
        .service-card {
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .service-card h3 {
            margin: 0;
            font-size: 1.2em;
            color: #333;
        }
        .service-card p {
            margin: 10px 0;
            color: #555;
        }
        .service-card a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: rgba(66, 155, 24, 0.69);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .service-card a:hover {
            background-color: rgba(66, 155, 24, 0.8);
        }
        .header-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .header-links .left-links {
            display: flex;
            gap: 15px;
        }
        .header-links a {
            padding: 10px 15px;
            background-color: rgba(66, 155, 24, 0.69);
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .header-links a:hover {
            background-color: rgba(66, 155, 24, 0.8);
        }
        .admin-links {
            display: flex;
            gap: 10px;
        }
        .admin-links a {
            background-color: rgba(35, 95, 148, 0.67);
        }
        .admin-links a:hover {
            background-color: #285e8e;
        }
    </style>
</head>
<body>
    <header style="padding: 10px; background-color: rgba(239, 238, 238, 0.84);">
        <div class="header-links">
            <div class="left-links">
                <?php if ($isLoggedIn): ?>
                    <?php if ($isAdmin): ?>
                        <div class="admin-links">
                            <a href="manage_services.php">Управление услугами</a>
                            <a href="manage_users.php">Управление пользователями</a>
                            <a href="Book_management.php">Управление записями</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="avtorisation.php">Войти</a>
                    <a href="registration.php">Регистрация</a>
                <?php endif; ?>
            </div>
            <?php if ($isLoggedIn): ?>
                <div style="display: flex; gap: 10px;">
                    <a href="profile.php">Личный кабинет</a>
                    <?php if (!$isAdmin): ?>
                        <a href="user_bookings.php">Мои записи</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <h2 style="text-align: center; font-size: 2.5em; background-color: rgba(50, 143, 22, 0.5); padding: 10px; border: 3px solid #3e6e12; border-radius: 10px; margin: 20px 0; width: 55%; margin-left: auto; margin-right: auto;">
            Наши услуги
        </h2>
        <div class="services-container">
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                    <?php if ($isLoggedIn): ?>
                        <a href="book_service.php?service_id=<?php echo $service['id']; ?>">Записаться</a>
                    <?php else: ?>
                        <a href="registration.php" onclick="alert('Для записи на услугу необходимо авторизоваться на сайте.')">Записаться</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
