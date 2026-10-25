<?php
// hi.php - мастер первичной настройки ICOS в стиле Windows
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>ICOS — Первичная настройка</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body {
        height: 100%;
        background: #0a0a14;
        color: #e0e0e0;
        font-family: "Segoe UI", Arial, sans-serif;
        overflow: hidden;
    }
    #app {
        height: 100%;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 20px;
    }
    .card {
        width: 100%;
        max-width: 600px;
        background: #14141f;
        border: 1px solid #2a2a3a;
        border-radius: 14px;
        padding: 28px 32px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        max-height: 90vh;
        overflow-y: auto;
    }
    h1 { font-size: 22px; color: #4a9eff; margin-bottom: 6px; letter-spacing: 1px; }
    .sub { color: #778; font-size: 13px; margin-bottom: 22px; }
    .step-indicator { display: flex; gap: 8px; margin-bottom: 22px; }
    .dot { flex: 1; height: 4px; background: #2a2a3a; border-radius: 2px; transition: background 0.3s; }
    .dot.active { background: #4a9eff; }
    .dot.done   { background: #55cc77; }

    .wifi-list { display: flex; flex-direction: column; gap: 8px; max-height: 280px; overflow-y: auto; margin-bottom: 18px; }
    .wifi-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 14px; background: #1c1c2a; border: 1px solid #2a2a3a;
        border-radius: 10px; cursor: pointer; transition: border-color 0.15s, background 0.15s; font-size: 14px;
    }
    .wifi-item:hover { border-color: #4a9eff; background: #20202f; }
    .wifi-item.selected { border-color: #4a9eff; background: #1e2a44; }
    .wifi-item .left { display: flex; flex-direction: column; gap: 2px; }
    .wifi-item .name { color: #e0e0e0; }
    .wifi-item .hint { color: #6688aa; font-size: 12px; }
    .wifi-item .signal { color: #88a; font-size: 12px; }

    .field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
    .field label { font-size: 13px; color: #99a; }
    .field input, .field select {
        background: #1c1c2a; border: 1px solid #2a2a3a; border-radius: 8px;
        color: #e0e0e0; padding: 10px 12px; font-size: 14px; font-family: inherit; outline: none;
    }
    .field input:focus, .field select:focus { border-color: #4a9eff; }

    .actions { display: flex; justify-content: space-between; gap: 10px; margin-top: 8px; flex-wrap: wrap; }
    button {
        padding: 10px 22px; font-size: 14px; font-family: inherit;
        background: #4a9eff; color: #fff; border: none; border-radius: 8px;
        cursor: pointer; transition: background 0.2s, opacity 0.2s;
    }
    button:hover { background: #3a8ee6; }
    button.secondary { background: transparent; border: 1px solid #2a2a3a; color: #99a; }
    button.secondary:hover { border-color: #4a9eff; color: #e0e0e0; }
    button:disabled { opacity: 0.4; cursor: not-allowed; }

    .status { font-size: 13px; color: #55cc77; min-height: 18px; margin-bottom: 12px; }
    .status.err { color: #ff6666; }
    .hidden { display: none !important; }

    .sync-row { display: flex; align-items: center; gap: 12px; margin-bottom: 18px; flex-wrap: wrap; }
    .clock { font-size: 13px; color: #99a; font-family: "Courier New", monospace; }

    .summary { font-size: 14px; line-height: 1.7; }
    .summary b { color: #4a9eff; }
</style>
</head>
<body>
<div id="app">
    <div class="card" id="setup-card">
        <div class="step-indicator">
            <div class="dot active" id="dot0"></div>
            <div class="dot" id="dot1"></div>
            <div class="dot" id="dot2"></div>
            <div class="dot" id="dot3"></div>
            <div class="dot" id="dot4"></div>
            <div class="dot" id="dot5"></div>
        </div>

        <!-- Шаг 1: Регион -->
        <div id="step-region">
            <h1>Регион</h1>
            <div class="sub">Выберите ваш регион</div>
            <div class="field">
                <label for="region">Регион</label>
                <select id="region">
                    <option value="">— выберите регион —</option>
                    <option value="RU">Россия</option>
                    <option value="BY">Беларусь</option>
                    <option value="KZ">Казахстан</option>
                    <option value="UA">Украина</option>
                    <option value="US">США</option>
                    <option value="DE">Германия</option>
                </select>
            </div>
            <div class="actions">
                <span></span>
                <button id="next-region" disabled>Далее</button>
            </div>
        </div>

        <!-- Шаг 2: Язык и раскладка -->
        <div id="step-lang" class="hidden">
            <h1>Язык и раскладка</h1>
            <div class="sub">Выберите язык системы и раскладку клавиатуры</div>
            <div class="field">
                <label for="lang">Язык системы</label>
                <select id="lang">
                    <option value="">— выберите язык —</option>
                    <option value="ru">Русский</option>
                    <option value="en">English</option>
                    <option value="de">Deutsch</option>
                    <option value="fr">Français</option>
                </select>
            </div>
            <div class="field">
                <label for="layout">Раскладка клавиатуры</label>
                <select id="layout">
                    <option value="">— выберите раскладку —</option>
                    <option value="ru-en">Русская — Английская</option>
                    <option value="en">Английская</option>
                    <option value="ru">Русская</option>
                </select>
            </div>
            <div class="actions">
                <button class="secondary" id="back-lang">Назад</button>
                <button id="next-lang" disabled>Далее</button>
            </div>
        </div>

        <!-- Шаг 3: Wi-Fi -->
        <div id="step-wifi" class="hidden">
            <h1>Подключение к сети</h1>
            <div class="sub">Выберите Wi-Fi сеть для подключения</div>
            <div class="wifi-list" id="wifi-list"></div>
            <div class="status" id="wifi-status"></div>
            <div class="actions">
                <button class="secondary" id="skip-wifi">Пропустить</button>
                <button id="next-wifi" disabled>Далее</button>
            </div>
        </div>

        <!-- Шаг 4: Часовой пояс -->
        <div id="step-tz" class="hidden">
            <h1>Часовой пояс</h1>
            <div class="sub">Выберите часовой пояс или синхронизируйте время с сетью</div>
            <div class="field">
                <label for="tz">Часовой пояс</label>
                <select id="tz"></select>
            </div>
            <div class="sync-row">
                <button class="secondary" id="sync-time">Синхронизировать с сетью</button>
                <span class="clock" id="clock">—</span>
            </div>
            <div class="actions">
                <button class="secondary" id="back-tz">Назад</button>
                <button id="next-tz" disabled>Далее</button>
            </div>
        </div>

        <!-- Шаг 5: Имя пользователя -->
        <div id="step-user" class="hidden">
            <h1>Кто будет использовать это устройство?</h1>
            <div class="sub">Введите имя пользователя</div>
            <div class="field">
                <label for="username">Имя пользователя</label>
                <input id="username" type="text" placeholder="Например, Иван" maxlength="32" autocomplete="off">
            </div>
            <div class="actions">
                <button class="secondary" id="back-user">Назад</button>
                <button id="next-user" disabled>Далее</button>
            </div>
        </div>

        <!-- Шаг 6: Пароль -->
        <div id="step-password" class="hidden">
            <h1>Придумайте пароль</h1>
            <div class="sub">Пароль защитит вашу учётную запись. Можно войти без пароля.</div>
            <div class="field">
                <label for="password">Пароль</label>
                <input id="password" type="password" placeholder="Введите пароль" autocomplete="new-password">
            </div>
            <div class="field">
                <label for="password2">Подтвердите пароль</label>
                <input id="password2" type="password" placeholder="Повторите пароль" autocomplete="new-password">
            </div>
            <div class="status" id="pass-status"></div>
            <div class="actions">
                <button class="secondary" id="back-pass">Назад</button>
                <button class="secondary" id="skip-pass">Войти без пароля</button>
                <button id="finish" disabled>Завершить настройку</button>
            </div>
        </div>

        <!-- Готово -->
        <div id="step-done" class="hidden">
            <h1>Всё готово!</h1>
            <div class="sub">Проверьте введённые данные</div>
            <div class="summary" id="summary"></div>
            <div class="actions" style="justify-content:flex-end;">
                <button id="go-password">Продолжить</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // ===== Ключи с РЕАЛЬНЫМИ введёнными данными =====
    var KEYS = ["icos_region", "icos_lang", "icos_layout", "icos_wifi", "icos_tz", "icos_user", "icos_password"];

    // ===== Если все данные уже есть — настройка не нужна =====
    var allFilled = KEYS.every(function (k) {
        var v = localStorage.getItem(k);
        return v !== null && v !== "";
    });
    if (allFilled) {
        window.location.replace("password.php");
        return;
    }

    // ===== Шаги =====
    var steps = ["step-region", "step-lang", "step-wifi", "step-tz", "step-user", "step-password", "step-done"];
    var dots = [
        document.getElementById("dot0"),
        document.getElementById("dot1"),
        document.getElementById("dot2"),
        document.getElementById("dot3"),
        document.getElementById("dot4"),
        document.getElementById("dot5")
    ];
    var current = 0;

    function showStep(n) {
        current = n;
        steps.forEach(function (id, i) {
            var el = document.getElementById(id);
            if (i === n) el.classList.remove("hidden");
            else el.classList.add("hidden");
        });
        dots.forEach(function (d, i) {
            d.classList.remove("active", "done");
            if (i < Math.min(n, dots.length)) d.classList.add("done");
            if (i === Math.min(n, dots.length - 1)) d.classList.add("active");
        });
    }

    // ===== Шаг 1: Регион =====
    var regionSel = document.getElementById("region");
    var nextRegionBtn = document.getElementById("next-region");
    regionSel.addEventListener("change", function () {
        nextRegionBtn.disabled = !regionSel.value;
    });
    nextRegionBtn.addEventListener("click", function () {
        if (!regionSel.value) return;
        localStorage.setItem("icos_region", regionSel.value);
        showStep(1);
    });

    // ===== Шаг 2: Язык и раскладка =====
    var langSel     = document.getElementById("lang");
    var layoutSel   = document.getElementById("layout");
    var nextLangBtn = document.getElementById("next-lang");

    function checkLang() {
        nextLangBtn.disabled = !(langSel.value && layoutSel.value);
    }
    langSel.addEventListener("change", checkLang);
    layoutSel.addEventListener("change", checkLang);

    nextLangBtn.addEventListener("click", function () {
        if (!langSel.value || !layoutSel.value) return;
        localStorage.setItem("icos_lang",   langSel.value);
        localStorage.setItem("icos_layout", layoutSel.value);
        showStep(2);
    });
    document.getElementById("back-lang").addEventListener("click", function () { showStep(0); });

    // ===== Шаг 3: Wi-Fi =====
    var networks = [
        { name: "MyWiFi",       hint: "Пароль: 192849", signal: "▂▄▆█" },
        { name: "TP-LINK_2G",   hint: "",                signal: "▂▄▆ " },
        { name: "MTS_Home",     hint: "",                signal: "▂▄   " },
        { name: "Beeline_WiFi", hint: "",                signal: "▂▄▆ " },
        { name: "Rostelecom",   hint: "",                signal: "▂    " },
        { name: "DIR-615",      hint: "",                signal: "▂▄   " },
        { name: "FreeWiFi",     hint: "",                signal: "▂    " }
    ];

    var selectedNetwork = null;
    var wifiStatus  = document.getElementById("wifi-status");
    var nextWifiBtn = document.getElementById("next-wifi");
    var listEl      = document.getElementById("wifi-list");

    networks.forEach(function (n) {
        var div = document.createElement("div");
        div.className = "wifi-item";
        div.innerHTML =
            '<div class="left">' +
                '<span class="name">' + n.name + '</span>' +
                (n.hint ? '<span class="hint">' + n.hint + '</span>' : '') +
            '</div>' +
            '<span class="signal">' + n.signal + '</span>';
        div.addEventListener("click", function () {
            document.querySelectorAll(".wifi-item").forEach(function (x) {
                x.classList.remove("selected");
            });
            div.classList.add("selected");
            selectedNetwork = n;
            nextWifiBtn.disabled = false;
            wifiStatus.textContent = "";
            wifiStatus.className = "status";
        });
        listEl.appendChild(div);
    });

    nextWifiBtn.addEventListener("click", function () {
        if (!selectedNetwork) return;
        wifiStatus.textContent = "Подключение к " + selectedNetwork.name + "...";
        wifiStatus.className = "status";
        nextWifiBtn.disabled = true;

        setTimeout(function () {
            if (selectedNetwork.name === "MyWiFi") {
                wifiStatus.textContent = "Подключено к MyWiFi";
                wifiStatus.className = "status";
                localStorage.setItem("icos_wifi", selectedNetwork.name);
                setTimeout(function () { showStep(3); }, 900);
            } else {
                wifiStatus.textContent = "Не удалось подключиться (неверный пароль)";
                wifiStatus.className = "status err";
                nextWifiBtn.disabled = false;
            }
        }, 1200);
    });

    document.getElementById("skip-wifi").addEventListener("click", function () {
        localStorage.setItem("icos_wifi", "skipped");
        showStep(3);
    });

    // ===== Шаг 4: Часовой пояс =====
    var clockEl   = document.getElementById("clock");
    var syncBtn   = document.getElementById("sync-time");
    var tzSel     = document.getElementById("tz");
    var nextTzBtn = document.getElementById("next-tz");

    var userTzName = "неизвестно";
    try {
        userTzName = Intl.DateTimeFormat().resolvedOptions().timeZone || "неизвестно";
    } catch (e) {}

    var offsetHours = -new Date().getTimezoneOffset() / 60;
    var offsetStr   = "UTC" + (offsetHours >= 0 ? "+" : "") + offsetHours;

    var fixedZones = [
        { value: "UTC+2",  label: "UTC+2 — Калининград" },
        { value: "UTC+3",  label: "UTC+3 — Москва" },
        { value: "UTC+4",  label: "UTC+4 — Самара" },
        { value: "UTC+5",  label: "UTC+5 — Екатеринбург" },
        { value: "UTC+6",  label: "UTC+6 — Омск" },
        { value: "UTC+7",  label: "UTC+7 — Красноярск" },
        { value: "UTC+8",  label: "UTC+8 — Иркутск" },
        { value: "UTC+9",  label: "UTC+9 — Якутск" },
        { value: "UTC+10", label: "UTC+10 — Владивосток" },
        { value: "UTC+11", label: "UTC+11 — Магадан" },
        { value: "UTC+12", label: "UTC+12 — Камчатка" }
    ];

    var userOpt = document.createElement("option");
    userOpt.value = userTzName;
    userOpt.textContent = offsetStr + " — " + userTzName + " (ваш пояс)";
    tzSel.appendChild(userOpt);

    fixedZones.forEach(function (z) {
        var o = document.createElement("option");
        o.value = z.value;
        o.textContent = z.label;
        tzSel.appendChild(o);
    });

    tzSel.value = userTzName;
    nextTzBtn.disabled = false;

    tzSel.addEventListener("change", function () {
        nextTzBtn.disabled = !tzSel.value;
    });

    syncBtn.addEventListener("click", function () {
        syncBtn.disabled = true;
        clockEl.textContent = "Синхронизация...";
        setTimeout(function () {
            var now = new Date();
            clockEl.textContent = "Время: " + now.toLocaleTimeString("ru-RU");
            tzSel.value = userTzName;
            nextTzBtn.disabled = false;
            syncBtn.disabled = false;
        }, 1000);
    });

    nextTzBtn.addEventListener("click", function () {
        if (!tzSel.value) return;
        localStorage.setItem("icos_tz", tzSel.value);
        showStep(4);
    });
    document.getElementById("back-tz").addEventListener("click", function () { showStep(2); });

    // ===== Шаг 5: Имя пользователя =====
    var userInput = document.getElementById("username");
    var nextUserBtn = document.getElementById("next-user");

    userInput.addEventListener("input", function () {
        nextUserBtn.disabled = userInput.value.trim().length < 2;
    });

    nextUserBtn.addEventListener("click", function () {
        var name = userInput.value.trim();
        if (name.length < 2) return;
        localStorage.setItem("icos_user", name);
        showStep(5);
    });
    document.getElementById("back-user").addEventListener("click", function () { showStep(3); });

    // ===== Шаг 6: Пароль =====
    var passInput   = document.getElementById("password");
    var passInput2  = document.getElementById("password2");
    var passStatus  = document.getElementById("pass-status");
    var finishBtn   = document.getElementById("finish");

    function checkPasswords() {
        var p1 = passInput.value;
        var p2 = passInput2.value;

        if (p1.length === 0 && p2.length === 0) {
            passStatus.textContent = "";
            passStatus.className = "status";
            finishBtn.disabled = true;
            return;
        }
        if (p1.length < 4) {
            passStatus.textContent = "Пароль слишком короткий (минимум 4 символа)";
            passStatus.className = "status err";
            finishBtn.disabled = true;
            return;
        }
        if (p1 !== p2) {
            passStatus.textContent = "Пароли не совпадают";
            passStatus.className = "status err";
            finishBtn.disabled = true;
            return;
        }
        passStatus.textContent = "Пароли совпадают";
        passStatus.className = "status";
        finishBtn.disabled = false;
    }

    passInput.addEventListener("input", checkPasswords);
    passInput2.addEventListener("input", checkPasswords);

    finishBtn.addEventListener("click", function () {
        var p1 = passInput.value;
        if (p1.length < 4) return;
        localStorage.setItem("icos_password", p1);
        showSummary();
        showStep(6);
    });

    document.getElementById("skip-pass").addEventListener("click", function () {
        localStorage.setItem("icos_password", "none");
        showSummary();
        showStep(6);
    });

    document.getElementById("back-pass").addEventListener("click", function () { showStep(4); });

    // ===== Итог =====
    function showSummary() {
        var region = localStorage.getItem("icos_region") || "—";
        var lang   = localStorage.getItem("icos_lang")   || "—";
        var layout = localStorage.getItem("icos_layout") || "—";
        var wifi   = localStorage.getItem("icos_wifi")   || "—";
        var tz     = localStorage.getItem("icos_tz")     || "—";
        var user   = localStorage.getItem("icos_user")   || "—";
        var pass   = localStorage.getItem("icos_password");

        var regionNames = { RU: "Россия", BY: "Беларусь", KZ: "Казахстан", UA: "Украина", US: "США", DE: "Германия" };
        var langNames   = { ru: "Русский", en: "English", de: "Deutsch", fr: "Français" };

        var passText = (pass === "none" || pass === null)
            ? "без пароля"
            : "•••••••• (задан)";

        document.getElementById("summary").innerHTML =
            "Регион: <b>" + (regionNames[region] || region) + "</b><br>" +
            "Язык: <b>" + (langNames[lang] || lang) + "</b><br>" +
            "Раскладка: <b>" + layout + "</b><br>" +
            "Wi-Fi: <b>" + (wifi === "skipped" ? "пропущено" : wifi) + "</b><br>" +
            "Часовой пояс: <b>" + tz + "</b><br>" +
            "Пользователь: <b>" + user + "</b><br>" +
            "Пароль: <b>" + passText + "</b>";
    }

    document.getElementById("go-password").addEventListener("click", function () {
        window.location.href = "password.php";
    });

    showStep(0);
})();
</script>
</body>
</html>