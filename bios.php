<?php
// bios.php - ICBIOS с проверкой кэша (localStorage)
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>ICBIOS</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body {
        height: 100%;
        background: #000;
        color: #e0e0e0;
        font-family: "Courier New", monospace;
        overflow: hidden;
    }
    #bios { height: 100%; padding: 40px; overflow-y: auto; }
    .line { margin-bottom: 6px; font-size: 16px; min-height: 19px; white-space: pre-wrap; }
    .dim { color: #888; }
    .err { color: #ff5555; }
    .ok  { color: #55ff55; }
    .warn { color: #ffcc55; }

    .logo {
        position: absolute; top: 20px; right: 20px;
        width: 64px; height: 64px;
        border: 2px solid #e0e0e0;
    }
    .logo::before, .logo::after {
        content: ""; position: absolute; top: 50%; left: 50%;
        width: 70%; height: 2px; background: #e0e0e0;
    }
    .logo::before { transform: translate(-50%, -50%) rotate(45deg); }
    .logo::after  { transform: translate(-50%, -50%) rotate(-45deg); }

    #progress-wrap { margin-top: 16px; font-size: 16px; display: none; }
    #bar {
        width: 420px; height: 14px;
        border: 1px solid #e0e0e0;
        margin-top: 6px; overflow: hidden;
    }
    #bar-fill {
        height: 100%; width: 0%;
        background: #e0e0e0;
        transition: width 0.15s linear;
    }
</style>
</head>
<body>
<div id="bios">
    <div class="line dim">ICBIOS v1.0 — Console Mode</div>
    <div class="line">&nbsp;</div>
    <div id="output"></div>

    <div id="progress-wrap">
        <div id="progress-label">Loading: <span id="pct">0</span>%</div>
        <div id="bar"><div id="bar-fill"></div></div>
    </div>
</div>

<div class="logo"></div>

<script>
(function () {
    // ===== Реальные характеристики =====
    var REAL_MEM_GB   = 8;
    var REAL_FREQ     = "3.5 GHz";
    var REAL_BITS     = 64;
    var REAL_PLATFORM = "Windows x64";
    var cores = navigator.hardwareConcurrency || "N/A";

    // ===== Время этапов (мс) =====
    var DL_DURATION     = 6000; // скачивание ICPC (только первый запуск)
    var BOOT_DURATION   = 4000; // полная загрузка после установки драйвера
    var FAST_BOOT       = 2500; // быстрая загрузка, если система уже установлена
    var LINE_DELAY      = 250;

    var output    = document.getElementById("output");
    var progWrap  = document.getElementById("progress-wrap");
    var progLabel = document.getElementById("progress-label");

    function printLine(text, cls) {
        var div = document.createElement("div");
        div.className = "line" + (cls ? " " + cls : "");
        div.textContent = text;
        output.appendChild(div);
    }

    function downloadDriver(onDone) {
        var TOTAL_GB = 2.69;
        var start = performance.now();
        progWrap.style.display = "block";
        progLabel.innerHTML = 'Downloading ICPC driver... <span id="pct">0</span>% <span id="dlsize" class="dim">(0.00 / 2.69 GB)</span>';
        var pctLocal  = document.getElementById("pct");
        var sizeLocal = document.getElementById("dlsize");
        var fillLocal = document.getElementById("bar-fill");
        fillLocal.style.width = "0%";

        function tick(now) {
            var p = Math.min((now - start) / DL_DURATION, 1);
            var percent = Math.floor(p * 100);
            var gb = (TOTAL_GB * p).toFixed(2);
            pctLocal.textContent  = percent;
            sizeLocal.textContent = "(" + gb + " / " + TOTAL_GB + " GB)";
            fillLocal.style.width = percent + "%";
            if (p < 1) requestAnimationFrame(tick);
            else { printLine("ICPC driver downloaded: " + TOTAL_GB + " GB", "ok"); setTimeout(onDone, 400); }
        }
        requestAnimationFrame(tick);
    }

    // Универсальная шкала загрузки
    function startProgress(duration, redirectTo) {
        var start = performance.now();
        progWrap.style.display = "block";
        progLabel.innerHTML = 'Loading: <span id="pct">0</span>%';
        var pctLocal  = document.getElementById("pct");
        var fillLocal = document.getElementById("bar-fill");
        fillLocal.style.width = "0%";

        function tick(now) {
            var p = Math.min((now - start) / duration, 1);
            var percent = Math.floor(p * 100);
            pctLocal.textContent  = percent;
            fillLocal.style.width = percent + "%";
            if (p < 1) requestAnimationFrame(tick);
            else window.location.href = redirectTo;
        }
        requestAnimationFrame(tick);
    }

    // ===== ПРОВЕРКА КЭША (все реальные данные введены?) =====
    var SETUP_KEYS = ["icos_region", "icos_lang", "icos_layout", "icos_wifi", "icos_tz", "icos_user", "icos_password"];
    var installed = SETUP_KEYS.every(function (k) {
        var v = localStorage.getItem(k);
        return v !== null && v !== "";
    });

    if (installed) {
        // Система уже установлена: драйвер НЕ качаем, но нормальную загрузку проходим
        var fastScript = [
            ["line", "ICBIOS> checking system cache...", "dim"],
            ["line", "System already installed. Skipping driver download.", "ok"],
            ["line", "Loading image: ICOS.img ...", "ok"],
            ["line", "Detecting IDE drives... done", ""],
            ["line", "Booting from primary device...", ""],
            ["fastprogress"]
        ];
        var fidx = 0;
        function fastNext() {
            if (fidx >= fastScript.length) return;
            var s = fastScript[fidx++];
            if (s[0] === "line") {
                printLine(s[1], s[2]);
                setTimeout(fastNext, LINE_DELAY);
            } else if (s[0] === "fastprogress") {
                startProgress(FAST_BOOT, "boot.php");
            }
        }
        setTimeout(fastNext, 400);
    } else {
        // Первый запуск: полный сценарий со скачиванием
        var script = [
            ["line", "ICBIOS> bios info", "dim"],
            ["line", "CPU: IC Processor @ " + REAL_FREQ + " (" + cores + " Cores)", ""],
            ["line", "RAM: " + REAL_MEM_GB + " GB", ""],
            ["line", "Platform: " + REAL_PLATFORM + " (" + REAL_BITS + "-bit)", "dim"],
            ["line", " ", ""],
            ["line", "ICBIOS> bios boot boot.img", "dim"],
            ["line", "Loading image: boot.img ...", "ok"],
            ["line", "Detecting IDE drives... done", ""],
            ["line", " ", ""],
            ["line", "ICBIOS> driver search ICPC", "dim"],
            ["line", "Searching driver ICPC...", ""],
            ["line", "ERROR: driver ICPC not found on local system.", "err"],
            ["line", "Attempting to download ICPC driver from update server...", "warn"],
            ["download"],
            ["line", " ", ""],
            ["line", "Booting from primary device...", ""],
            ["progress"]
        ];
        var idx = 0;
        function next() {
            if (idx >= script.length) return;
            var s = script[idx++];
            if (s[0] === "line") { printLine(s[1] === " " ? " " : s[1], s[2]); setTimeout(next, LINE_DELAY); }
            else if (s[0] === "download") { downloadDriver(next); }
            else if (s[0] === "progress") { startProgress(BOOT_DURATION, "boot.php"); }
        }
        setTimeout(next, 400);
    }

    // ===== F10 — сброс сохранения =====
    document.addEventListener("keydown", function (e) {
        if (e.key === "F10") {
            e.preventDefault();
            // Очищаем ВСЕ данные настройки
            ["icos_region", "icos_lang", "icos_layout", "icos_wifi", "icos_tz", "icos_user", "icos_password"].forEach(function (k) {
                localStorage.removeItem(k);
            });
            window.location.href = "reset.php";
        }
    });
})();
</script>
</body>
</html>