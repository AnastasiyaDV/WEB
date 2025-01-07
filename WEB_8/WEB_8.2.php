<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Календарь</title>
</head>
<body>
    <form method="POST" style="text-align: center; margin-bottom: 20px;">
        <label for="date">Введите месяц и год в формате MM-YYYY:</label>
        <input type="text" name="date" id="date" placeholder="MM-YYYY" pattern="\d{2}-\d{4}">
        <button type="submit">Показать календарь</button>
    </form>
    <div style="display: flex; justify-content: center;">
<?php
function Calendar($month = null, $year = null) {
    if ($month === null || $year === null) {
        $month = date('n');
        $year = date('Y');
    }

    $daysOfWeek = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
    $months = [
        1 => 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
        'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
    ];

    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $firstDayOfMonth = date('N', strtotime("$year-$month-01"));

    $holidays = [
        "$year-01-01" => 'Новый год',
        "$year-01-02" => 'Новый год',
        "$year-01-03" => 'Новый год',
        "$year-01-04" => 'Новый год',
        "$year-01-05" => 'Новый год',
        "$year-01-06" => 'Новый год',
        "$year-01-07" => 'Новый год',
        "$year-01-08" => 'Новый год',
        "$year-02-23" => '23 февраля',
        "$year-03-08" => '8 Марта',
        "$year-05-01" => '1 Мая',
        "$year-05-09" => '9 Мая',
        "$year-06-12" => '12 Июня',
        "$year-11-04" => '4 Ноября',
        
    ];

    echo "<table border='1' cellpadding='7'; text-align: center;'>";
    echo "<caption style='margin-bottom: 10px; font-size: 1.5em;'>{$months[$month]} $year</caption>";

    echo "<tr style='background-color: #f0f0f0;'>";
    foreach ($daysOfWeek as $day) {
        echo "<th style='width: 40px;'>$day</th>";
    }
    echo "</tr>";

    $currentDay = 1;
    echo "<tr>";

    for ($i = 1; $i < $firstDayOfMonth; $i++) {
        echo "<td></td>";
    }

    while ($currentDay <= $daysInMonth) {

        $currentDate = sprintf("%04d-%02d-%02d", $year, $month, $currentDay);

        $isWeekend = date('N', strtotime($currentDate)) >= 6;
        $isHoliday = array_key_exists($currentDate, $holidays);

        $style = '';
        if ($isHoliday || $isWeekend) {
            $style = "background-color: #ffcccc;";
        }

        echo "<td style='$style'>$currentDay</td>";

        if (date('N', strtotime($currentDate)) == 7) {
            echo "</tr><tr>";
        }

        $currentDay++;
    }

    $lastDayOfMonth = date('N', strtotime("$year-$month-$daysInMonth"));
    for ($i = $lastDayOfMonth + 1; $i <= 7; $i++) {
        echo "<td></td>";
    }

    echo "</tr>";
    echo "</table>";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['date']) && !empty($_POST['date'])) {
        if (preg_match('/^(\d{2})-(\d{4})$/', $_POST['date'], $matches)) {
            $month = (int)$matches[1];
            $year = (int)$matches[2];
            Calendar($month, $year);
        } else {
            echo "<p style='text-align: center; color: red;'>Invalid date format. Please use MM-YYYY.</p>";
        }
    }
     else {
        Calendar();
    }
}
// } else {
//     Calendar();
// }
?>
    </div>
</html>