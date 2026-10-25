<?php
// start.php - логотип системы + крутящийся спиннер, 3 секунды, потом hi.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>ICOS</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body {
        height: 100%;
        background: #0a0a14;
        color: #e0e0e0;
        font-family: "Segoe UI", Arial, sans-serif;
        overflow: hidden;
    }
    #start {
        height: 100%;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
    }
    /* Логотип системы: квадрат с крестиком, как в BIOS, только большой */
    .logo-big {
        position: relative;
        width: 140px; height: 140px;
        border: 4px solid #4a9eff;
        border-radius: 18px;
        margin-bottom: 28px;
        box-shadow: 0 0 30px rgba(74, 158, 255, 0.4);
    }
    .logo-big::before, .logo-big::after {
        content: ""; position: absolute; top: 50%; left: 50%;
        width: 70%; height: 4px;
        background: #4a9eff;
        border-radius: 2px;
    }
    .logo-big::before { transform: translate(-50%, -50%) rotate(45deg); }
    .logo-big::after  { transform: translate(-50%, -50%) rotate(-45deg); }

    .sys-name {
        font-size: 22px;
        letter-spacing: 4px;
        margin-bottom: 30px;
        color: #c0d8ff;
    }
    /* Спиннер */
    .spinner {
        width: 36px; height: 36px;
        border: 3px solid rgba(74, 158, 255, 0.25);
        border-top-color: #4a9eff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>
<div id="start">
    <div class="logo-big"></div>
    <div class="sys-name">ICOS</div>
    <div class="spinner"></div>
</div>

<script>
    setTimeout(function () {
        window.location.href = "hi.php";
    }, 3000);
</script>
</body>
</html>