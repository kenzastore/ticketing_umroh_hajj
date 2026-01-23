const assert = require('assert');

// Mock browser environment if needed
global.window = {
    location: { pathname: '/' },
    addEventListener: () => {}
};
global.document = {
    addEventListener: () => {},
    querySelector: () => null,
    createElement: () => ({ style: {}, appendChild: () => {}, classList: { add: () => {} } })
};

try {
    const ShortcutSystem = require('../../public/assets/js/shortcut-system.js');
    assert.ok(ShortcutSystem, 'ShortcutSystem should be defined');
    
    // Task 2: Registry tests
    assert.ok(ShortcutSystem.registry, 'Registry should be defined');
    
    // We expect some initial global shortcuts to be present after Task 2 implementation
    assert.ok(ShortcutSystem.registry.global, 'Global shortcuts should be defined in registry');
    const globalShortcuts = ShortcutSystem.registry.global;
    assert.ok(Array.isArray(globalShortcuts), 'Global shortcuts should be an array');
    
    // Task 3: Listener tests
    let eventListenerAdded = false;
    let registeredCallback = null;
    
    global.document.addEventListener = (type, callback) => {
        if (type === 'keydown') {
            eventListenerAdded = true;
            registeredCallback = callback;
        }
    };
    
    ShortcutSystem.init();
    assert.ok(eventListenerAdded, 'Should add a keydown event listener on init');
    
    // Simulate Alt+D (Dashboard)
    let navigationTriggered = false;
    global.window.location.assign = (url) => {
        if (url === '/admin/dashboard.php') {
            navigationTriggered = true;
        }
    };
    
    if (registeredCallback) {
        registeredCallback({
            code: 'KeyD',
            altKey: true,
            ctrlKey: false,
            shiftKey: false,
            target: { tagName: 'BODY' },
            preventDefault: () => {}
        });
        assert.ok(navigationTriggered, 'Alt+D should trigger navigation to dashboard');
    }
    
    console.log('Test Passed');
} catch (e) {
    console.error('Test Failed:', e.message);
    process.exit(1);
}
