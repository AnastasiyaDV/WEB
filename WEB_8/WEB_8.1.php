<!DOCTYPE html>
<html>

<head>
    <title>Таблица умножения</title>
    <style>
        .number {
            display: grid;
            grid-template-columns: repeat(5, 150px);
            grid-template-rows: repeat(2, 250px);
            gap: 5px;
        }
    </style>
</head>

<body>
    <div class="number">
        <?php
        for ($i = 1; $i <= 10; $i++) {
            echo "<div>";
            for ($j = 0; $j <= 10; $j++) {
                echo "$i * $j = " . ($i * $j) . "<br>";
            }
            echo "</div>";
        }

        ?>
    </div>
</body>

</html>