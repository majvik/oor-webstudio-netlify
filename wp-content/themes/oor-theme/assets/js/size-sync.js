// Синхронизация размеров элементов
class SizeSync {
    constructor() {
        this.syncTasks = [];
        this.init();
    }

    addSyncTask(sourceSelector, targetSelector, options = {}) {
        const task = {
            sourceSelector,
            targetSelector,
            syncWidth: options.syncWidth !== false,
            syncHeight: options.syncHeight || false,
            offsetWidth: options.offsetWidth || 0,
            offsetHeight: options.offsetHeight || 0
        };
        this.syncTasks.push(task);
        return this;
    }

    sync() {
        if (window.innerWidth <= 768) {
            this.syncTasks.forEach(task => {
                const targetElement = document.querySelector(task.targetSelector);
                if (targetElement) {
                    if (task.syncWidth) targetElement.style.width = '';
                    if (task.syncHeight) targetElement.style.height = '';
                }
            });
            return;
        }

        this.syncTasks.forEach(task => {
            const sourceElement = document.querySelector(task.sourceSelector);
            const targetElement = document.querySelector(task.targetSelector);
            
            if (sourceElement && targetElement) {
                if (task.syncWidth) {
                    const width = sourceElement.offsetWidth + task.offsetWidth;
                    targetElement.style.width = width + 'px';
                }
                
                if (task.syncHeight) {
                    const height = sourceElement.offsetHeight + task.offsetHeight;
                    targetElement.style.height = height + 'px';
                }
                
            } else {
            }
        });
    }

    init() {
        const initWithDelay = () => {
            setTimeout(() => this.sync(), 100);
            setTimeout(() => this.sync(), 500);
            setTimeout(() => this.sync(), 1000);
        };

        document.addEventListener('DOMContentLoaded', initWithDelay);

        window.addEventListener('resize', () => this.sync());

        window.addEventListener('load', initWithDelay);
    }
}

const sizeSync = new SizeSync();
sizeSync.addSyncTask(
    '.oor-quality-img-container-1',
    '.oor-become-artist-button',
    { syncWidth: true, syncHeight: false }
);

sizeSync.addSyncTask(
    '.oor-without-fear-image',
    '.oor-manifesto-button',
    { syncWidth: true, syncHeight: false }
);

sizeSync.addSyncTask(
    '.oor-without-fear-image',
    '.oor-without-fear-text',
    { syncWidth: true, syncHeight: false }
);

// Синхронизация высоты элементов .oor-studio-service-item
function syncServiceItemsHeight() {
    if (window.innerWidth <= 768) {
        // На мобильных не синхронизируем
        const items = document.querySelectorAll('.oor-studio-service-item');
        items.forEach(item => {
            item.style.height = '';
        });
        return;
    }

    const items = document.querySelectorAll('.oor-studio-service-item');
    if (items.length === 0) return;

    let maxHeight = 0;
    items.forEach(item => {
        item.style.height = '';
        const height = item.offsetHeight;
        if (height > maxHeight) {
            maxHeight = height;
        }
    });

    if (maxHeight > 0) {
        items.forEach(item => {
            item.style.height = maxHeight + 'px';
        });
    }
}

// Инициализация синхронизации высоты элементов сервисов
function initServiceItemsHeightSync() {
    const initWithDelay = () => {
        setTimeout(() => syncServiceItemsHeight(), 100);
        setTimeout(() => syncServiceItemsHeight(), 500);
        setTimeout(() => syncServiceItemsHeight(), 1000);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWithDelay);
    } else {
        initWithDelay();
    }

    window.addEventListener('resize', () => {
        setTimeout(() => syncServiceItemsHeight(), 100);
    });

    window.addEventListener('load', initWithDelay);
}

initServiceItemsHeightSync();
