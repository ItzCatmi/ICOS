<?php
// reset.php - промежуточный экран после F10, возвращает в BIOS
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Reset</title>
<style>
    html, body {
        height: 100%; margin: 0;
        background: #000; color: #e0e0e0;
        font-family: "Courier New", monospace;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }
</style>
</head>
<body>
<div>Сохранение сброшено. Перезагрузка...</div>
<script>
    setTimeout(function () { window.location.href = "bios.php"; }, 1200);
</script>
</body>
</html>