<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=lab9', 'root', 'root');

$user_id = $_SESSION['user_id'];
$doctor_id = (int)$_POST['doctor_id'];
$service_id = (int)$_POST['service_id'];
$appointment_date = $_POST['appointment_date'];


$stmt_check = $pdo->prepare("
    SELECT COUNT(*) FROM bookings
    WHERE doctor_id = :doctor_id AND `date` = :appointment_date
");
$stmt_check->execute(['doctor_id' => $doctor_id, 'appointment_date' => $appointment_date]);

if ($stmt_check->fetchColumn() > 0) {
    die("Ошибка: выбранное время уже занято.");
}


$stmt_insert = $pdo->prepare("
    INSERT INTO bookings (user_id, doctor_id, service_id, `date`)
    VALUES (:user_id, :doctor_id, :service_id, :appointment_date)
");
$stmt_insert->execute([
    'user_id' => $user_id,
    'doctor_id' => $doctor_id,
    'service_id' => $service_id,
    'appointment_date' => $appointment_date
]);

header('Location: confirmation.php');
exit;
?>
