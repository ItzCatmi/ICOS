<?php
// password.php - экран блокировки и входа в стиле Windows 11
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>ICOS — Вход</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body {
        height: 100%;
        font-family: "Segoe UI", Arial, sans-serif;
        color: #fff;
        overflow: hidden;
        background-color: #07122a;
    }

    /* ===== ФОН (Обои) ===== */
    #bg {
        position: absolute; inset: 0;
        background: radial-gradient(circle at 30% 20%, #1d4e9e 0%, #0f2861 40%, #07122a 100%);
        z-index: 0;
    }
    #bg::before {
        content: ""; position: absolute; inset: -50%;
        background: 
            radial-gradient(ellipse at 50% 50%, rgba(80, 160, 255, 0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 20%, rgba(0, 200, 255, 0.1) 0%, transparent 40%);
        filter: blur(40px);
        animation: pulse-bg 10s ease-in-out infinite alternate;
    }
    @keyframes pulse-bg {
        0% { transform: scale(1) translate(0, 0); }
        100% { transform: scale(1.1) translate(-2%, 2%); }
    }
    .wave-line {
        position: absolute;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        z-index: 1;
    }
    .wave-line:nth-child(1) { width: 800px; height: 800px; top: -200px; left: -200px; }
    .wave-line:nth-child(2) { width: 1200px; height: 1200px; bottom: -400px; right: -400px; border-width: 2px; border-color: rgba(80, 160, 255, 0.1); }
    .wave-line:nth-child(3) { width: 400px; height: 400px; top: 20%; right: 10%; border-color: rgba(255, 255, 255, 0.08); }

    /* ===== ЭКРАН 1: Часы ===== */
    #screen-clock {
        position: absolute; inset: 0; z-index: 2;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        transition: transform 0.6s cubic-bezier(0.2, 0.8, 0.2, 1), opacity 0.6s ease;
    }
    #screen-clock.up {
        transform: translateY(-25vh) scale(0.9);
        opacity: 0;
        pointer-events: none;
    }
    .big-time {
        font-size: 140px;
        font-weight: 200;
        letter-spacing: -2px;
        line-height: 1;
        text-shadow: 0 4px 20px rgba(0,0,0,0.3);
        margin-bottom: 10px;
    }
    .big-date {
        font-size: 28px;
        font-weight: 400;
        color: #d0e0ff;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }

    /* ===== ЭКРАН 2: Вход ===== */
    #screen-login {
        position: absolute; inset: 0; z-index: 3;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        opacity: 0;
        transform: translateY(40px);
        pointer-events: none;
        transition: opacity 0.5s ease, transform 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
    }
    #screen-login.active {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .avatar {
        width: 130px; height: 130px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4a9eff, #2b6cd4);
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 20px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        border: 2px solid rgba(255,255,255,0.2);
    }
    .avatar svg { width: 70px; height: 70px; fill: #fff; }

    .user-name {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 24px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.5);
    }

    .password-box {
        display: flex; align-items: center;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 8px;
        padding: 0 12px;
        width: 320px;
        height: 44px;
        margin-bottom: 12px;
        backdrop-filter: blur(10px);
        transition: border-color 0.2s, background 0.2s;
    }
    .password-box:focus-within {
        border-color: #4a9eff;
        background: rgba(255,255,255,0.15);
    }
    .password-box input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        color: #fff;
        font-size: 15px;
        font-family: inherit;
        height: 100%;
        padding: 0 8px;
    }
    .password-box input::placeholder { color: #aab; }
    .password-box button {
        background: transparent;
        border: none;
        color: #c0d8ff;
        cursor: pointer;
        padding: 6px;
        border-radius: 4px;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.15s;
    }
    .password-box button:hover { background: rgba(255,255,255,0.2); }

    .hint-text {
        font-size: 13px;
        color: #aab;
        margin-bottom: 8px;
        min-height: 18px;
    }
    .error-text { color: #ff8888; }

    .reset-link {
        font-size: 13px;
        color: #c0d8ff;
        text-decoration: none;
        cursor: pointer;
    }
    .reset-link:hover { text-decoration: underline; }

    /* ===== НИЖНЯЯ ПАНЕЛЬ ===== */
    .bottom-bar {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 36px;
        z-index: 10;
        color: #e0e0e0;
        font-size: 14px;
    }
    .bottom-left, .bottom-right {
        display: flex; align-items: center; gap: 24px;
    }
    
    /* Переключатель раскладки (RUS/ENG) */
    .lang-switch {
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        padding: 6px 10px;
        border-radius: 6px;
        transition: background 0.2s;
        user-select: none;
    }
    .lang-switch:hover { background: rgba(255,255,255,0.1); }

    .icon-btn {
        background: transparent; border: none;
        cursor: pointer; padding: 6px;
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.2s;
    }
    .icon-btn:hover { background: rgba(255,255,255,0.1); }
    .icon-btn svg {
        width: 20px; height: 20px;
        fill: none; stroke: #e0e0e0; stroke-width: 1.5;
        stroke-linecap: round; stroke-linejoin: round;
    }
    
    .hidden { display: none !important; }
</style>
</head>
<body>
<div id="bg">
    <div class="wave-line"></div>
    <div class="wave-line"></div>
    <div class="wave-line"></div>
</div>

<!-- ЭКРАН 1: Часы -->
<div id="screen-clock">
    <div class="big-time" id="clock-time">10:24</div>
    <div class="big-date" id="clock-date">Четверг, 21 марта</div>
</div>

<!-- ЭКРАН 2: Вход -->
<div id="screen-login">
    <div class="avatar">
        <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12zm0 2.5c-3.3 0-9.8 1.6-9.8 4.9v2.4h19.6v-2.4c0-3.3-6.5-4.9-9.8-4.9z"/></svg>
    </div>
    <div class="user-name" id="login-username">Пользователь</div>

    <div class="password-box">
        <input id="pwd-input" type="password" placeholder="Пароль" autocomplete="off">
        <button id="login-btn" title="Войти">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
    </div>

    <div class="hint-text" id="hint-text"></div>
    <a class="reset-link" id="reset-link">Сбросить пароль</a>
</div>

<!-- Нижняя панель -->
<div class="bottom-bar">
    <div class="bottom-left">
        <!-- Переключатель раскладки -->
        <div class="lang-switch" id="lang-switch">RUS</div>
    </div>
    <div class="bottom-right">
        <button class="icon-btn" title="Специальные возможности">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
        </button>
        <button class="icon-btn" title="Питание">
            <svg viewBox="0 0 24 24"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
        </button>
    </div>
</div>

<script>
(function () {
    // ===== Проверка: настроена ли система? =====
    var KEYS = ["icos_region", "icos_lang", "icos_layout", "icos_wifi", "icos_tz", "icos_user", "icos_password"];
    var configured = KEYS.every(function (k) {
        var v = localStorage.getItem(k);
        return v !== null && v !== "";
    });

    if (!configured) {
        window.location.replace("hi.php");
        return;
    }

    var username = localStorage.getItem("icos_user") || "Пользователь";
    var realPassword = localStorage.getItem("icos_password");
    
    // Системный язык (для даты и текста ошибок)
    var sysLang = localStorage.getItem("icos_lang") || "ru";
    
    // Текущая раскладка (изначально — первая в списке раскладок системы)
    var currentLayout = (sysLang === "ru") ? "RUS" : "ENG";

    document.getElementById("login-username").textContent = username;
    document.getElementById("lang-switch").textContent = currentLayout;

    // ===== Часы на экране 1 =====
    var monthsRu = ["января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря"];
    var daysRu = ["Воскресенье","Понедельник","Вторник","Среда","Четверг","Пятница","Суббота"];
    
    var monthsEn = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    var daysEn = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

    function updateClock() {
        var now = new Date();
        var hh = String(now.getHours()).padStart(2, "0");
        var mm = String(now.getMinutes()).padStart(2, "0");
        document.getElementById("clock-time").textContent = hh + ":" + mm;

        if (sysLang === "ru") {
            document.getElementById("clock-date").textContent = daysRu[now.getDay()] + ", " + now.getDate() + " " + monthsRu[now.getMonth()];
        } else {
            document.getElementById("clock-date").textContent = daysEn[now.getDay()] + ", " + monthsEn[now.getMonth()] + " " + now.getDate();
        }
    }

    updateClock();
    setInterval(updateClock, 1000);

    // ===== Переключатель РАСКЛАДКИ =====
    var langSwitch = document.getElementById("lang-switch");
    
    langSwitch.addEventListener("click", function (e) {
        e.stopPropagation();
        if (currentLayout === "RUS") {
            currentLayout = "ENG";
        } else {
            currentLayout = "RUS";
        }
        langSwitch.textContent = currentLayout;
    });

    // ===== Анимация перехода =====
    var screenClock = document.getElementById("screen-clock");
    var screenLogin = document.getElementById("screen-login");

    function showLogin() {
        if (screenLogin.classList.contains("active")) return;
        screenClock.classList.add("up");
        setTimeout(function () {
            screenLogin.classList.add("active");
            setTimeout(function () {
                document.getElementById("pwd-input").focus();
            }, 300);
        }, 150);
    }

    function showClock() {
        screenLogin.classList.remove("active");
        setTimeout(function () {
            screenClock.classList.remove("up");
            document.getElementById("pwd-input").value = "";
            document.getElementById("hint-text").textContent = "";
            document.getElementById("hint-text").classList.remove("error-text");
        }, 300);
    }

    screenClock.addEventListener("click", showLogin);

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") showClock();
    });

    // ===== Вход =====
    var pwdInput = document.getElementById("pwd-input");
    var loginBtn = document.getElementById("login-btn");
    var hintText = document.getElementById("hint-text");

    function tryLogin() {
        var entered = pwdInput.value;

        if (realPassword === "none") {
            window.location.href = "workspace.php"; // <--- ПЕРЕХОД НА WORKSPACE
            return;
        }

        if (entered === realPassword) {
            hintText.textContent = (sysLang === "ru") ? "Добро пожаловать..." : "Welcome...";
            hintText.classList.remove("error-text");
            setTimeout(function () {
                window.location.href = "workspace.php"; // <--- ПЕРЕХОД НА WORKSPACE
            }, 600);
        } else {
            hintText.textContent = (sysLang === "ru") ? "Неверный пароль. Попробуйте снова." : "Incorrect password. Try again.";
            hintText.classList.add("error-text");
            pwdInput.value = "";
            pwdInput.focus();
        }
    }

    loginBtn.addEventListener("click", tryLogin);
    pwdInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") tryLogin();
    });

    // ===== Сброс =====
    document.getElementById("reset-link").addEventListener("click", function (e) {
        e.stopPropagation();
        var msg = (sysLang === "ru") ? "Сбросить все настройки и вернуться в BIOS?" : "Reset all settings and return to BIOS?";
        if (confirm(msg)) {
            KEYS.forEach(function (k) { localStorage.removeItem(k); });
            window.location.href = "bios.php";
        }
    });

    // Клик по пустому месту на экране входа возвращает часы
    screenLogin.addEventListener("click", function (e) {
        if (e.target === screenLogin) {
            showClock();
        }
    });
})();
</script>
</body>
</html>