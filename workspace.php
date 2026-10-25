<?php
// workspace.php - Ядро системы
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICOS — Рабочий стол</title>
    <link rel="stylesheet" href="workspace.css">
</head>
<body oncontextmenu="return false;">

    <div id="desktop">
        <div class="icon" data-app="computer">
            <svg viewBox="0 0 24 24"><path d="M21 2H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h7v2H8v2h8v-2h-2v-2h7c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H3V4h18v12z"/></svg>
            <span>Мой компьютер</span>
        </div>
        <div class="icon" data-app="trash">
            <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            <span>Корзина</span>
        </div>
        <div class="icon" data-app="market">
            <svg viewBox="0 0 24 24"><path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3H9c0-1.66 1.34-3 3-3zm7 17H5V8h14v12zm-7-8c-1.66 0-3-1.34-3-3H7c0 2.76 2.24 5 5 5s5-2.24 5-5h-2c0 1.66-1.34 3-3 3z"/></svg>
            <span>ICMarket</span>
        </div>
    </div>

    <div id="windows-layer"></div>

    <div id="start-menu">
        <div class="start-search"><input type="text" id="start-search-input" placeholder="Поиск приложений..."></div>
        <div class="start-apps">
            <div class="app-btn" onclick="OS.openApp('computer'); OS.toggleStart()">
                <svg viewBox="0 0 24 24"><path d="M21 2H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h7v2H8v2h8v-2h-2v-2h7c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H3V4h18v12z"/></svg><span>Компьютер</span>
            </div>
            <div class="app-btn" onclick="OS.openApp('notes'); OS.toggleStart()">
                <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg><span>Заметки</span>
            </div>
            <div class="app-btn" onclick="OS.openApp('cmd'); OS.toggleStart()">
                <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12zm-8-6l-3.5 3.5-1.4-1.4L9.2 12l-2.1-2.1 1.4-1.4L12 12zm2 3h4v-1.5h-4V15z"/></svg><span>Командная строка</span>
            </div>
        </div>
    </div>

    <div id="taskbar">
        <div class="start-btn" id="start-btn">
            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div class="taskbar-apps" id="taskbar-apps"></div>
        <div class="system-tray">
            <div class="tray-icon lang-tray" id="tray-lang">RUS</div>
            <div class="tray-clock" id="tray-clock">
                <span id="tray-time">00:00</span>
                <span id="tray-date">01.01.2024</span>
            </div>
        </div>
    </div>

    <script src="workspace.js"></script>
</body>
</html>