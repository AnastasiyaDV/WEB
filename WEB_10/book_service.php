<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['service_id'])) {
    echo "Ошибка: услуга не выбрана.";
    exit;
}

$service_id = (int)$_GET['service_id'];

$pdo = new PDO('mysql:host=localhost;dbname=lab9', 'root', 'root');

$stmt_service = $pdo->prepare("SELECT name FROM services WHERE id = :service_id");
$stmt_service->execute(['service_id' => $service_id]);
$service = $stmt_service->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    echo "Ошибка: услуга не найдена.";
    exit;
}

$stmt_doctors = $pdo->prepare("SELECT id, full_name FROM doctors WHERE service_id = :service_id");
$stmt_doctors->execute(['service_id' => $service_id]);
$doctors = $stmt_doctors->fetchAll(PDO::FETCH_ASSOC);

$stmt_bookings = $pdo->prepare("
    SELECT doctor_id, DATE_FORMAT(`date`, '%Y-%m-%d %H:%i') as `date` FROM bookings
    WHERE service_id = :service_id AND `date` >= NOW()
");
$stmt_bookings->execute(['service_id' => $service_id]);
$bookedSlots = $stmt_bookings->fetchAll(PDO::FETCH_ASSOC);

$bookedTimes = [];
foreach ($bookedSlots as $slot) {
    $bookedTimes[$slot['doctor_id']][] = $slot['date'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Запись на услугу</title>
    <style>
        body {
            background: url(med_center.jpg);
        }
        form {
            text-align: center;
            background-color: rgba(239, 238, 238, 0.82);
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
            margin: 10px auto;
            max-width: 600px;
            padding: 10px;
            border-radius: 10px;
        }
    </style>
    <script>
        const bookedTimes = <?php echo json_encode($bookedTimes); ?>;

        function filterTimes() {
            const doctorId = document.getElementById("doctor").value;
            const timeSelect = document.getElementById("appointment_time");
            const allTimes = timeSelect.querySelectorAll("option");
            
            allTimes.forEach(option => {
                const date = option.value;
                option.disabled = bookedTimes[doctorId]?.includes(date);
            });
        }

        window.onload = () => {
            document.getElementById("doctor").addEventListener("change", filterTimes);
            filterTimes();
        };
    </script>
</head>
<body>
    <form method="POST" action="submit_booking.php">
        <h1>Запись на услугу: <?php echo htmlspecialchars($service['name']); ?></h1>
        <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
        <div>
            <label for="doctor"><font size="5">Выберите врача:</font></label>
            <select id="doctor" name="doctor_id" required>
                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?php echo $doctor['id']; ?>">
                        <?php echo htmlspecialchars($doctor['full_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <br>
        <div>
            <label for="appointment_time"> <font size="5">Дата и время приема:</font></label>
            <select id="appointment_time" name="appointment_date" required>
                <?php

                $start = strtotime('09:00');
                $end = strtotime('18:00');
                $today = date('Y-m-d');
                while ($start <= $end) {
                    $time = date('H:i', $start);
                    echo "<option value=\"{$today} {$time}\">{$today} {$time}</option>";
                    $start = strtotime('+30 minutes', $start);
                }
                ?>
            </select>
        </div>
        <br>
        <button type="submit" style="padding: 10px 20px; background-color: rgba(66, 155, 24, 0.5); border-radius: 4px;">Записаться</button>
    </form>
    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php" style="padding: 10px 20px; background-color: rgba(18, 60, 126, 0.72); color: white; text-decoration: none; border-radius: 4px;">Вернуться на главную</a>
    </div>
</body>
</html>
