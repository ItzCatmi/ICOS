<?php
// icons.php — библиотека SVG-иконок в стиле Windows 11 (Fluent).
// Все иконки плоские, цветные, с мягкими формами — как в реальной Windows 11.
// Использование: echo ic("start");  или через JS: window.ICONS.start
?>
<?php
function ic_svg(string $name): string {
    static $icons = null;
    if ($icons === null) {
        $icons = [
            // Логотип ICOS — четыре плитки, как флаг Windows, но с фирменным градиентом
            'start' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="icg-s" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#4cc2ff"/><stop offset="1" stop-color="#1c64d2"/></linearGradient></defs><rect x="3" y="3" width="12" height="12" rx="2.2" fill="url(#icg-s)"/><rect x="17" y="3" width="12" height="12" rx="2.2" fill="url(#icg-s)"/><rect x="3" y="17" width="12" height="12" rx="2.2" fill="url(#icg-s)"/><rect x="17" y="17" width="12" height="12" rx="2.2" fill="url(#icg-s)"/></svg>',

            // Проводник — жёлтая папка Fluent
            'explorer' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="icg-f1" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffd54f"/><stop offset="1" stop-color="#f6a821"/></linearGradient><linearGradient id="icg-f2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffe082"/><stop offset="1" stop-color="#ffc233"/></linearGradient></defs><path d="M3 8.5A2.5 2.5 0 0 1 5.5 6h6.2c1 0 1.9.5 2.4 1.4l1.2 1.9H26.5A2.5 2.5 0 0 1 29 11.8V24a2.5 2.5 0 0 1-2.5 2.5h-21A2.5 2.5 0 0 1 3 24z" fill="url(#icg-f1)"/><path d="M3 12.5A2 2 0 0 1 5 10.5h22a2 2 0 0 1 2 2v10.7a2.5 2.5 0 0 1-2.5 2.5h-19A2.5 2.5 0 0 1 5 23.2z" fill="url(#icg-f2)" opacity="0.9"/></svg>',

            // Этот компьютер — монитор Fluent с синим экраном
            'computer' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="icg-m" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#4cc2ff"/><stop offset="1" stop-color="#1c64d2"/></linearGradient></defs><rect x="3" y="5" width="26" height="17" rx="2.5" fill="#5b6470"/><rect x="5" y="7" width="22" height="13" rx="1.5" fill="url(#icg-m)"/><path d="M12 24h8l1.2 3.2h-10.4z" fill="#7a828e"/><rect x="9" y="27.4" width="14" height="1.8" rx="0.9" fill="#5b6470"/></svg>',

            // Корзина пустая
            'trash' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M8.5 12h15l-1.2 14.2a2 2 0 0 1-2 1.8h-8.6a2 2 0 0 1-2-1.8z" fill="#9aa4b2"/><path d="M8.5 12h15l-.3 3.5H8.8z" fill="#77828f" opacity="0.55"/><rect x="6" y="8.2" width="20" height="3" rx="1.5" fill="#5b6470"/><path d="M12.8 8.2 13.6 6a1.6 1.6 0 0 1 1.5-1h1.8a1.6 1.6 0 0 1 1.5 1l.8 2.2z" fill="none" stroke="#5b6470" stroke-width="1.6"/><g stroke="#e8edf4" stroke-width="1.4" stroke-linecap="round"><line x1="13.5" y1="16.5" x2="13.9" y2="24.5"/><line x1="16" y1="16.5" x2="16" y2="24.5"/><line x1="18.5" y1="16.5" x2="18.1" y2="24.5"/></g></svg>',

            // Корзина полная
            'trash_full' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M8.5 12h15l-1.2 14.2a2 2 0 0 1-2 1.8h-8.6a2 2 0 0 1-2-1.8z" fill="#7d8794"/><rect x="6" y="8.2" width="20" height="3" rx="1.5" fill="#5b6470"/><path d="M12.8 8.2 13.6 6a1.6 1.6 0 0 1 1.5-1h1.8a1.6 1.6 0 0 1 1.5 1l.8 2.2z" fill="none" stroke="#5b6470" stroke-width="1.6"/><g stroke="#cfd6df" stroke-width="1.4" stroke-linecap="round"><line x1="13.5" y1="16.5" x2="13.9" y2="24.5"/><line x1="16" y1="16.5" x2="16" y2="24.5"/><line x1="18.5" y1="16.5" x2="18.1" y2="24.5"/></g><path d="M9.5 11.5 11 5l3 3 3-4.5 2.5 4 3-2.5 1.5 6.5z" fill="#a8b3c2"/></svg>',

            // Магазин (Microsoft Store style) — голубая сумка с флагом
            'market' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="icg-st" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#3ba7f7"/><stop offset="1" stop-color="#0f6bd6"/></linearGradient></defs><path d="M6 11h20a1.6 1.6 0 0 1 1.6 1.8l-1.3 13A2.4 2.4 0 0 1 23.9 28H8.1a2.4 2.4 0 0 1-2.4-2.2l-1.3-13A1.6 1.6 0 0 1 6 11z" fill="url(#icg-st)"/><path d="M11.5 11.5V9a4.5 4.5 0 0 1 9 0v2.5" fill="none" stroke="#dff1ff" stroke-width="1.8" stroke-linecap="round"/><rect x="12.4" y="15.5" width="7.2" height="7.2" rx="1" fill="#fff"/><rect x="13.6" y="16.7" width="2.2" height="2.2" fill="#3ba7f7"/><rect x="16.2" y="16.7" width="2.2" height="2.2" fill="#7fc9ff"/><rect x="13.6" y="19.3" width="2.2" height="2.2" fill="#7fc9ff"/><rect x="16.2" y="19.3" width="2.2" height="2.2" fill="#3ba7f7"/></svg>',

            // Заметки (Sticky Notes style) — жёлтый блокнот
            'notes' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="icg-n" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffe27a"/><stop offset="1" stop-color="#f8c53d"/></linearGradient></defs><path d="M5 7.5A2.5 2.5 0 0 1 7.5 5h17A2.5 2.5 0 0 1 27 7.5V19L19 27H7.5A2.5 2.5 0 0 1 5 24.5z" fill="url(#icg-n)"/><path d="M27 19h-5.5a2.5 2.5 0 0 0-2.5 2.5V27z" fill="#d9a52c"/><g stroke="#b8860b" stroke-width="1.5" stroke-linecap="round"><line x1="9.5" y1="11" x2="22" y2="11"/><line x1="9.5" y1="15" x2="22" y2="15"/><line x1="9.5" y1="19" x2="16" y2="19"/></g></svg>',

            // Консоль (Terminal style) — тёмное окно с >_
            'terminal' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="5" width="26" height="22" rx="3" fill="#1b1f27"/><rect x="3" y="5" width="26" height="5" rx="3" fill="#2a3140"/><rect x="3" y="8" width="26" height="2" fill="#2a3140"/><circle cx="7" cy="7.8" r="1" fill="#4cc2ff"/><path d="M8 15.5l4 3-4 3" fill="none" stroke="#5ee08a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><line x1="14.5" y1="21.8" x2="21" y2="21.8" stroke="#c9d4e3" stroke-width="1.8" stroke-linecap="round"/></svg>',

            // Параметры — шестерёнка Fluent
            'settings' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="icg-g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#7db6e8"/><stop offset="1" stop-color="#3d7ab8"/></linearGradient></defs><path fill-rule="evenodd" fill="url(#icg-g)" d="M13.2 3.6c.4-.4 1-.6 1.5-.6h2.6c.6 0 1.2.2 1.5.6l1 1c.3.3.7.5 1.1.4l1.3-.3c.6-.1 1.2 0 1.6.4l1.9 1.9c.4.4.5 1 .4 1.6l-.3 1.3c-.1.4 0 .8.4 1.1l1 1c.4.3.6.9.6 1.5v2.6c0 .6-.2 1.2-.6 1.5l-1 1c-.4.3-.5.7-.4 1.1l.3 1.3c.1.6 0 1.2-.4 1.6l-1.9 1.9c-.4.4-1 .5-1.6.4l-1.3-.3c-.4-.1-.8 0-1.1.4l-1 1c-.3.4-.9.6-1.5.6h-2.6c-.6 0-1.2-.2-1.5-.6l-1-1c-.3-.4-.7-.5-1.1-.4l-1.3.3c-.6.1-1.2 0-1.6-.4l-1.9-1.9c-.4-.4-.5-1-.4-1.6l.3-1.3c.1-.4 0-.8-.4-1.1l-1-1c-.4-.3-.6-.9-.6-1.5v-2.6c0-.6.2-1.2.6-1.5l1-1c.4-.3.5-.7.4-1.1l-.3-1.3c-.1-.6 0-1.2.4-1.6l1.9-1.9c.4-.4 1-.5 1.6-.4l1.3.3c.4.1.8-.1 1.1-.4zM16 11.5a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9z"/></svg>',

            // Поиск — лупа
            'search' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><circle cx="14" cy="14" r="8.5" fill="none" stroke="#3f6fb5" stroke-width="2.6"/><line x1="20.5" y1="20.5" x2="27" y2="27" stroke="#3f6fb5" stroke-width="3" stroke-linecap="round"/></svg>',

            // Домашняя страница
            'home' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M16 4 4 15h3v12h7v-7h4v7h7V15h3z" fill="none" stroke="#3f6fb5" stroke-width="2.2" stroke-linejoin="round"/></svg>',

            // Галерея / фотографии
            'photos' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="icg-p" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#5ec8ff"/><stop offset="1" stop-color="#2a7de0"/></linearGradient></defs><rect x="3.5" y="6" width="25" height="20" rx="3" fill="url(#icg-p)"/><circle cx="11" cy="12.5" r="2.4" fill="#ffe27a"/><path d="M6 23.5 13 16l4.5 4.5L21 17l5 6.5z" fill="#eaf6ff"/><rect x="3.5" y="6" width="25" height="20" rx="3" fill="none" stroke="#fff" stroke-opacity="0.35" stroke-width="1.4"/></svg>',

            // Калькулятор
            'calc' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><rect x="6" y="3" width="20" height="26" rx="3" fill="#e8ecf2"/><rect x="8.5" y="5.5" width="15" height="6" rx="1.5" fill="#1b2430"/><g fill="#5b6470"><rect x="8.5" y="14" width="4.4" height="3.6" rx="1"/><rect x="13.8" y="14" width="4.4" height="3.6" rx="1"/><rect x="19.1" y="14" width="4.4" height="3.6" rx="1"/><rect x="8.5" y="19" width="4.4" height="3.6" rx="1"/><rect x="13.8" y="19" width="4.4" height="3.6" rx="1"/><rect x="8.5" y="24" width="9.7" height="3.6" rx="1"/><rect x="19.1" y="19" width="4.4" height="8.6" rx="1" fill="#3ba7f7"/></g></svg>',

            // Браузер (Edge style)
            'edge' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="icg-e" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="#0f6bd6"/><stop offset="1" stop-color="#37c2e8"/></linearGradient></defs><circle cx="16" cy="16" r="13" fill="url(#icg-e)"/><path d="M6 19.5C4.8 13 9 5.8 16.5 6c6.4.2 9.3 4.4 9.4 7.6h-9.7c-.4-3-5.6-3.6-8.1-.7-2.7 3-2.4 8.5 1.4 10.9-3.2-.6-5.6-2.7-3.9-4.3z" fill="#eafcff" opacity="0.9"/><path d="M26.5 17c1.6 5.6-2.4 11.4-9.3 11.9-5.8.4-9.9-2.6-11.2-5.5 2.2 2.3 6.5 3.4 9.9 1.8 3.6-1.7 4.6-5 4.2-7.2z" fill="#1c8ce0"/></svg>',

            // Wi-Fi
            'wifi' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="#e6edf6" stroke-width="2.4" stroke-linecap="round"><path d="M4 13.5a17 17 0 0 1 24 0"/><path d="M8.5 18.5a10.5 10.5 0 0 1 15 0"/><path d="M12.7 23.3a4.5 4.5 0 0 1 6.6 0"/></g><circle cx="16" cy="27" r="1.8" fill="#e6edf6"/></svg>',

            // Громкость
            'volume' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M6 12h4l6-5v18l-6-5H6z" fill="#e6edf6"/><g fill="none" stroke="#e6edf6" stroke-width="2" stroke-linecap="round"><path d="M19.5 12.5a5 5 0 0 1 0 7"/><path d="M22.5 9.5a9 9 0 0 1 0 13"/></g></svg>',

            // Батарея
            'battery' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="10.5" width="23" height="11" rx="2.5" fill="none" stroke="#e6edf6" stroke-width="2"/><rect x="27" y="13.5" width="2.5" height="5" rx="1.2" fill="#e6edf6"/><rect x="5.5" y="13" width="14" height="6" rx="1.2" fill="#5ee08a"/></svg>',

            // Профиль пользователя
            'user' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="icg-u" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#4cc2ff"/><stop offset="1" stop-color="#1c64d2"/></linearGradient></defs><circle cx="16" cy="16" r="14" fill="url(#icg-u)"/><circle cx="16" cy="12.5" r="4.6" fill="#fff"/><path d="M7.5 25.5a9 9 0 0 1 17 0" fill="#fff"/></svg>',

            // Питание
            'power' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="#e6edf6" stroke-width="2.4" stroke-linecap="round"><path d="M22.5 8.5a9.5 9.5 0 1 1-13 0"/><line x1="16" y1="4" x2="16" y2="15"/></g></svg>',

            // Справка (?)
            'help' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><circle cx="16" cy="16" r="12.5" fill="none" stroke="#e6edf6" stroke-width="2.2"/><path d="M12.5 12.5a3.6 3.6 0 1 1 5.4 3.1c-1.1.7-1.9 1.3-1.9 2.6" fill="none" stroke="#e6edf6" stroke-width="2.2" stroke-linecap="round"/><circle cx="16" cy="22.5" r="1.5" fill="#e6edf6"/></svg>',

            // Уведомления (колокольчик)
            'bell' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M16 5a7 7 0 0 1 7 7v4.5l2 3.5H7l2-3.5V12a7 7 0 0 1 7-7z" fill="none" stroke="#e6edf6" stroke-width="2.2" stroke-linejoin="round"/><path d="M13 23.5a3 3 0 0 0 6 0" fill="none" stroke="#e6edf6" stroke-width="2.2" stroke-linecap="round"/></svg>',

            // Календарь
            'calendar' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><rect x="4" y="6" width="24" height="22" rx="3" fill="#fff"/><rect x="4" y="6" width="24" height="6.5" rx="3" fill="#d93a3a"/><rect x="4" y="10" width="24" height="2.5" fill="#d93a3a"/><g fill="#5b6470"><rect x="8" y="15.5" width="4" height="4" rx="1"/><rect x="14" y="15.5" width="4" height="4" rx="1"/><rect x="20" y="15.5" width="4" height="4" rx="1"/><rect x="8" y="21.5" width="4" height="4" rx="1"/><rect x="14" y="21.5" width="4" height="4" rx="1"/></g><rect x="20" y="21.5" width="4" height="4" rx="1" fill="#3ba7f7"/></svg>',

            // Файл текстовый
            'file' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M7 4.5A2.5 2.5 0 0 1 9.5 2h9.7L26 9.3V27.5a2.5 2.5 0 0 1-2.5 2.5h-14A2.5 2.5 0 0 1 7 27.5z" fill="#f3f6fa"/><path d="M19.2 2 26 9.3h-5.1a1.7 1.7 0 0 1-1.7-1.7z" fill="#c9d4e3"/><g stroke="#8a97a8" stroke-width="1.6" stroke-linecap="round"><line x1="11" y1="15" x2="21" y2="15"/><line x1="11" y1="19.5" x2="21" y2="19.5"/><line x1="11" y1="24" x2="17" y2="24"/></g></svg>',

            // Папка (в списках)
            'folder' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="icg-fl" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffd54f"/><stop offset="1" stop-color="#f2a91f"/></linearGradient></defs><path d="M3 8.5A2.5 2.5 0 0 1 5.5 6h6.2c1 0 1.9.5 2.4 1.4l1.2 1.9H26.5A2.5 2.5 0 0 1 29 11.8V24a2.5 2.5 0 0 1-2.5 2.5h-21A2.5 2.5 0 0 1 3 24z" fill="url(#icg-fl)"/></svg>',

            // Быстрое действие: Wi-Fi мини
            'q_wifi' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 9a13 13 0 0 1 18 0"/><path d="M6.5 12.8a8 8 0 0 1 11 0"/><path d="M9.8 16.4a3.8 3.8 0 0 1 4.4 0"/></g><circle cx="12" cy="19.4" r="1.4" fill="currentColor"/></svg>',

            // Быстрое действие: Bluetooth
            'q_bt' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M8 6.5 16 13l-4 3.5V7.5l4 3.5L8 17.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 3v18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" transform="translate(0 -0.5)"/></svg>',

            // Быстрое действие: Не беспокоить (луна)
            'q_dnd' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 8.5 8.5 0 1 0 20 14.5z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',

            // Быстрое действие: Экономия заряда
            'q_battery' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="2.5" y="8" width="16" height="8" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><rect x="19.5" y="10.5" width="2" height="3" rx="1" fill="currentColor"/><path d="M10.8 9.6 8 12.6h2.6l-.6 2.4 3-3.2h-2.6z" fill="currentColor"/></svg>',

            // Быстрое действие: Тема (контраст)
            'q_theme' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M12 3.5a8.5 8.5 0 0 1 0 17z" fill="currentColor"/></svg>',

            // Быстрое действие: Проектор
            'q_projector' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="2.5" y="5" width="19" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="8" cy="11" r="2.6" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M15 8.5h4M15 12h4M7 19.5h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',

            // Стрелки
            'chevron_right' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m9 5 7 7-7 7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'back' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15 5 8 12l7 7M8 12h11" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'close' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 6 18 18M18 6 6 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
            'minimize' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 12h12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
            'maximize' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="6.5" y="6.5" width="11" height="11" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>',
            'restore' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="8" width="10" height="10" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M8 8V6.5A1.5 1.5 0 0 1 9.5 5H17a2 2 0 0 1 2 2v7.5A1.5 1.5 0 0 1 17.5 16H16" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>',
            'pin' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 21s-6-5.3-6-10a6 6 0 1 1 12 0c0 4.7-6 10-6 10z" fill="none" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="11" r="2.2" fill="currentColor"/></svg>',

            // Дисплей (монитор) — для Параметры > Система
            'display' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="2.5" y="4" width="19" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M8 20.5h8M12 17v3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',

            // Обновить (стрелка-кольцо)
            'refresh' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.65 6.35A8 8 0 1 0 19.73 14h-2.08a6 6 0 1 1-1.4-6.95L13 10h7V3z" fill="currentColor"/></svg>',

            // Вид (глаз)
            'view' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3.2" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>',
            'all_apps' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g fill="currentColor"><rect x="4" y="4" width="6.5" height="6.5" rx="1.4"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.4"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.4"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.4"/></g></svg>',
        ];
    }
    return isset($icons[$name]) ? $icons[$name] : '';
}

// Отдать все иконки как JS-объект (для workspace.js)
function ic_js_map(): string {
    $names = ['start','explorer','computer','trash','trash_full','market','notes','terminal','settings','search','home','photos','calc','edge','wifi','volume','battery','user','power','help','bell','calendar','file','folder','q_wifi','q_bt','q_dnd','q_battery','q_theme','q_projector','chevron_right','back','close','minimize','maximize','restore','pin','all_apps','display','refresh','view'];
    $parts = [];
    foreach ($names as $n) {
        $parts[] = json_encode($n, JSON_UNESCAPED_UNICODE) . ':' . json_encode(ic_svg($n), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    return '{' . implode(',', $parts) . '}';
}
?>
