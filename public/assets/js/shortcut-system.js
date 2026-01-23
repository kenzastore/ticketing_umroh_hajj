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
        console.log('ShortcutSystem initialized');
    }
};

if (typeof module !== 'undefined' && module.exports) {
    module.exports = ShortcutSystem;
}
