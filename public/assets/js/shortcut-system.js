/**
 * ShortcutSystem
 * Manages keyboard shortcuts across the application.
 */
const ShortcutSystem = {
    registry: {
        global: [
            { key: 'KeyD', modifier: 'altKey', action: 'navigation', target: '/admin/dashboard.php', description: 'Go to Dashboard' },
            { key: 'KeyB', modifier: 'altKey', action: 'navigation', target: '/admin/booking_requests.php', description: 'Go to Booking Requests' },
            { key: 'KeyM', modifier: 'altKey', action: 'navigation', target: '/admin/movement_fullview.php', description: 'Go to Movement' },
            { key: 'KeyF', modifier: 'altKey', action: 'navigation', target: '/finance/dashboard.php', description: 'Go to Finance Dashboard' }
        ],
        // Page specific shortcuts can be added here
        '/admin/booking_requests.php': [
            { key: 'KeyN', modifier: 'altKey', action: 'click', target: '#btn-new-request', description: 'New Booking Request' }
        ]
    },
    init: function() {
        document.addEventListener('keydown', (e) => this.handleKeydown(e));
        console.log('ShortcutSystem initialized');
    },
    handleKeydown: function(e) {
        // Ignore if user is typing in input or textarea
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) {
            return;
        }

        const shortcuts = this.getAvailableShortcuts();
        for (const s of shortcuts) {
            if (e.code === s.key && !!e.altKey === (s.modifier === 'altKey') && !!e.ctrlKey === (s.modifier === 'ctrlKey')) {
                e.preventDefault();
                this.executeAction(s);
                break;
            }
        }
    },
    getAvailableShortcuts: function() {
        const global = this.registry.global || [];
        const pageSpecific = this.registry[window.location.pathname] || [];
        return [...global, ...pageSpecific];
    },
    executeAction: function(shortcut) {
        if (shortcut.action === 'navigation') {
            window.location.assign(shortcut.target);
        } else if (shortcut.action === 'click') {
            const el = document.querySelector(shortcut.target);
            if (el) el.click();
        }
    }
};

if (typeof module !== 'undefined' && module.exports) {
    module.exports = ShortcutSystem;
}
