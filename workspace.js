/* ============================================================
   ICOS — ядро рабочего стола в стиле Windows 11
   Файловая система, окна (drag/maximize/snap), Пуск, taskbar,
   центр уведомлений, приложения: Проводник, Заметки (вкладки),
   Терминал, Магазин, Параметры, Браузер, Поиск, Корзина.
   ============================================================ */
const OS = (function () {
    const I = window.ICONS || {};
    let zIndexCounter = 100;
    const openWindows = {};

    // ================= ФАЙЛОВАЯ СИСТЕМА =================
    function initFS() {
        if (!localStorage.getItem('icos_fs')) {
            const user = localStorage.getItem('icos_user') || 'User';
            const initialFS = [
                { id: 'root', name: 'Локальный диск (C:)', type: 'drive', parent: null },
                { id: 'f1', name: 'Программы', type: 'folder', parent: 'root' },
                { id: 'f2', name: 'ICOS', type: 'folder', parent: 'root' },
                { id: 'f3', name: 'Пользователи', type: 'folder', parent: 'root' },
                { id: 'u1', name: user, type: 'folder', parent: 'f3' },
                { id: 'f4', name: 'Документы', type: 'folder', parent: 'u1' },
                { id: 'f5', name: 'Загрузки', type: 'folder', parent: 'u1' },
                { id: 'f6', name: 'Рабочий стол', type: 'folder', parent: 'u1' },
                { id: 'd1', name: 'Добро пожаловать.txt', type: 'file', parent: 'f4', content: 'Добро пожаловать в ICOS!\n\nЭто ваша папка Документы. Здесь можно создавать файлы и папки (правая кнопка мыши).' },
            ];
            localStorage.setItem('icos_fs', JSON.stringify(initialFS));
        }
        if (!localStorage.getItem('icos_trash')) localStorage.setItem('icos_trash', JSON.stringify([]));
    }
    const getFS = () => JSON.parse(localStorage.getItem('icos_fs') || '[]');
    const saveFS = fs => localStorage.setItem('icos_fs', JSON.stringify(fs));
    const getTrash = () => JSON.parse(localStorage.getItem('icos_trash') || '[]');
    const saveTrash = t => localStorage.setItem('icos_trash', JSON.stringify(t));

    function createItem(name, type, parent, content = "") {
        const fs = getFS();
        const id = 'item_' + Date.now() + '_' + Math.floor(Math.random() * 999);
        fs.push({ id, name, type, parent, content });
        saveFS(fs);
        refreshTrashIcon();
        return id;
    }
    function deleteItem(id, toTrash = true) {
        let fs = getFS();
        const item = fs.find(i => i.id === id);
        if (!item) return;
        if (toTrash) { const t = getTrash(); t.push(item); saveTrash(t); }
        (function remove(pid) {
            fs.filter(i => i.parent === pid).forEach(c => remove(c.id));
            fs = fs.filter(i => i.id !== pid);
        })(id);
        saveFS(fs);
        refreshTrashIcon();
    }
    function pathOf(id) {
        const fs = getFS();
        const parts = [];
        let cur = fs.find(i => i.id === id);
        while (cur) { parts.unshift(cur.name); cur = fs.find(i => i.id === cur.parent); }
        return parts.join('\\');
    }
    function refreshTrashIcon() {
        const el = document.getElementById('trash-icon');
        if (el) el.innerHTML = getTrash().length ? I.trash_full : I.trash;
    }

    // ================= УВЕДОМЛЕНИЯ / ТОСТЫ =================
    function toast(title, text) {
        const t = document.createElement('div');
        t.className = 'toast';
        t.innerHTML = `<b>${title}</b><span>${text}</span>`;
        document.body.appendChild(t);
        addNotifToCenter(title, text);
        setTimeout(() => { t.classList.add('out'); setTimeout(() => t.remove(), 300); }, 4200);
    }
    function addNotifToCenter(title, text) {
        const list = document.getElementById('ac-notif-list');
        if (!list) return;
        const empty = list.querySelector('.ac-empty');
        if (empty) empty.remove();
        const n = document.createElement('div');
        n.className = 'ac-notif';
        n.innerHTML = `<b>${title}</b><span>${text}</span>`;
        list.prepend(n);
    }

    // ================= ЯДРО =================
    const APPS = {
        computer: { title: 'Проводник', icon: 'explorer', w: 860, h: 540 },
        pc:       { title: 'Этот компьютер', icon: 'computer', w: 860, h: 540 },
        trash:    { title: 'Корзина', icon: 'trash', w: 700, h: 460 },
        notes:    { title: 'Заметки', icon: 'notes', w: 640, h: 500 },
        cmd:      { title: 'Терминал', icon: 'terminal', w: 700, h: 440 },
        market:   { title: 'ICMarket', icon: 'market', w: 820, h: 560 },
        settings: { title: 'Параметры', icon: 'settings', w: 860, h: 560 },
        edge:     { title: 'IC Browser', icon: 'edge', w: 900, h: 580 },
        search:   { title: 'Поиск', icon: 'search', w: 640, h: 520 },
        calc:     { title: 'Калькулятор', icon: 'calc', w: 340, h: 500 },
    };

    function init() {
        initFS();
        refreshTrashIcon();

        // Иконки рабочего стола: одинарный клик — выбор, двойной — запуск
        document.querySelectorAll('#desktop .icon').forEach(icon => {
            icon.setAttribute('tabindex', '0');
            icon.addEventListener('dblclick', () => openApp(icon.dataset.app));
            icon.addEventListener('keydown', e => { if (e.key === 'Enter') openApp(icon.dataset.app); });
            icon.addEventListener('mousedown', e => e.stopPropagation());
            icon.addEventListener('click', e => {
                document.querySelectorAll('#desktop .icon').forEach(i => i.classList.remove('active'));
                icon.classList.add('active');
            });
        });

        // Клик по рабочему столу снимает выделение и закрывает меню
        document.getElementById('desktop').addEventListener('mousedown', () => {
            document.querySelectorAll('#desktop .icon').forEach(i => i.classList.remove('active'));
            closePopups();
        });

        // Правый клик на столе — контекстное меню Win11
        document.getElementById('desktop').addEventListener('contextmenu', e => {
            e.preventDefault();
            showContextMenu(e.clientX, e.clientY, [
                { icon: I.view, label: 'Вид', submenu: true },
                { icon: I.all_apps, label: 'Сортировка', submenu: true },
                { icon: I.refresh ?? '', label: 'Обновить', action: () => location.reload(), raw: '<svg viewBox="0 0 24 24"><path d="M17.65 6.35A8 8 0 1 0 19.73 14h-2.08a6 6 0 1 1-1.4-6.95L13 10h7V3z" fill="currentColor"/></svg>' },
                { sep: true },
                { icon: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6z" fill="currentColor"/></svg>', label: 'Создать', submenu: true, action: () => newCreateMenu(e.clientX, e.clientY) },
                { icon: I.display ?? I.settings, label: 'Параметры экрана', action: () => openApp('settings') },
                { sep: true },
                { icon: I.terminal, label: 'Открыть в терминале', action: () => openApp('cmd') },
            ]);
        });

        // Панель задач
        document.getElementById('start-btn').addEventListener('click', e => { e.stopPropagation(); toggleStart(); });
        document.querySelectorAll('.tb-btn[data-app]').forEach(b => {
            b.addEventListener('click', () => {
                const app = b.dataset.app;
                if (app === 'search') openApp('search');
                else openApp(app);
            });
        });

        // Tray
        document.getElementById('tray-group').addEventListener('click', e => { e.stopPropagation(); toggleActionCenter(); closeCalendar(); });
        document.getElementById('tray-clock').addEventListener('click', e => { e.stopPropagation(); toggleCalendar(); closeActionCenter(); });
        document.getElementById('tray-chevron').addEventListener('click', e => {
            e.stopPropagation();
            document.getElementById('tray-hidden').classList.toggle('open');
        });
        document.getElementById('show-desktop').addEventListener('click', minimizeAll);

        // Меню Пуск
        buildStartMenu();
        document.getElementById('sm-power-btn').addEventListener('click', e => {
            e.stopPropagation();
            showPowerMenu();
        });
        document.getElementById('start-search-input').addEventListener('input', function () {
            filterStart(this.value);
        });
        document.getElementById('sm-allapps-btn').addEventListener('click', () => openApp('search'));

        // Центр уведомлений
        buildQuickActions();
        document.getElementById('ac-clear').addEventListener('click', () => {
            document.getElementById('ac-notif-list').innerHTML = '<div class="ac-empty">Новых уведомлений нет</div>';
        });
        document.getElementById('ac-settings-btn').addEventListener('click', () => { closeActionCenter(); openApp('settings'); });
        document.getElementById('vol-slider').addEventListener('input', function () {
            document.getElementById('vol-value').textContent = this.value;
        });
        document.getElementById('bri-slider').addEventListener('input', function () {
            document.getElementById('bri-value').textContent = this.value + '%';
            ensureDim().style.opacity = String((100 - this.value) / 130);
        });

        // Закрытие всплывающих окон по клику вне
        document.addEventListener('mousedown', e => {
            const sm = document.getElementById('start-menu');
            if (sm.classList.contains('active') && !sm.contains(e.target) && !document.getElementById('start-btn').contains(e.target)) toggleStart(false);
            const ac = document.getElementById('action-center');
            if (ac.classList.contains('active') && !ac.contains(e.target) && !document.getElementById('tray-group').contains(e.target)) closeActionCenter();
            const cf = document.getElementById('calendar-flyout');
            if (cf.classList.contains('active') && !cf.contains(e.target) && !document.getElementById('tray-clock').contains(e.target)) closeCalendar();
            document.querySelectorAll('.power-menu, .context-menu, .tray-hidden').forEach(m => {
                if (!m.contains(e.target)) m.remove();
            });
            if (!e.target.closest('.tray-chevron')) document.getElementById('tray-hidden').classList.remove('open');
        });

        // Клавиатура
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') { toggleStart(false); closeActionCenter(); closeCalendar(); document.querySelectorAll('.context-menu,.power-menu').forEach(m => m.remove()); }
        });

        setInterval(updateClock, 1000);
        updateClock();

        // Имя пользователя в Пуске
        const user = localStorage.getItem('icos_user') || 'Пользователь';
        document.getElementById('sm-username').textContent = user;

        // Приветствие при первом входе
        if (!sessionStorage.getItem('icos_welcomed')) {
            sessionStorage.setItem('icos_welcomed', '1');
            setTimeout(() => toast('ICOS 11', `Добро пожаловать, ${user}! Нажмите Пуск или правую кнопку мыши на столе.`), 1200);
        }
    }

    function ensureDim() {
        let d = document.getElementById('dim-overlay');
        if (!d) {
            d = document.createElement('div');
            d.id = 'dim-overlay';
            document.body.appendChild(d);
        }
        return d;
    }

    function updateClock() {
        const now = new Date();
        const p = n => String(n).padStart(2, '0');
        document.getElementById('tray-time').textContent = p(now.getHours()) + ':' + p(now.getMinutes());
        document.getElementById('tray-date').textContent = p(now.getDate()) + '.' + p(now.getMonth() + 1) + '.' + now.getFullYear();
        const cfT = document.getElementById('cf-time'), cfD = document.getElementById('cf-date');
        if (cfT) {
            cfT.textContent = p(now.getHours()) + ':' + p(now.getMinutes()) + ':' + p(now.getSeconds());
            cfD.textContent = now.toLocaleDateString('ru-RU', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }
    }

    // ================= ВСПлывающие окна =================
    function closePopups() {
        toggleStart(false); closeActionCenter(); closeCalendar();
        document.querySelectorAll('.context-menu,.power-menu,.tray-hidden.open').forEach(m => m.classList ? m.classList.remove('open') : 0);
        document.querySelectorAll('.context-menu,.power-menu').forEach(m => m.remove());
    }
    function toggleStart(force) {
        const sm = document.getElementById('start-menu');
        const on = force !== undefined ? force : !sm.classList.contains('active');
        sm.classList.toggle('active', on);
        if (on) { closeActionCenter(); closeCalendar(); setTimeout(() => document.getElementById('start-search-input').focus(), 60); }
    }
    function toggleActionCenter() {
        const ac = document.getElementById('action-center');
        ac.classList.toggle('active');
        if (ac.classList.contains('active')) closeCalendar();
    }
    function closeActionCenter() { document.getElementById('action-center').classList.remove('active'); }
    function toggleCalendar() {
        const cf = document.getElementById('calendar-flyout');
        buildCalendar();
        cf.classList.toggle('active');
        if (cf.classList.contains('active')) closeActionCenter();
    }
    function closeCalendar() { document.getElementById('calendar-flyout').classList.remove('active'); }

    function buildCalendar() {
        const grid = document.getElementById('cf-grid');
        const now = new Date();
        const y = now.getFullYear(), m = now.getMonth();
        const months = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
        let html = `<div class="cf-month-title">${months[m]} ${y}</div>`;
        ['пн','вт','ср','чт','пт','сб','вс'].forEach(d => html += `<div class="cf-dow">${d}</div>`);
        const first = new Date(y, m, 1);
        let startDow = (first.getDay() + 6) % 7;
        const daysInMonth = new Date(y, m + 1, 0).getDate();
        const prevDays = new Date(y, m, 0).getDate();
        for (let i = 0; i < 42; i++) {
            const dnum = i - startDow + 1;
            if (dnum < 1) html += `<div class="cf-day other">${prevDays + dnum}</div>`;
            else if (dnum > daysInMonth) html += `<div class="cf-day other">${dnum - daysInMonth}</div>`;
            else html += `<div class="cf-day${dnum === now.getDate() ? ' today' : ''}">${dnum}</div>`;
            if (i >= startDow + daysInMonth - 1 && (i + 1) % 7 === 0) break;
        }
        grid.innerHTML = html;
    }

    function buildQuickActions() {
        const qa = document.getElementById('ac-quick');
        const tiles = [
            { key: 'wifi', icon: I.q_wifi, label: 'Wi-Fi', on: true },
            { key: 'bt', icon: I.q_bt, label: 'Bluetooth', on: false },
            { key: 'dnd', icon: I.q_dnd, label: 'Не беспокоить', on: false },
            { key: 'battery', icon: I.q_battery, label: 'Экономия', on: false },
            { key: 'theme', icon: I.q_theme, label: 'Контраст', on: false },
            { key: 'projector', icon: I.q_projector, label: 'Проектор', on: false },
        ];
        const saved = JSON.parse(localStorage.getItem('icos_qa') || '{}');
        qa.innerHTML = '';
        tiles.forEach(t => {
            const on = t.key in saved ? saved[t.key] : t.on;
            const b = document.createElement('button');
            b.className = 'qa-tile' + (on ? ' on' : '');
            b.innerHTML = t.icon + `<span>${t.label}</span>`;
            b.addEventListener('click', () => {
                const nowOn = !b.classList.contains('on');
                b.classList.toggle('on', nowOn);
                const s = JSON.parse(localStorage.getItem('icos_qa') || '{}');
                s[t.key] = nowOn; localStorage.setItem('icos_qa', JSON.stringify(s));
            });
            qa.appendChild(b);
        });
    }

    function showPowerMenu() {
        document.querySelectorAll('.power-menu').forEach(m => m.remove());
        const menu = document.createElement('div');
        menu.className = 'power-menu';
        const items = [
            { label: 'Спящий режим', act: () => sleepScreen() },
            { label: 'Завершение работы', act: () => shutdownFlow('Завершение работы...', 'shutdown') },
            { label: 'Перезагрузка', act: () => shutdownFlow('Перезагрузка...', 'restart') },
            { label: 'Выйти из системы', act: () => shutdownFlow('Выход...', 'logout') },
        ];
        items.forEach(it => {
            const d = document.createElement('div');
            d.textContent = it.label;
            d.onclick = e => { e.stopPropagation(); menu.remove(); it.act(); };
            menu.appendChild(d);
        });
        document.getElementById('start-menu').appendChild(menu);
    }
    function sleepScreen() {
        const ov = document.createElement('div');
        ov.style.cssText = 'position:fixed;inset:0;background:#000;z-index:10001;cursor:pointer;';
        ov.onclick = () => ov.remove();
        ov.title = 'Кликните, чтобы выйти из спящего режима';
        document.body.appendChild(ov);
    }
    function shutdownFlow(text, mode) {
        const s = document.getElementById('shutdown-screen');
        document.getElementById('sd-text').textContent = text;
        s.classList.remove('hidden');
        setTimeout(() => {
            if (mode === 'logout') location.href = 'password.php';
            else if (mode === 'restart') location.href = 'boot.php';
            else { document.getElementById('sd-text').textContent = 'Питание отключено'; }
        }, 2500);
    }

    // ================= КОНТЕКСТНОЕ МЕНЮ =================
    function showContextMenu(x, y, items) {
        document.querySelectorAll('.context-menu').forEach(m => m.remove());
        const menu = document.createElement('div');
        menu.className = 'context-menu';
        items.forEach(item => {
            if (item.sep) { const s = document.createElement('div'); s.className = 'cm-sep'; menu.appendChild(s); return; }
            const d = document.createElement('div');
            d.className = 'cm-item';
            d.innerHTML = (item.raw || item.icon || '') + `<span>${item.label}</span>`;
            d.onclick = e => { e.stopPropagation(); if (item.action) item.action(); menu.remove(); };
            menu.appendChild(d);
        });
        document.body.appendChild(menu);
        const r = menu.getBoundingClientRect();
        menu.style.left = Math.min(x, window.innerWidth - r.width - 8) + 'px';
        menu.style.top = Math.min(y, window.innerHeight - r.height - 60) + 'px';
    }
    function newCreateMenu(x, y) {
        showContextMenu(x, y - 40, [
            { label: 'Папку', action: () => desktopPromptCreate('folder') },
            { label: 'Текстовый документ', action: () => desktopPromptCreate('file') },
        ]);
    }
    function desktopPromptCreate(type) {
        const name = prompt(type === 'folder' ? 'Имя новой папки:' : 'Имя нового файла:', type === 'folder' ? 'Новая папка' : 'Новый документ.txt');
        if (name) { createItem(name, type, 'f6', ''); toast('Создано', `«${name}» добавлен(а) на рабочий стол`); }
    }

    // ================= ОКНА =================
    function openApp(appId, args = {}) {
        if (appId === 'pc') appId = 'computer';
        const meta = APPS[appId];
        if (!meta) return;

        if (openWindows[appId]) {
            const w = openWindows[appId];
            if (w.element.style.display === 'none') w.element.style.display = 'flex';
            if (appId === 'computer' && args.path) navigateExplorer(w.element, args.path);
            if (appId === 'notes' && args.fileId) notesOpenFile(w.element, args.fileId);
            bringToFront(w.element);
            syncTaskbar();
            return;
        }

        const win = document.createElement('div');
        win.className = 'window';
        const width = Math.min(meta.w, window.innerWidth - 40);
        const height = Math.min(meta.h, window.innerHeight - 100);
        win.style.width = width + 'px'; win.style.height = height + 'px';
        const cascade = Object.keys(openWindows).length * 24;
        win.style.left = Math.max(10, (window.innerWidth - width) / 2 + cascade - 40) + 'px';
        win.style.top = Math.max(10, (window.innerHeight - height) / 2 + cascade - 60) + 'px';
        win.style.zIndex = ++zIndexCounter;

        win.innerHTML = `
            <div class="window-header">
                <div class="window-title"><span class="wt-icon">${I[meta.icon] || ''}</span>${meta.title}</div>
                <div class="window-controls">
                    <button class="minimize" title="Свернуть">${I.minimize}</button>
                    <button class="maximize" title="Развернуть">${I.maximize}</button>
                    <button class="close" title="Закрыть">${I.close}</button>
                </div>
            </div>
            <div class="window-content"></div>`;

        document.getElementById('windows-layer').appendChild(win);
        openWindows[appId] = { element: win, meta };

        setupWindowEvents(win, appId);
        addToTaskbar(appId);
        bringToFront(win);

        const content = win.querySelector('.window-content');
        if (appId === 'computer') renderExplorer(win, content, args.path || 'root');
        if (appId === 'trash') renderTrash(win, content);
        if (appId === 'notes') renderNotes(win, content, args.fileId);
        if (appId === 'cmd') renderTerminal(win, content);
        if (appId === 'market') renderMarket(win, content);
        if (appId === 'settings') renderSettings(win, content);
        if (appId === 'edge') renderBrowser(win, content);
        if (appId === 'search') renderSearch(win, content);
        if (appId === 'calc') renderCalc(win, content);
    }

    function addToTaskbar(appId) {
        if (document.getElementById('task-' + appId)) return;
        const meta = APPS[appId];
        const btn = document.createElement('div');
        btn.className = 'taskbar-app';
        btn.id = 'task-' + appId;
        btn.title = meta.title;
        btn.innerHTML = I[meta.icon] || '';
        btn.addEventListener('click', () => {
            const w = openWindows[appId];
            if (!w) return;
            if (w.element.style.display === 'none') { w.element.style.display = 'flex'; bringToFront(w.element); }
            else if (w.element.classList.contains('inactive')) bringToFront(w.element);
            else minimizeWin(appId);
            syncTaskbar();
        });
        document.getElementById('taskbar-apps').appendChild(btn);
    }

    function bringToFront(win) {
        win.style.zIndex = ++zIndexCounter;
        document.querySelectorAll('.window').forEach(w => w.classList.add('inactive'));
        win.classList.remove('inactive');
        syncTaskbar();
    }
    function minimizeWin(appId) {
        const w = openWindows[appId];
        if (!w) return;
        w.element.style.display = 'none';
        w.minimized = true;
        syncTaskbar();
    }
    function minimizeAll() {
        Object.keys(openWindows).forEach(minimizeWin);
    }
    function syncTaskbar() {
        let topWin = null, topZ = -1;
        Object.values(openWindows).forEach(w => {
            const z = parseInt(w.element.style.zIndex || '0');
            if (w.element.style.display !== 'none' && z > topZ) { topZ = z; topWin = w; }
        });
        Object.entries(openWindows).forEach(([id, w]) => {
            const t = document.getElementById('task-' + id);
            if (!t) return;
            t.classList.toggle('active', w === topWin);
            t.classList.toggle('minimized', w.element.style.display === 'none');
        });
    }

    function setupWindowEvents(win, appId) {
        const header = win.querySelector('.window-header');
        let dragging = false, sx, sy, sl, st;

        header.addEventListener('mousedown', e => {
            if (e.target.closest('.window-controls')) return;
            bringToFront(win);
            if (win.dataset.max === 'true') return;
            dragging = true; sx = e.clientX; sy = e.clientY;
            sl = win.offsetLeft; st = win.offsetTop;
            e.preventDefault();
        });
        document.addEventListener('mousemove', e => {
            if (!dragging) return;
            win.style.left = (sl + e.clientX - sx) + 'px';
            win.style.top = Math.max(0, st + e.clientY - sy) + 'px';
            // Snap-подсветка как в Win11
            if (e.clientY <= 4) showSnapHint('top');
            else if (e.clientX <= 4) showSnapHint('left');
            else if (e.clientX >= window.innerWidth - 4) showSnapHint('right');
            else hideSnapHint();
        });
        document.addEventListener('mouseup', e => {
            if (!dragging) return;
            dragging = false;
            const hint = consumeSnapHint();
            if (hint === 'top') maximizeWin(win, true);
            else if (hint === 'left') snapSide(win, 0);
            else if (hint === 'right') snapSide(win, 1);
        });
        header.addEventListener('dblclick', e => {
            if (e.target.closest('.window-controls')) return;
            toggleMaximize(win);
        });

        win.querySelector('.close').addEventListener('click', () => {
            win.classList.add('closing');
            setTimeout(() => {
                win.remove();
                delete openWindows[appId];
                const t = document.getElementById('task-' + appId);
                if (t) t.remove();
                syncTaskbar();
            }, 130);
        });
        win.querySelector('.minimize').addEventListener('click', () => minimizeWin(appId));
        win.querySelector('.maximize').addEventListener('click', () => toggleMaximize(win));
        win.addEventListener('mousedown', () => bringToFront(win));
    }

    let snapHintEl = null, snapPending = null;
    function showSnapHint(pos) {
        snapPending = pos;
        if (!snapHintEl) {
            snapHintEl = document.createElement('div');
            snapHintEl.style.cssText = 'position:fixed;z-index:9600;border-radius:8px;background:rgba(76,194,255,.25);border:2px solid rgba(76,194,255,.6);pointer-events:none;transition:all .1s;display:none;';
            document.body.appendChild(snapHintEl);
        }
        const H = window.innerHeight - 56, W = window.innerWidth;
        const r = { top: [0, 0, W, H], left: [0, 0, W / 2, H], right: [W / 2, 0, W / 2, H] }[pos];
        snapHintEl.style.display = 'block';
        snapHintEl.style.left = r[0] + 'px'; snapHintEl.style.top = r[1] + 'px';
        snapHintEl.style.width = r[2] + 'px'; snapHintEl.style.height = r[3] + 'px';
    }
    function hideSnapHint() { snapPending = null; if (snapHintEl) snapHintEl.style.display = 'none'; }
    function consumeSnapHint() { const p = snapPending; hideSnapHint(); return p; }

    function maximizeWin(win, on) {
        if (on) {
            if (win.dataset.max !== 'true') {
                win.dataset.ow = win.style.width; win.dataset.oh = win.style.height;
                win.dataset.ol = win.style.left; win.dataset.ot = win.style.top;
            }
            win.style.width = '100%'; win.style.height = 'calc(100% - 56px)';
            win.style.left = '0'; win.style.top = '0';
            win.dataset.max = 'true';
            win.classList.add('maximized');
            win.querySelector('.maximize').innerHTML = I.restore;
        } else {
            win.style.width = win.dataset.ow; win.style.height = win.dataset.oh;
            win.style.left = win.dataset.ol; win.style.top = win.dataset.ot;
            win.dataset.max = 'false';
            win.classList.remove('maximized');
            win.querySelector('.maximize').innerHTML = I.maximize;
        }
    }
    function toggleMaximize(win) { maximizeWin(win, win.dataset.max !== 'true'); }
    function snapSide(win, side) {
        if (win.dataset.max !== 'true') {
            win.dataset.ow = win.style.width; win.dataset.oh = win.style.height;
            win.dataset.ol = win.style.left; win.dataset.ot = win.style.top;
        }
        const H = window.innerHeight - 56, W = window.innerWidth;
        win.style.top = '0'; win.style.height = H + 'px'; win.style.width = (W / 2) + 'px';
        win.style.left = side === 0 ? '0' : (W / 2) + 'px';
        win.dataset.max = 'snap';
    }

    // ================= МЕНЮ ПУСК =================
    function buildStartMenu() {
        const pinnedRow = document.getElementById('sm-pinned');
        const recRow = document.getElementById('sm-recommend');
        const pinnedApps = ['computer', 'notes', 'cmd', 'market', 'settings', 'edge', 'search', 'calc', 'trash'];
        pinnedRow.innerHTML = '';
        pinnedApps.forEach(id => {
            const a = APPS[id];
            const b = document.createElement('div');
            b.className = 'sm-app';
            b.dataset.name = a.title.toLowerCase();
            b.innerHTML = `${I[a.icon]}<span>${a.title}</span>`;
            b.addEventListener('click', () => { openApp(id); toggleStart(false); });
            pinnedRow.appendChild(b);
        });
        recRow.innerHTML = '';
        const recs = [
            { app: 'notes', title: 'Заметки', sub: 'Недавно добавлено' },
            { app: 'computer', title: 'Добро пожаловать.txt', sub: 'Документы' },
            { app: 'cmd', title: 'Терминал', sub: 'Недавно использовалось' },
            { app: 'market', title: 'Калькулятор', sub: 'Рекомендуется для вас' },
        ];
        recs.forEach(r => {
            const a = APPS[r.app];
            const d = document.createElement('div');
            d.className = 'sm-rec';
            d.innerHTML = `${I[a.icon]}<div class="rec-txt"><b>${r.title}</b><i>${r.sub}</i></div>`;
            d.addEventListener('click', () => { openApp(r.app); toggleStart(false); });
            recRow.appendChild(d);
        });
    }
    function filterStart(q) {
        q = q.trim().toLowerCase();
        document.querySelectorAll('#sm-pinned .sm-app').forEach(el => {
            el.style.display = !q || el.dataset.name.includes(q) ? '' : 'none';
        });
        document.querySelector('.sm-recommend-head').style.display = q ? 'none' : '';
        document.getElementById('sm-recommend').style.display = q ? 'none' : '';
    }

    // ================= ПРОВОДНИК =================
    function renderExplorer(win, content, startId) {
        win._expHistory = [startId];
        win._expPos = 0;
        content.innerHTML = `
            <div class="explorer">
                <div class="exp-toolbar">
                    <button class="exp-nav-btn exp-back" title="Назад">${I.back}</button>
                    <button class="exp-nav-btn exp-fwd" title="Вперёд" style="rotate:180deg;">${I.back}</button>
                    <button class="exp-nav-btn exp-up" title="Вверх"><svg viewBox="0 0 24 24"><path d="m12 5-7 7 1.4 1.4L11 8.8V19h2V8.8l4.6 4.6L19 12z" fill="currentColor"/></svg></button>
                    <div class="exp-address"></div>
                    <div class="exp-searchbox">${I.search}<span>Поиск</span></div>
                </div>
                <div class="exp-body">
                    <div class="explorer-sidebar"></div>
                    <div class="explorer-main"></div>
                </div>
                <div class="exp-statusbar"><span class="exp-count"></span><span>Элементов: <b class="exp-count2">0</b></span></div>
            </div>`;
        const sidebar = content.querySelector('.explorer-sidebar');
        const user = localStorage.getItem('icos_user') || 'Пользователь';
        sidebar.innerHTML = `
            <div class="sb-item sb-home" data-nav="root">${I.home}<span>Главная</span></div>
            <div class="sb-sep"></div>
            <div class="sb-item" data-nav="root">${I.computer}<span>Этот компьютер</span></div>
            <div class="sb-item" data-nav="f4">${I.folder}<span>Документы</span></div>
            <div class="sb-item" data-nav="f5">${I.folder}<span>Загрузки</span></div>
            <div class="sb-item" data-nav="f6">${I.folder}<span>Рабочий стол</span></div>
            <div class="sb-item" data-nav="__trash">${I.trash}<span>Корзина</span></div>`;
        sidebar.querySelectorAll('[data-nav]').forEach(el => {
            el.addEventListener('click', () => {
                if (el.dataset.nav === '__trash') { openApp('trash'); return; }
                navigateExplorer(win, el.dataset.nav);
            });
        });
        content.querySelector('.exp-back').addEventListener('click', () => expGo(win, -1));
        content.querySelector('.exp-fwd').addEventListener('click', () => expGo(win, 1));
        content.querySelector('.exp-up').addEventListener('click', () => {
            const fs = getFS();
            const cur = fs.find(i => i.id === win._expHistory[win._expPos]);
            if (cur && cur.parent) navigateExplorer(win, cur.parent);
        });
        navigateExplorer(win, startId);
    }
    function expGo(win, dir) {
        const np = win._expPos + dir;
        if (np < 0 || np >= win._expHistory.length) return;
        win._expPos = np;
        drawExplorerDir(win, win._expHistory[np]);
    }
    function navigateExplorer(win, id) {
        win._expHistory = win._expHistory.slice(0, win._expPos + 1);
        if (win._expHistory[win._expPos] !== id) { win._expHistory.push(id); win._expPos++; }
        drawExplorerDir(win, id);
    }
    function drawExplorerDir(win, parentId) {
        const content = win.querySelector('.window-content');
        const main = content.querySelector('.explorer-main');
        const addr = content.querySelector('.exp-address');
        const fs = getFS();
        const items = fs.filter(i => i.parent === parentId);

        addr.innerHTML = `${I.folder}<span>${escapeHtml(pathOf(parentId))}</span>`;
        content.querySelector('.exp-back').disabled = win._expPos <= 0;
        content.querySelector('.exp-fwd').disabled = win._expPos >= win._expHistory.length - 1;
        content.querySelector('.exp-count').textContent = items.length + ' эл.';
        content.querySelector('.exp-count2').textContent = items.length;

        // Подсветка сайдбара
        content.querySelectorAll('.sb-item').forEach(s => s.classList.toggle('active', s.dataset.nav === parentId));

        main.innerHTML = '';
        if (!items.length) {
            main.innerHTML = '<div class="exp-empty">Эта папка пуста</div>';
        }
        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'folder-icon';
            const ic = item.type === 'folder' || item.type === 'drive' ? I.folder : I.file;
            div.innerHTML = `${ic}<span>${escapeHtml(item.name)}</span>`;
            div.addEventListener('click', e => {
                e.stopPropagation();
                main.querySelectorAll('.folder-icon').forEach(f => f.classList.remove('active'));
                div.classList.add('active');
            });
            div.addEventListener('dblclick', () => {
                if (item.type === 'file') openApp('notes', { fileId: item.id });
                else navigateExplorer(win, item.id);
            });
            div.addEventListener('contextmenu', e => {
                e.preventDefault(); e.stopPropagation();
                showContextMenu(e.clientX, e.clientY, [
                    { label: 'Открыть', action: () => item.type === 'file' ? openApp('notes', { fileId: item.id }) : navigateExplorer(win, item.id) },
                    { sep: true },
                    { label: 'Переименовать', action: () => {
                        const nn = prompt('Новое имя:', item.name);
                        if (nn) { const f2 = getFS(); const t = f2.find(i => i.id === item.id); if (t) { t.name = nn; saveFS(f2); drawExplorerDir(win, parentId); } }
                    }},
                    { label: 'Удалить', action: () => {
                        if (confirm(`Переместить «${item.name}» в корзину?`)) { deleteItem(item.id); drawExplorerDir(win, parentId); toast('Корзина', `«${item.name}» перемещён в корзину`); refreshTrashIcon(); }
                    }},
                ]);
            });
            main.appendChild(div);
        });

        main.onmousedown = e => { if (e.target === main) main.querySelectorAll('.folder-icon').forEach(f => f.classList.remove('active')); };
        main.oncontextmenu = e => {
            e.preventDefault();
            showContextMenu(e.clientX, e.clientY, [
                { label: 'Создать папку', action: () => { const n = prompt('Имя папки:', 'Новая папка'); if (n) { createItem(n, 'folder', parentId); drawExplorerDir(win, parentId); } } },
                { label: 'Создать текстовый документ', action: () => { const n = prompt('Имя файла:', 'Новый документ.txt'); if (n) { createItem(n, 'file', parentId, ''); drawExplorerDir(win, parentId); } } },
                { sep: true },
                { label: 'Открыть в терминале', action: () => openApp('cmd') },
            ]);
        };
    }

    // ================= КОРЗИНА =================
    function renderTrash(win, content) {
        content.innerHTML = `
            <div class="explorer">
                <div class="exp-toolbar">
                    <button class="exp-nav-btn tr-empty-btn" style="width:auto;padding:0 12px;font-size:12px;" title="Очистить корзину">Очистить корзину</button>
                </div>
                <div class="exp-body"><div class="explorer-main" id="trash-main"></div></div>
            </div>`;
        content.querySelector('.tr-empty-btn').addEventListener('click', () => {
            if (!getTrash().length) return;
            if (confirm('Удалить все элементы из корзины навсегда?')) {
                saveTrash([]); drawTrash(win); refreshTrashIcon(); toast('Корзина', 'Корзина очищена');
            }
        });
        drawTrash(win);
    }
    function drawTrash(win) {
        const main = win.querySelector('#trash-main');
        main.innerHTML = '';
        const trash = getTrash();
        if (!trash.length) { main.innerHTML = '<div class="exp-empty">Корзина пуста</div>'; return; }
        trash.forEach((item, index) => {
            const div = document.createElement('div');
            div.className = 'folder-icon';
            div.innerHTML = `${item.type === 'file' ? I.file : I.folder}<span>${escapeHtml(item.name)}</span>`;
            div.addEventListener('contextmenu', e => {
                e.preventDefault(); e.stopPropagation();
                showContextMenu(e.clientX, e.clientY, [
                    { label: 'Восстановить', action: () => {
                        const t = getTrash(); const restored = t.splice(index, 1)[0]; saveTrash(t);
                        const fs = getFS();
                        if (!fs.find(i => i.id === restored.parent)) restored.parent = 'root';
                        fs.push(restored); saveFS(fs); drawTrash(win); refreshTrashIcon();
                    }},
                    { label: 'Удалить навсегда', action: () => {
                        if (confirm('Удалить навсегда?')) { const t = getTrash(); t.splice(index, 1); saveTrash(t); drawTrash(win); refreshTrashIcon(); }
                    }},
                ]);
            });
            main.appendChild(div);
        });
    }

    // ================= ЗАМЕТКИ (вкладки, как Notepad Win11) =================
    function renderNotes(win, content, fileId) {
        win._npTabs = [];
        content.innerHTML = `
            <div class="notepad">
                <div class="np-tabs"></div>
                <textarea spellcheck="false"></textarea>
                <div class="np-status"><span class="np-file-name">—</span><span><label>Символы: </label><span class="np-chars">0</span></span></div>
            </div>`;
        const area = content.querySelector('textarea');
        const tabsEl = content.querySelector('.np-tabs');

        function addTab(name, fid) {
            win._npTabs.push({ name, fid });
            drawTabs(); selectTab(win._npTabs.length - 1);
        }
        function drawTabs() {
            tabsEl.innerHTML = '';
            win._npTabs.forEach((t, i) => {
                const tab = document.createElement('div');
                tab.className = 'np-tab' + (i === win._npSel ? '' : ' inactive');
                tab.innerHTML = `<span>${escapeHtml(t.name)}</span><button title="Закрыть">×</button>`;
                tab.addEventListener('click', e => { if (e.target.tagName !== 'BUTTON') selectTab(i); });
                tab.querySelector('button').addEventListener('click', () => {
                    win._npTabs.splice(i, 1);
                    if (win._npSel >= win._npTabs.length) win._npSel = win._npTabs.length - 1;
                    if (!win._npTabs.length) { addTab('Без имени.txt', null); return; }
                    drawTabs(); selectTab(Math.max(0, win._npSel));
                });
                tabsEl.appendChild(tab);
            });
            const plus = document.createElement('button');
            plus.className = 'np-newtab'; plus.textContent = '+'; plus.title = 'Новая вкладка';
            plus.addEventListener('click', () => { win._npSel = win._npTabs.length; addTab('Без имени.txt', null); });
            tabsEl.appendChild(plus);
        }
        function selectTab(i) {
            win._npSel = i;
            const t = win._npTabs[i];
            if (!t) return;
            if (t.fid) {
                const f = getFS().find(x => x.id === t.fid);
                area.value = f ? (f.content || '') : '';
            } else area.value = '';
            content.querySelector('.np-file-name').textContent = t.name;
            updateCount();
            drawTabs();
        }
        function updateCount() { content.querySelector('.np-chars').textContent = area.value.length; }

        area.addEventListener('input', () => {
            const t = win._npTabs[win._npSel];
            if (t && t.fid) {
                const fs = getFS(); const f = fs.find(x => x.id === t.fid);
                if (f) { f.content = area.value; saveFS(fs); }
            }
            updateCount();
        });

        if (fileId) {
            const f = getFS().find(x => x.id === fileId);
            addTab(f ? f.name : 'Без имени.txt', fileId);
        } else {
            addTab('Без имени.txt', null);
        }
    }
    function notesOpenFile(win, fileId) {
        const fs = getFS(); const f = fs.find(x => x.id === fileId);
        if (!f) return;
        const existing = win._npTabs.findIndex(t => t.fid === fileId);
        if (existing >= 0) { win.querySelector('.np-tab') && win._npTabs.forEach; }
        if (existing >= 0) {
            win._npSel = existing;
            win.querySelector('.window-content textarea').value = f.content || '';
        } else {
            win._npSel = win._npTabs.length;
            const tabsEl = win.querySelector('.np-tabs');
            win._npTabs.push({ name: f.name, fid: fileId });
            // перерисовать через внутренний selectTab нельзя — делаем вручную
            const area = win.querySelector('.window-content textarea');
            tabsEl.querySelectorAll('.np-tab').forEach(t => t.classList.add('inactive'));
            area.value = f.content || '';
            win.querySelector('.np-file-name').textContent = f.name;
        }
        bringToFront(win.querySelector('.window'));
    }

    // ================= ТЕРМИНАЛ =================
    function renderTerminal(win, content) {
        content.innerHTML = `
            <div class="cmd-app">
                <div class="term-tabs"><div class="term-tab">${I.terminal}<span>Командная строка</span></div></div>
                <div class="cmd-output"></div>
                <div class="cmd-input-line"><span class="cmd-prompt">C:\\&gt;</span><input type="text" autocomplete="off"></div>
            </div>`;
        const input = content.querySelector('.cmd-input');
        const out = content.querySelector('.cmd-output');
        const promptEl = content.querySelector('.cmd-prompt');
        let currentDir = 'root';

        const updatePrompt = () => {
            const dir = getFS().find(i => i.id === currentDir);
            promptEl.textContent = (dir ? pathOf(currentDir) : 'C:') + '>';
        };
        out.textContent = 'Microsoft ICOS [Version 11.0.22631.icos]\n(c) Корпорация ICOS. Все права защищены.\n\nВведите help для списка команд.\n\n';
        updatePrompt();

        input.addEventListener('keydown', e => {
            if (e.key !== 'Enter') return;
            const line = input.value.trim();
            input.value = '';
            out.textContent += promptEl.textContent + ' ' + line + '\n';
            if (!line) return;
            const args = line.split(/\s+/);
            const cmd = args[0].toLowerCase();
            const fs = getFS();

            if (cmd === 'help') out.textContent += 'Доступные команды:\n  cls, dir, cd, mkdir, type, del, echo, ver, systeminfo, start, date, time\n\n';
            else if (cmd === 'cls') out.textContent = '';
            else if (cmd === 'ver') out.textContent += 'ICOS [Version 11.0.22631.icos]\n\n';
            else if (cmd === 'date') out.textContent += new Date().toLocaleDateString('ru-RU') + '\n\n';
            else if (cmd === 'time') out.textContent += new Date().toLocaleTimeString('ru-RU') + '\n\n';
            else if (cmd === 'systeminfo') out.textContent += `Имя ОС:            ICOS 11 Pro\nВерсия:            11.0.22631\nПользователь:      ${localStorage.getItem('icos_user') || 'User'}\nРегион:            ${localStorage.getItem('icos_region') || 'RU'}\nЯзык:              ${localStorage.getItem('icos_lang') || 'ru'}\nЧасовой пояс:      ${localStorage.getItem('icos_tz') || 'UTC'}\n\n`;
            else if (cmd === 'dir') {
                const children = fs.filter(i => i.parent === currentDir);
                out.textContent += `\n Содержимое папки ${pathOf(currentDir)}\n\n`;
                children.forEach(c => {
                    out.textContent += `01.10.2026  10:00    ${c.type === 'file' ? '         ' : '<DIR>        '} ${c.name}\n`;
                });
                out.textContent += `\n        ${children.length} элемент(ов)\n\n`;
            }
            else if (cmd === 'cd') {
                if (!args[1] || args[1] === '.') { out.textContent += pathOf(currentDir) + '\n\n'; }
                else if (args[1] === '..') { const d = fs.find(i => i.id === currentDir); if (d && d.parent) currentDir = d.parent; updatePrompt(); }
                else {
                    const t = fs.find(i => i.name.toLowerCase() === args[1].toLowerCase() && i.parent === currentDir && i.type !== 'file');
                    if (t) { currentDir = t.id; updatePrompt(); } else out.textContent += 'Не удается найти указанный путь.\n\n';
                }
            }
            else if (cmd === 'mkdir') { if (args[1]) { createItem(args.slice(1).join(' '), 'folder', currentDir); out.textContent += '\n'; } else out.textContent += 'Синтаксис: mkdir <имя>\n\n'; }
            else if (cmd === 'type') {
                const t = fs.find(i => i.name.toLowerCase() === (args[1] || '').toLowerCase() && i.parent === currentDir);
                out.textContent += (t && t.type === 'file') ? (t.content || '') + '\n\n' : 'Не удается найти указанный файл.\n\n';
            }
            else if (cmd === 'del') {
                const t = fs.find(i => i.name.toLowerCase() === (args[1] || '').toLowerCase() && i.parent === currentDir && i.type === 'file');
                if (t) { deleteItem(t.id); out.textContent += '\n'; } else out.textContent += 'Файл не найден.\n\n';
            }
            else if (cmd === 'echo') {
                const full = line.slice(5).trim();
                const parts = full.split('>');
                if (parts.length === 2 && parts[1].trim()) { createItem(parts[1].trim(), 'file', currentDir, parts[0].trim()); out.textContent += '\n'; }
                else out.textContent += full + '\n\n';
            }
            else if (cmd === 'start') {
                const target = (args[1] || '').toLowerCase();
                if (target.includes('notepad') || target === 'notes') openApp('notes');
                else if (target.includes('explorer')) openApp('computer');
                else if (target.includes('store') || target.includes('market')) openApp('market');
                else if (target.includes('ms-settings') || target.includes('settings')) openApp('settings');
                else if (target.includes('browser') || target === 'edge') openApp('edge');
                else out.textContent += 'Не удалось запустить приложение.\n\n';
                out.textContent += '\n';
            }
            else out.textContent += `"${args[0]}" не является внутренней или внешней командой.\n\n`;
            out.scrollTop = out.scrollHeight;
        });
        content.querySelector('.cmd-app').addEventListener('click', () => input.focus());
        setTimeout(() => input.focus(), 100);
    }

    // ================= МАГАЗИН =================
    function renderMarket(win, content) {
        const installed = JSON.parse(localStorage.getItem('icos_installed') || '[]');
        const apps = [
            { id: 'calc', name: 'Калькулятор', dev: 'ICOS Corporation', desc: 'Стандартный, научный и программный режимы.', rate: '★★★★☆ 4.5', icon: 'calc' },
            { id: 'edge', name: 'IC Browser', dev: 'ICOS Corporation', desc: 'Быстрый и безопасный браузер на движке Blink.', rate: '★★★★★ 4.8', icon: 'edge' },
            { id: 'photos', name: 'Фотографии', dev: 'ICOS Corporation', desc: 'Просмотр и редактирование фото, AI-альбомы.', rate: '★★★★☆ 4.2', icon: 'photos' },
            { id: 'music', name: 'Медиаплеер', dev: 'ICOS Corporation', desc: 'Музыка и видео в одном приложении.', rate: '★★★☆☆ 3.9', icon: 'bell' },
            { id: 'clock', name: 'Часы', dev: 'ICOS Corporation', desc: 'Таймеры, будильники, мировые часы.', rate: '★★★★☆ 4.4', icon: 'calendar' },
            { id: 'notes', name: 'Заметки', dev: 'ICOS Corporation', desc: 'Быстрые заметки с вкладками и автосохранением.', rate: '★★★★★ 4.7', icon: 'notes' },
        ];
        content.innerHTML = `
            <div class="market">
                <div class="mk-banner">${I.market}<div><h2>ICMarket</h2><p>Лучшие приложения для ICOS 11 — проверенные и безопасные.</p></div></div>
                <div class="mk-section-title">Приложения для вас</div>
                <div class="market-grid"></div>
            </div>`;
        const grid = content.querySelector('.market-grid');
        apps.forEach(a => {
            const isInst = installed.includes(a.id) || ['calc','edge','notes'].includes(a.id);
            const card = document.createElement('div');
            card.className = 'market-card';
            card.innerHTML = `<div class="mc-icon">${I[a.icon]}</div><h4>${a.name}</h4><div class="mc-dev">${a.dev}</div><p>${a.desc}</p><div class="mc-rate">${a.rate}</div>`;
            const btn = document.createElement('button');
            btn.textContent = isInst ? 'Открыть' : 'Установить';
            if (isInst) btn.classList.add('installed');
            btn.addEventListener('click', () => {
                if (btn.textContent === 'Установить') {
                    btn.textContent = '...';
                    setTimeout(() => {
                        const inst = JSON.parse(localStorage.getItem('icos_installed') || '[]');
                        inst.push(a.id); localStorage.setItem('icos_installed', JSON.stringify(inst));
                        btn.textContent = 'Открыть'; btn.classList.add('installed');
                        toast('ICMarket', `${a.name} успешно установлено`);
                    }, 1200);
                } else if (APPS[a.id]) { openApp(a.id); }
                else toast(a.name, 'Приложение недоступно в этой сборке');
            });
            card.appendChild(btn);
            grid.appendChild(card);
        });
    }

    // ================= ПАРАМЕТРЫ =================
    function renderSettings(win, content) {
        const pages = {
            system: 'Система', bluetooth: 'Bluetooth и устройства', network: 'Сеть и Интернет',
            personal: 'Персонализация', apps: 'Приложения', accounts: 'Учётные записи', time: 'Время и язык', about: 'О системе'
        };
        content.innerHTML = `
            <div class="settings-app">
                <div class="set-sidebar">
                    <div class="set-usercard">${I.user}<div><b>${escapeHtml(localStorage.getItem('icos_user') || 'Пользователь')}</b><i>Локальная учётная запись</i></div></div>
                    ${Object.entries(pages).map(([k, v], i) => `<div class="set-nav${i === 0 ? ' active' : ''}" data-page="${k}">${I.settings}<span>${v}</span></div>`).join('')}
                </div>
                <div class="set-main"></div>
            </div>`;
        const main = content.querySelector('.set-main');
        const navs = content.querySelectorAll('.set-nav');
        navs.forEach(n => n.addEventListener('click', () => {
            navs.forEach(x => x.classList.remove('active'));
            n.classList.add('active');
            drawPage(n.dataset.page);
        }));

        function card(icon, title, sub, controlHTML) {
            return `<div class="set-card"><div class="sc-l">${icon}<div><b>${title}</b><br><small>${sub}</small></div></div>${controlHTML || ''}</div>`;
        }
        function drawPage(p) {
            if (p === 'system') main.innerHTML = `<h2>Система</h2>
                ${card(I.display || I.computer, 'Дисплей', 'Мониторы, яркость, масштаб', '')}
                ${card(I.volume, 'Звук', 'Громкость, устройства вывода', '')}
                ${card(I.bell, 'Уведомления', 'Оповещения приложений', '<div class="toggle on"></div>')}
                ${card(I.power, 'Питание', 'Спящий режим, батарея', '')}`;
            if (p === 'network') main.innerHTML = `<h2>Сеть и Интернет</h2>
                ${card(I.wifi, 'Wi-Fi', escapeHtml(localStorage.getItem('icos_wifi') === 'skipped' ? 'Не подключено' : (localStorage.getItem('icos_wifi') || 'MyWiFi')) + ' — подключено', '<div class="toggle on"></div>')}`;
            if (p === 'personal') main.innerHTML = `<h2>Персонализация</h2>
                ${card(I.photos, 'Тема', 'Цвет оформления Windows', '<select class="win-select"><option>Тёмная</option><option>Светлая</option></select>')}
                ${card(I.calendar, 'Панель задач', 'Выравнивание по центру', '<select class="win-select"><option>По центру</option><option>По левому краю</option></select>')}`;
            if (p === 'time') main.innerHTML = `<h2>Время и язык</h2>
                ${card(I.calendar, 'Часовой пояс', escapeHtml(localStorage.getItem('icos_tz') || 'UTC'), '')}
                ${card(I.settings, 'Язык системы', escapeHtml(localStorage.getItem('icos_lang') || 'ru'), '')}`;
            if (p === 'about') main.innerHTML = `<h2>О системе</h2>
                ${card(I.computer, 'Характеристики устройства', 'ICOS PC · 8 ГБ ОЗУ · 256 ГБ SSD', '')}
                ${card(I.start, 'Характеристики ICOS', 'ICOS 11 Pro · Версия 11.0.22631 (сборка icos.2610) · Сборка от 26.10.2026', '')}`;
            if (p === 'apps' || p === 'bluetooth' || p === 'accounts') main.innerHTML = `<h2>${pages[p]}</h2>
                ${card(I.market, 'Магазин приложений', 'Управляйте установленными приложениями через ICMarket', '')}`;
            main.querySelectorAll('.toggle').forEach(tg => tg.addEventListener('click', () => tg.classList.toggle('on')));
        }
        drawPage('system');
    }

    // ================= БРАУЗЕР =================
    function renderBrowser(win, content) {
        content.innerHTML = `
            <div class="browser-app">
                <div class="br-toolbar">
                    <button class="exp-nav-btn br-back">${I.back}</button>
                    <button class="exp-nav-btn br-reload"><svg viewBox="0 0 24 24"><path d="M17.65 6.35A8 8 0 1 0 19.73 14h-2.08a6 6 0 1 1-1.4-6.95L13 10h7V3z" fill="currentColor"/></svg></button>
                    <div class="br-url"><span style="color:#5ee08a;">🔒</span><input value="icos://start" spellcheck="false"></div>
                </div>
                <div class="br-page"></div>
            </div>`;
        const page = content.querySelector('.br-page');
        const url = content.querySelector('.br-url input');
        function home() {
            page.innerHTML = `<h1>Добро пожаловать в IC Browser</h1>
                <p>Встроенный браузер операционной системы ICOS 11. Быстрый, лёгкий, с защитой отслеживания.</p>
                <div class="br-tiles">
                    <div class="br-tile">🌐 icos.ru</div><div class="br-tile">📰 Новости</div>
                    <div class="br-tile">🛒 ICMarket</div><div class="br-tile">✉️ Почта</div>
                    <div class="br-tile">🎬 Видео</div><div class="br-tile">🎮 Игры</div>
                </div>`;
        }
        function load(u) {
            url.value = u;
            page.innerHTML = `<h1>${escapeHtml(u)}</h1><p>Страница «${escapeHtml(u)}» загружена (демонстрационный режим IC Browser).</p>`;
        }
        home();
        url.addEventListener('keydown', e => { if (e.key === 'Enter') load(url.value.trim() || 'icos://start'); });
        content.querySelector('.br-back').addEventListener('click', home);
        content.querySelector('.br-reload').addEventListener('click', () => load(url.value));
    }

    // ================= ПОИСК =================
    function renderSearch(win, content) {
        content.innerHTML = `
            <div class="search-panel">
                <div class="sp-search">${I.search}<input placeholder="Введите запрос для поиска приложений и параметров" autofocus></div>
                <div class="sp-results">
                    <div class="sp-best">Лучшее соответствие</div>
                    <div class="sp-list"></div>
                </div>
            </div>`;
        const input = content.querySelector('input');
        const list = content.querySelector('.sp-list');
        const allApps = Object.entries(APPS).map(([id, a]) => ({ id, ...a }));
        function draw(q) {
            q = q.trim().toLowerCase();
            list.innerHTML = '';
            const res = q ? allApps.filter(a => a.title.toLowerCase().includes(q)) : allApps.slice(0, 6);
            if (!res.length) { list.innerHTML = '<div class="exp-empty">Ничего не найдено</div>'; return; }
            res.forEach(a => {
                const d = document.createElement('div');
                d.className = 'sp-item';
                d.innerHTML = `${I[a.icon]}<div><b>${a.title}</b><small>Приложение</small></div>`;
                d.addEventListener('click', () => { openApp(a.id); });
                list.appendChild(d);
            });
        }
        input.addEventListener('input', () => draw(input.value));
        draw('');
        setTimeout(() => input.focus(), 100);
    }

    // ================= КАЛЬКУЛЯТОР =================
    function renderCalc(win, content) {
        content.innerHTML = `
            <div style="display:flex;flex-direction:column;height:100%;background:#202020;">
                <div style="padding:18px 16px 6px;text-align:right;font-size:30px;font-weight:600;" id="calc-disp">0</div>
                <div style="flex:1;display:grid;grid-template-columns:repeat(4,1fr);gap:2px;padding:6px;" id="calc-keys"></div>
            </div>`;
        const disp = content.querySelector('#calc-disp');
        const keys = content.querySelector('#calc-keys');
        let expr = '';
        const layout = ['C','⌫','%','÷','7','8','9','×','4','5','6','−','1','2','3','+','±','0',',','='];
        layout.forEach(k => {
            const b = document.createElement('button');
            b.textContent = k;
            b.style.cssText = 'border:none;border-radius:4px;background:' + (k === '=' ? '#4cc2ff;color:#08213d;font-weight:700;' : 'rgba(255,255,255,.06);color:#eee;') + 'font-size:16px;cursor:default;font-family:inherit;';
            b.addEventListener('click', () => press(k));
            b.addEventListener('mouseenter', () => { if (k !== '=') b.style.background = 'rgba(255,255,255,.12)'; });
            b.addEventListener('mouseleave', () => { if (k !== '=') b.style.background = 'rgba(255,255,255,.06)'; });
            keys.appendChild(b);
        });
        function press(k) {
            if (k === 'C') { expr = ''; disp.textContent = '0'; return; }
            if (k === '⌫') { expr = expr.slice(0, -1); disp.textContent = expr || '0'; return; }
            if (k === '±') { expr = expr.startsWith('-') ? expr.slice(1) : '-' + expr; disp.textContent = expr || '0'; return; }
            if (k === '=') {
                try {
                    const js = expr.replace(/÷/g, '/').replace(/×/g, '*').replace(/−/g, '-').replace(/,/g, '.').replace(/%/g, '/100');
                    const val = Function('"use strict";return (' + js + ')')();
                    disp.textContent = String(val).replace('.', ',');
                    expr = String(val);
                } catch { disp.textContent = 'Ошибка'; expr = ''; }
                return;
            }
            expr += k; disp.textContent = expr;
        }
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }

    window.addEventListener('load', init);
    return { openApp, toggleStart, toast };
})();
