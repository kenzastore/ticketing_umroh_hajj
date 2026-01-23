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
    
    // Check for at least one navigation shortcut as per spec
    const hasDashboard = globalShortcuts.some(s => s.action === 'navigation' && s.description.toLowerCase().includes('dashboard'));
    assert.ok(hasDashboard, 'Should have a dashboard navigation shortcut');
    
    console.log('Test Passed');
} catch (e) {
    console.error('Test Failed:', e.message);
    process.exit(1);
}
