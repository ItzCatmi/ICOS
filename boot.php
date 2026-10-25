<?php
// boot.php - краткий экран загрузки, потом start.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Boot</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body { height: 100%; background: #000; overflow: hidden; }
    #screen {
        height: 100%;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        color: #e0e0e0;
        font-family: "Courier New", monospace;
        font-size: 18px;
        transition: background 0.15s;
    }
    #screen.flash { background: #ffffff; color: #000; }
    .line { margin-bottom: 6px; }
    #bar-wrap {
        width: 320px; height: 12px;
        border: 1px solid #e0e0e0;
        margin-top: 18px; overflow: hidden;
    }
    #bar-fill {
        height: 100%; width: 0%;
        background: #e0e0e0;
        transition: width 0.05s linear;
    }
</style>
</head>
<body>
<div id="screen">
    <div class="line" id="l1"></div>
    <div class="line" id="l2"></div>
    <div id="bar-wrap" style="display:none;"><div id="bar-fill"></div></div>
</div>

<script>
(function () {
    var screen = document.getElementById("screen");
    var l1 = document.getElementById("l1");
    var l2 = document.getElementById("l2");
    var barWrap = document.getElementById("bar-wrap");
    var barFill = document.getElementById("bar-fill");

    // 1) Чёрный экран (уже есть) — короткая пауза
    setTimeout(function () {
        // 2) Вспышка белым
        screen.classList.add("flash");
        setTimeout(function () {
            screen.classList.remove("flash");

            // 3) Две строки на 1.5 секунды
            l1.textContent = "boot system.img";
            l2.textContent = "boot ICOS.img";
            barWrap.style.display = "block";

            // 4) Загрузка на 1 секунду
            var DURATION = 1000;
            var start = performance.now();
            function tick(now) {
                var p = Math.min((now - start) / DURATION, 1);
                barFill.style.width = Math.floor(p * 100) + "%";
                if (p < 1) requestAnimationFrame(tick);
                else window.location.href = "start.php";
            }
            requestAnimationFrame(tick);
        }, 150);
    }, 300);
})();
</script>
</body>
</html>