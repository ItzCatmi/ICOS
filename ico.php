<?php
// ico.php — генератор .ico файла для логотипа ICOS (favicon Windows 11 style)
header('Content-Type: image/x-icon');

$size = 32;
$img = imagecreatetruecolor($size, $size);
imagesavealpha($img, true);
$transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
imagefill($img, 0, 0, $transparent);

// Градиент от #4cc2ff к #1c64d2
function lerp($a, $b, $t) { return (int)round($a + ($b - $a) * $t); }

for ($y = 0; $y < $size; $y++) {
    for ($x = 0; $x < $size; $x++) {
        $t = (($x / ($size - 1)) + ($y / ($size - 1))) / 2; // диагональный градиент
        $r = lerp(0x4c, 0x1c, $t);
        $g = lerp(0xc2, 0x64, $t);
        $b = lerp(0xff, 0xd2, $t);
        imagesetpixel($img, $x, $y, imagecolorallocate($img, $r, $g, $b));
    }
}

$white = imagecolorallocate($img, 255, 255, 255);

// Четыре скруглённых квадрата с зазором (как флаг Windows 11)
$gap = 3;      // внешний отступ
$mid = 2;      // зазор между плитками
$tile = (int)((($size - 2 * $gap - $mid) / 2)); // размер плитки
$rad = 3;      // радиус скругления

$tiles = [
    [$gap, $gap],
    [$gap + $tile + $mid, $gap],
    [$gap, $gap + $tile + $mid],
    [$gap + $tile + $mid, $gap + $tile + $mid],
];

foreach ($tiles as [$tx, $ty]) {
    // Заливаем плитку, вырезая углы
    for ($y = 0; $y < $tile; $y++) {
        for ($x = 0; $x < $tile; $x++) {
            $dx = 0; $dy = 0;
            if ($x < $rad && $y < $rad) { $dx = $rad - $x; $dy = $rad - $y; }
            elseif ($x >= $tile - $rad && $y < $rad) { $dx = $x - ($tile - $rad - 1); $dy = $rad - $y; }
            elseif ($x < $rad && $y >= $tile - $rad) { $dx = $rad - $x; $dy = $y - ($tile - $rad - 1); }
            elseif ($x >= $tile - $rad && $y >= $tile - $rad) { $dx = $x - ($tile - $rad - 1); $dy = $y - ($tile - $rad - 1); }
            if (($dx > 0 && $dy > 0) && ($dx - 0.5) * ($dx - 0.5) + ($dy - 0.5) * ($dy - 0.5) > $rad * $rad) continue;
            imagesetpixel($img, $tx + $x, $ty + $y, $white);
        }
    }
}

// Превращаем PNG-данные в ICO
ob_start();
imagepng($img);
$png = ob_get_clean();
imagedestroy($img);

// ICO header: reserved(2) type(2)=1 count(2)=1 ; entry 16 bytes
$icondir = pack('vvv', 0, 1, 1);
$entry = pack('CCCCvvVV', $size, $size, 0, 0, 1, 32, strlen($png), 22);
echo $icondir . $entry . $png;
?>
