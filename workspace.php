<?php require_once 'icons.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="ico.php" type="image/x-icon">
<title>ICOS — Рабочий стол</title>
<link rel="stylesheet" href="workspace.css?v=2">
</head>
<body>

    <!-- ===== Обои (как Windows 11 "Bloom") ===== -->
    <div id="wallpaper"><div id="wallpaper-img"></div></div>

    <!-- ===== Рабочий стол ===== -->
    <div id="desktop">
        <div class="icon" data-app="computer">
            <span class="icon-img"><?= ic_svg('explorer') ?></span>
            <span class="icon-label">Проводник</span>
        </div>
        <div class="icon" data-app="pc">
            <span class="icon-img"><?= ic_svg('computer') ?></span>
            <span class="icon-label">Этот компьютер</span>
        </div>
        <div class="icon" data-app="trash">
            <span class="icon-img" id="trash-icon"><?= ic_svg('trash') ?></span>
            <span class="icon-label">Корзина</span>
        </div>
        <div class="icon" data-app="edge">
            <span class="icon-img"><?= ic_svg('edge') ?></span>
            <span class="icon-label">IC Browser</span>
        </div>
        <div class="icon" data-app="notes">
            <span class="icon-img"><?= ic_svg('notes') ?></span>
            <span class="icon-label">Заметки</span>
        </div>
        <div class="icon" data-app="cmd">
            <span class="icon-img"><?= ic_svg('terminal') ?></span>
            <span class="icon-label">Терминал</span>
        </div>
        <div class="icon" data-app="market">
            <span class="icon-img"><?= ic_svg('market') ?></span>
            <span class="icon-label">ICMarket</span>
        </div>
        <div class="icon" data-app="settings">
            <span class="icon-img"><?= ic_svg('settings') ?></span>
            <span class="icon-label">Параметры</span>
        </div>
    </div>

    <div id="windows-layer"></div>

    <!-- ===== Меню Пуск (Windows 11) ===== -->
    <div id="start-menu">
        <div class="sm-search-wrap">
            <div class="sm-search">
                <span class="sm-search-icon"><?= ic_svg('search') ?></span>
                <input type="text" id="start-search-input" placeholder="Поиск приложений и параметров" autocomplete="off">
            </div>
        </div>
        <div class="sm-body">
            <div class="sm-section-head">
                <span>Закреплено</span>
                <button class="sm-allapps" id="sm-allapps-btn">Все приложения <?= ic_svg('chevron_right') ?></button>
            </div>
            <div class="sm-pinned" id="sm-pinned"></div>
            <div class="sm-recommend-head">Рекомендуемые</div>
            <div class="sm-recommend" id="sm-recommend"></div>
        </div>
        <div class="sm-footer">
            <div class="sm-user">
                <span class="sm-avatar"><?= ic_svg('user') ?></span>
                <span id="sm-username">Пользователь</span>
            </div>
            <button class="sm-power" id="sm-power-btn" title="Завершение работы"><?= ic_svg('power') ?></button>
        </div>
    </div>

    <!-- ===== Центр уведомлений + быстрые действия (Win11) ===== -->
    <div id="action-center">
        <div class="ac-notif-head">
            <span>Уведомления</span>
            <button class="ac-clear" id="ac-clear">Очистить все</button>
        </div>
        <div class="ac-notif-list" id="ac-notif-list">
            <div class="ac-empty">Новых уведомлений нет</div>
        </div>
        <div class="ac-quick" id="ac-quick"></div>
        <div class="ac-slider-row">
            <span class="ac-slider-icon"><?= ic_svg('volume') ?></span>
            <input type="range" id="vol-slider" min="0" max="100" value="65">
            <span id="vol-value">65</span>
        </div>
        <div class="ac-slider-row">
            <span class="ac-slider-icon"><?= ic_svg('battery') ?></span>
            <input type="range" id="bri-slider" min="30" max="100" value="100">
            <span id="bri-value">100%</span>
        </div>
        <div class="ac-footer">
            <button class="ac-foot-btn" id="ac-settings-btn"><?= ic_svg('settings') ?><span>Параметры</span></button>
        </div>
    </div>

    <!-- ===== Календарь всплывающий (Win11 flyout) ===== -->
    <div id="calendar-flyout">
        <div class="cf-time" id="cf-time">00:00</div>
        <div class="cf-date" id="cf-date"></div>
        <div class="cf-grid" id="cf-grid"></div>
    </div>

    <!-- ===== Экран завершения работы ===== -->
    <div id="shutdown-screen" class="hidden">
        <div class="sd-spinner"></div>
        <div class="sd-text" id="sd-text">Завершение работы...</div>
    </div>

    <!-- ===== Панель задач (Windows 11: по центру) ===== -->
    <div id="taskbar">
        <div class="tb-center">
            <div class="tb-btn tb-start" id="start-btn" title="Пуск"><?= ic_svg('start') ?></div>
            <div class="tb-btn" data-app="search" title="Поиск"><?= ic_svg('search') ?></div>
            <div class="tb-btn" data-app="edge" title="IC Browser"><?= ic_svg('edge') ?></div>
            <div class="tb-btn" data-app="market" title="ICMarket"><?= ic_svg('market') ?></div>
            <div class="tb-sep"></div>
            <div class="taskbar-apps" id="taskbar-apps"></div>
        </div>
        <div class="system-tray" id="system-tray">
            <button class="tray-chevron" id="tray-chevron" title="Показать скрытые значки"><?= ic_svg('chevron_right') ?></button>
            <div class="tray-hidden" id="tray-hidden">
                <div class="tray-icon-sm" title="Заметки в фоне"><?= ic_svg('notes') ?></div>
            </div>
            <div class="tray-group" id="tray-group" title="Сеть, звук, батарея">
                <span class="tray-mini"><?= ic_svg('wifi') ?></span>
                <span class="tray-mini"><?= ic_svg('volume') ?></span>
                <span class="tray-mini"><?= ic_svg('battery') ?></span>
            </div>
            <div class="tray-clock" id="tray-clock">
                <span id="tray-time">00:00</span>
                <span id="tray-date">01.01.2025</span>
            </div>
            <div class="show-desktop" id="show-desktop" title="Свернуть все окна"></div>
        </div>
    </div>

    <script>window.ICONS = <?= ic_js_map() ?>;</script>
    <script src="workspace.js?v=2"></script>
</body>
</html>
