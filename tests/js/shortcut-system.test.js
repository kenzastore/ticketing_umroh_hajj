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
    console.log('Test Passed');
} catch (e) {
    console.error('Test Failed:', e.message);
    process.exit(1);
}
