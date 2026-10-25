const OS = (function() {
    let zIndexCounter = 100;
    const openWindows = {};

    // ================= ФАЙЛОВАЯ СИСТЕМА (localStorage) =================
    // Структура: { id: "1", name: "Папка", type: "folder", parent: "root", content: "" }
    // type: "folder" или "file"
    
    function initFS() {
        if (!localStorage.getItem('icos_fs')) {
            const initialFS = [
                { id: 'root', name: 'C:', type: 'folder', parent: null },
                { id: 'f1', name: 'Program Files', type: 'folder', parent: 'root' },
                { id: 'f2', name: 'Windows', type: 'folder', parent: 'root' },
                { id: 'f3', name: 'Users', type: 'folder', parent: 'root' },
                { id: 'f4', name: 'Documents', type: 'folder', parent: 'f3' },
                { id: 'f5', name: 'Downloads', type: 'folder', parent: 'f3' },
            ];
            localStorage.setItem('icos_fs', JSON.stringify(initialFS));
        }
        if (!localStorage.getItem('icos_trash')) {
            localStorage.setItem('icos_trash', JSON.stringify([]));
        }
    }

    function getFS() { return JSON.parse(localStorage.getItem('icos_fs')); }
    function saveFS(fs) { localStorage.setItem('icos_fs', JSON.stringify(fs)); }
    
    function getTrash() { return JSON.parse(localStorage.getItem('icos_trash')); }
    function saveTrash(t) { localStorage.setItem('icos_trash', JSON.stringify(t)); }

    function createItem(name, type, parent, content = "") {
        const fs = getFS();
        const id = 'item_' + Date.now();
        fs.push({ id, name, type, parent, content });
        saveFS(fs);
        return id;
    }

    function deleteItem(id, toTrash = true) {
        let fs = getFS();
        const item = fs.find(i => i.id === id);
        if (!item) return;

        if (toTrash) {
            const trash = getTrash();
            trash.push(item);
            saveTrash(trash);
        }

        // Рекурсивное удаление дочерних элементов
        function removeChildren(parentId) {
            const children = fs.filter(i => i.parent === parentId);
            children.forEach(c => removeChildren(c.id));
            fs = fs.filter(i => i.id !== parentId);
        }
        removeChildren(id);
        saveFS(fs);
    }

    // ================= ЯДРО ОКОН =================
    function init() {
        initFS();
        document.querySelectorAll('.icon').forEach(icon => {
            icon.addEventListener('dblclick', () => openApp(icon.dataset.app));
            icon.addEventListener('click', (e) => {
                e.stopPropagation();
                document.querySelectorAll('.icon').forEach(i => i.classList.remove('active'));
                icon.classList.add('active');
            });
        });
        document.getElementById('start-btn').addEventListener('click', toggleStart);
        document.addEventListener('click', () => {
            document.querySelectorAll('.icon').forEach(i => i.classList.remove('active'));
            document.querySelectorAll('.context-menu').forEach(m => m.remove());
        });
        setInterval(updateClock, 1000);
        updateClock();
        document.getElementById('tray-lang').addEventListener('click', function() {
            this.textContent = this.textContent === 'RUS' ? 'ENG' : 'RUS';
        });
    }

    function updateClock() {
        const now = new Date();
        document.getElementById('tray-time').textContent = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
        document.getElementById('tray-date').textContent = String(now.getDate()).padStart(2, '0') + '.' + String(now.getMonth() + 1).padStart(2, '0') + '.' + now.getFullYear();
    }

    function toggleStart() {
        document.getElementById('start-menu').classList.toggle('active');
    }

    function showContextMenu(x, y, items) {
        document.querySelectorAll('.context-menu').forEach(m => m.remove());
        const menu = document.createElement('div');
        menu.className = 'context-menu';
        menu.style.left = x + 'px';
        menu.style.top = y + 'px';
        items.forEach(item => {
            const div = document.createElement('div');
            div.textContent = item.label;
            div.onclick = (e) => { e.stopPropagation(); item.action(); menu.remove(); };
            menu.appendChild(div);
        });
        document.body.appendChild(menu);
    }

    // ================= ОТКРЫТИЕ ПРИЛОЖЕНИЙ =================
    function openApp(appId, args = {}) {
        if (openWindows[appId]) {
            if (appId === 'computer' && args.path) openExplorer(openWindows[appId].element, args.path);
            if (appId === 'notes' && args.fileId) openNotepad(openWindows[appId].element, args.fileId);
            bringToFront(openWindows[appId].element);
            return;
        }

        let title = '', iconSvg = '', width = 600, height = 400, contentHTML = '';

        switch (appId) {
            case 'computer':
                title = 'Проводник'; width = 750; height = 500;
                iconSvg = '<svg viewBox="0 0 24 24"><path d="M21 2H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h7v2H8v2h8v-2h-2v-2h7c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H3V4h18v12z"/></svg>';
                contentHTML = `<div class="explorer"><div class="explorer-sidebar" id="explorer-sidebar"></div><div class="explorer-main" id="explorer-main"></div></div>`;
                break;
            case 'trash':
                title = 'Корзина'; width = 600; height = 400;
                iconSvg = '<svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>';
                contentHTML = `<div class="explorer"><div class="explorer-main" id="trash-main"></div></div>`;
                break;
            case 'notes':
                title = 'Заметки'; width = 600; height = 450;
                iconSvg = '<svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>';
                contentHTML = `<div class="notepad"><textarea id="notepad-area" placeholder="Введите текст..."></textarea></div>`;
                break;
            case 'cmd':
                title = 'Командная строка'; width = 650; height = 400;
                iconSvg = '<svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12zm-8-6l-3.5 3.5-1.4-1.4L9.2 12l-2.1-2.1 1.4-1.4L12 12zm2 3h4v-1.5h-4V15z"/></svg>';
                contentHTML = `<div class="cmd-app"><div class="cmd-output" id="cmd-out"></div><div class="cmd-input-line"><span class="cmd-prompt" id="cmd-prompt">C:\\></span><input type="text" class="cmd-input" id="cmd-input" autocomplete="off"></div></div>`;
                break;
            case 'market':
                title = 'ICMarket'; width = 700; height = 500;
                iconSvg = '<svg viewBox="0 0 24 24"><path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3H9c0-1.66 1.34-3 3-3zm7 17H5V8h14v12zm-7-8c-1.66 0-3-1.34-3-3H7c0 2.76 2.24 5 5 5s5-2.24 5-5h-2c0 1.66-1.34 3-3 3z"/></svg>';
                contentHTML = `<div class="market"><div class="market-grid">
                    <div class="market-card"><h4>Калькулятор</h4><p>Простой калькулятор</p><button onclick="alert('Установлено!')">Установить</button></div>
                    <div class="market-card"><h4>Браузер</h4><p>Веб-браузер</p><button onclick="alert('Установлено!')">Установить</button></div>
                </div></div>`;
                break;
        }

        const win = document.createElement('div');
        win.className = 'window';
        win.style.width = width + 'px'; win.style.height = height + 'px';
        win.style.left = (window.innerWidth / 2 - width / 2 + (Math.random()*40-20)) + 'px';
        win.style.top = (window.innerHeight / 2 - height / 2 + (Math.random()*40-20)) + 'px';
        win.style.zIndex = ++zIndexCounter;

        win.innerHTML = `
            <div class="window-header">
                <div class="window-title">${iconSvg} ${title}</div>
                <div class="window-controls">
                    <button class="minimize">—</button>
                    <button class="maximize">□</button>
                    <button class="close">✕</button>
                </div>
            </div>
            <div class="window-content">${contentHTML}</div>
        `;

        document.getElementById('windows-layer').appendChild(win);
        openWindows[appId] = { element: win, title: title, iconSvg: iconSvg };

        addToTaskbar(appId, title, iconSvg);
        setupWindowEvents(win, appId);
        bringToFront(win);

        // Инициализация приложений
        if (appId === 'computer') openExplorer(win, args.path || 'root');
        if (appId === 'trash') renderTrash(win);
        if (appId === 'notes') setupNotepad(win, args.fileId);
        if (appId === 'cmd') setupCmd(win);
    }

    function addToTaskbar(appId, title, iconSvg) {
        const tasks = document.getElementById('taskbar-apps');
        const btn = document.createElement('div');
        btn.className = 'taskbar-app active';
        btn.id = 'task-' + appId;
        btn.innerHTML = `${iconSvg} <span>${title}</span>`;
        btn.addEventListener('click', () => {
            const win = openWindows[appId].element;
            if (win.style.display === 'none') win.style.display = 'flex';
            bringToFront(win);
        });
        tasks.appendChild(btn);
    }

    function bringToFront(win) {
        win.style.zIndex = ++zIndexCounter;
        document.querySelectorAll('.taskbar-app').forEach(btn => btn.classList.remove('active'));
        for (let app in openWindows) {
            if (openWindows[app].element === win) {
                document.getElementById('task-' + app).classList.add('active');
            }
        }
    }

    function setupWindowEvents(win, appId) {
        const header = win.querySelector('.window-header');
        let isDragging = false, startX, startY, startLeft, startTop;
        header.addEventListener('mousedown', (e) => {
            if (e.target.closest('.window-controls')) return;
            isDragging = true; startX = e.clientX; startY = e.clientY;
            startLeft = win.offsetLeft; startTop = win.offsetTop;
            bringToFront(win);
        });
        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            win.style.left = (startLeft + e.clientX - startX) + 'px';
            win.style.top = (startTop + e.clientY - startY) + 'px';
        });
        document.addEventListener('mouseup', () => isDragging = false);

        win.querySelector('.close').addEventListener('click', () => {
            win.remove();
            delete openWindows[appId];
            const t = document.getElementById('task-' + appId);
            if (t) t.remove();
        });
        win.querySelector('.minimize').addEventListener('click', () => {
            win.style.display = 'none';
            document.getElementById('task-' + appId).classList.remove('active');
        });
        win.querySelector('.maximize').addEventListener('click', () => {
            if (win.dataset.max === 'true') {
                win.style.width = win.dataset.ow; win.style.height = win.dataset.oh;
                win.style.left = win.dataset.ol; win.style.top = win.dataset.ot;
                win.dataset.max = 'false';
            } else {
                win.dataset.ow = win.style.width; win.dataset.oh = win.style.height;
                win.dataset.ol = win.style.left; win.dataset.ot = win.style.top;
                win.style.width = '100%'; win.style.height = 'calc(100% - 50px)';
                win.style.left = '0'; win.style.top = '0';
                win.dataset.max = 'true';
            }
        });
        win.addEventListener('mousedown', () => bringToFront(win));
    }

    // ================= ПРОВОДНИК =================
    function openExplorer(win, parentId) {
        const main = win.querySelector('#explorer-main');
        const sidebar = win.querySelector('#explorer-sidebar');
        if (!main) return;

        // Отрисовка сайдбара
        sidebar.innerHTML = `
            <div onclick="OS.openApp('computer', {path: 'root'})" class="${parentId === 'root' ? 'active' : ''}"><svg style="width:16px;height:16px;fill:#4a9eff;" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg> Диск C:</div>
            <div onclick="OS.openApp('computer', {path: 'f3'})" class="${parentId === 'f3' ? 'active' : ''}"><svg style="width:16px;height:16px;fill:#ccc;" viewBox="0 0 24 24"><path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z"/></svg> Пользователи</div>
        `;

        // Отрисовка файлов
        main.innerHTML = '';
        const fs = getFS();
        const items = fs.filter(i => i.parent === parentId);

        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'folder-icon';
            div.innerHTML = item.type === 'folder' 
                ? `<svg viewBox="0 0 24 24" fill="#ffd700"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>`
                : `<svg viewBox="0 0 24 24" fill="#ccc"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>`;
            div.innerHTML += `<span>${item.name}</span>`;

            // Клик — открыть
            div.addEventListener('dblclick', () => {
                if (item.type === 'folder') {
                    openExplorer(win, item.id);
                } else {
                    openApp('notes', { fileId: item.id });
                }
            });

            // ПКМ — контекстное меню
            div.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                e.stopPropagation();
                showContextMenu(e.pageX, e.pageY, [
                    { label: 'Открыть', action: () => {
                        if (item.type === 'folder') openExplorer(win, item.id);
                        else openApp('notes', { fileId: item.id });
                    }},
                    { label: 'Переименовать', action: () => {
                        const newName = prompt('Новое имя:', item.name);
                        if (newName) {
                            const fs = getFS();
                            const target = fs.find(i => i.id === item.id);
                            if (target) { target.name = newName; saveFS(fs); openExplorer(win, parentId); }
                        }
                    }},
                    { label: 'Удалить', action: () => {
                        if (confirm(`Удалить "${item.name}"?`)) {
                            deleteItem(item.id);
                            openExplorer(win, parentId);
                        }
                    }}
                ]);
            });
            main.appendChild(div);
        });

        // ПКМ по пустому месту
        main.oncontextmenu = (e) => {
            e.preventDefault();
            showContextMenu(e.pageX, e.pageY, [
                { label: 'Создать папку', action: () => {
                    const name = prompt('Имя папки:', 'Новая папка');
                    if (name) { createItem(name, 'folder', parentId); openExplorer(win, parentId); }
                }},
                { label: 'Создать текстовый документ', action: () => {
                    const name = prompt('Имя файла:', 'Новый файл.txt');
                    if (name) { createItem(name, 'file', parentId, ''); openExplorer(win, parentId); }
                }}
            ]);
        };
    }

    // ================= ЗАМЕТКИ =================
    function setupNotepad(win, fileId) {
        const area = win.querySelector('#notepad-area');
        if (!area) return;

        // Если передан файл — загружаем
        if (fileId) {
            const fs = getFS();
            const file = fs.find(i => i.id === fileId);
            if (file) area.value = file.content || '';
        }

        // Сохранение при вводе
        area.addEventListener('input', () => {
            if (fileId) {
                const fs = getFS();
                const file = fs.find(i => i.id === fileId);
                if (file) { file.content = area.value; saveFS(fs); }
            }
        });
    }

    // ================= КОРЗИНА =================
    function renderTrash(win) {
        const main = win.querySelector('#trash-main');
        if (!main) return;
        main.innerHTML = '';
        const trash = getTrash();

        if (trash.length === 0) {
            main.innerHTML = '<span style="color:#666;width:100%;text-align:center;margin-top:50px;">Корзина пуста</span>';
            return;
        }

        trash.forEach((item, index) => {
            const div = document.createElement('div');
            div.className = 'folder-icon';
            div.innerHTML = `<svg viewBox="0 0 24 24" fill="#ff6666"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>`;
            div.innerHTML += `<span>${item.name}</span>`;

            div.addEventListener('contextmenu', (e) => {
                e.preventDefault(); e.stopPropagation();
                showContextMenu(e.pageX, e.pageY, [
                    { label: 'Восстановить', action: () => {
                        const t = getTrash();
                        const restored = t.splice(index, 1)[0];
                        saveTrash(t);
                        const fs = getFS();
                        fs.push(restored);
                        saveFS(fs);
                        renderTrash(win);
                    }},
                    { label: 'Удалить окончательно', action: () => {
                        if (confirm('Удалить навсегда?')) {
                            const t = getTrash();
                            t.splice(index, 1);
                            saveTrash(t);
                            renderTrash(win);
                        }
                    }}
                ]);
            });
            main.appendChild(div);
        });
    }

    // ================= CMD =================
    function setupCmd(win) {
        const input = win.querySelector('#cmd-input');
        const out = win.querySelector('#cmd-out');
        const promptEl = win.querySelector('#cmd-prompt');
        if (!input || !out) return;

        let currentDir = 'root';
        
        function updatePrompt() {
            const fs = getFS();
            const dir = fs.find(i => i.id === currentDir);
            const path = dir ? (dir.parent === null ? dir.name : 'C:\\' + dir.name) : 'C:\\';
            promptEl.textContent = path + '>';
        }
        updatePrompt();

        out.innerHTML = `ICOS [Version 1.0.0]\n(c) ICOS Corporation. All rights reserved.\n\nВведите "help" для списка команд.\n\n`;

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const cmdLine = input.value.trim();
                input.value = '';
                out.innerHTML += promptEl.textContent + ' ' + cmdLine + '\n';
                if (cmdLine === '') return;

                const args = cmdLine.split(' ');
                const cmd = args[0].toLowerCase();
                const fs = getFS();

                if (cmd === 'help') {
                    out.innerHTML += `Доступные команды:\n  help - список команд\n  cls - очистить экран\n  dir - список файлов\n  cd <папка> - перейти в папку\n  cd .. - вернуться назад\n  mkdir <имя> - создать папку\n  echo <текст> > <имя> - создать файл\n  del <имя> - удалить файл\n  ver - версия ОС\n\n`;
                } else if (cmd === 'cls') {
                    out.innerHTML = '';
                } else if (cmd === 'ver') {
                    out.innerHTML += `ICOS [Version 1.0.0]\n\n`;
                } else if (cmd === 'dir') {
                    const children = fs.filter(i => i.parent === currentDir);
                    out.innerHTML += ` Содержимое папки ${promptEl.textContent.replace('>', '')}\n\n`;
                    if (children.length === 0) {
                        out.innerHTML += ` Файлов не найдено.\n\n`;
                    } else {
                        children.forEach(c => {
                            const typeStr = c.type === 'folder' ? '<DIR>        ' : '             ';
                            out.innerHTML += `01.01.2024  10:00    ${typeStr} ${c.name}\n`;
                        });
                        out.innerHTML += `\n`;
                    }
                } else if (cmd === 'cd') {
                    if (!args[1] || args[1] === '.') return;
                    if (args[1] === '..') {
                        const dir = fs.find(i => i.id === currentDir);
                        if (dir && dir.parent) currentDir = dir.parent;
                    } else {
                        const target = fs.find(i => i.name.toLowerCase() === args[1].toLowerCase() && i.parent === currentDir && i.type === 'folder');
                        if (target) currentDir = target.id;
                        else out.innerHTML += `Не удается найти указанный путь.\n\n`;
                    }
                    updatePrompt();
                } else if (cmd === 'mkdir') {
                    if (!args[1]) out.innerHTML += `Синтаксис: mkdir <имя>\n\n`;
                    else { createItem(args[1], 'folder', currentDir); out.innerHTML += `Папка создана.\n\n`; }
                } else if (cmd === 'echo') {
                    const fullText = args.slice(1).join(' ');
                    const parts = fullText.split('>');
                    if (parts.length === 2) {
                        const text = parts[0].trim();
                        const filename = parts[1].trim();
                        if (filename) {
                            createItem(filename, 'file', currentDir, text);
                            out.innerHTML += `Файл создан.\n\n`;
                        }
                    } else {
                        out.innerHTML += fullText + '\n\n';
                    }
                } else if (cmd === 'del') {
                    if (!args[1]) out.innerHTML += `Синтаксис: del <имя>\n\n`;
                    else {
                        const target = fs.find(i => i.name.toLowerCase() === args[1].toLowerCase() && i.parent === currentDir);
                        if (target) { deleteItem(target.id); out.innerHTML += `Удалено.\n\n`; }
                        else out.innerHTML += `Файл не найден.\n\n`;
                    }
                } else {
                    out.innerHTML += `'${cmd}' не является внутренней или внешней командой.\n\n`;
                }
                out.scrollTop = out.scrollHeight;
            }
        });
        input.focus();
        win.addEventListener('click', () => input.focus());
    }

    window.onload = init;
    return { openApp, toggleStart, openExplorer };
})();