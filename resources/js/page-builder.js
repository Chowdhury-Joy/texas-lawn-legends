import grapesjs from 'grapesjs';

function blockWrapperHtml(uuid, type, html) {
    return `<div data-gjs-type="page-block" data-block-uuid="${uuid}" data-block-type="${type}">${html}</div>`;
}

let activeWire = null;
let activeEditor = null;

function findComponentByUuid(uuid) {
    if (!activeEditor) return null;
    return activeEditor.getWrapper().find(`[data-block-uuid="${uuid}"]`)[0] ?? null;
}

async function handleSavePage() {
    if (!activeWire || !activeEditor) return;

    const list = activeEditor.getWrapper().find('[data-gjs-type="page-block-list"]')[0] ?? activeEditor.getWrapper();
    const uuids = list
        .components()
        .map((component) => component.getAttributes()['data-block-uuid'])
        .filter((uuid) => uuid && uuid !== 'new');

    await activeWire.reorder(uuids);
    await activeWire.save();
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('#page-builder-save');

    if (!button) return;

    event.preventDefault();
    handleSavePage();
});

window.PageBuilder = {
    init(wire, { canvas, blocksPanel, layersPanel, cssUrl, blocks, labels, availableTypes }) {
        activeWire = wire;

        if (activeEditor) {
            try {
                activeEditor.destroy();
            } catch (e) {
                console.error(e);
            }
            activeEditor = null;
        }

        // When we re-render a block in place (after an edit), we remove and
        // re-append its canvas component. That fires component:remove — this
        // flag tells the remove handler to skip the server-side removeBlock so
        // an in-place re-render isn't mistaken for a deletion.
        let suppressRemoveFor = null;

        const editor = grapesjs.init({
            container: canvas,
            height: '100%',
            width: 'auto',
            fromElement: false,
            storageManager: false,
            avoidInlineStyle: true,
            canvas: {
                styles: [cssUrl],
            },
            deviceManager: {
                devices: [
                    { name: 'Desktop', width: '' },
                    { name: 'Tablet', width: '768px' },
                    { name: 'Mobile', width: '375px' },
                ],
            },
            blockManager: {
                appendTo: blocksPanel,
            },
            layerManager: {
                appendTo: layersPanel,
            },
            panels: {
                defaults: [],
            },
        });

        activeEditor = editor;

        // Device selector handling
        document.querySelectorAll('.page-builder__device-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const device = e.currentTarget.getAttribute('data-device');
                
                let deviceName = 'Desktop';
                if (device === 'tablet') deviceName = 'Tablet';
                if (device === 'mobile') deviceName = 'Mobile';

                editor.setDevice(deviceName);
                
                document.querySelectorAll('.page-builder__device-btn').forEach(b => b.classList.remove('active'));
                e.currentTarget.classList.add('active');
            });
        });

        editor.DomComponents.addType('page-block-list', {
            isComponent: (el) => el.getAttribute && el.getAttribute('data-gjs-type') === 'page-block-list',
            model: {
                defaults: {
                    droppable: '[data-gjs-type="page-block"]',
                    draggable: false,
                    removable: false,
                    copyable: false,
                    selectable: false,
                },
            },
        });

        editor.DomComponents.addType('page-block', {
            isComponent: (el) => el.getAttribute && el.getAttribute('data-gjs-type') === 'page-block',
            model: {
                defaults: {
                    draggable: '[data-gjs-type="page-block-list"]',
                    droppable: false,
                    editable: false,
                    removable: true,
                    copyable: false,
                    selectable: true,
                    highlightable: true,
                },
            },
        });

        const wrapperHtml = `<div data-gjs-type="page-block-list">${blocks
            .map((block) => blockWrapperHtml(block.uuid, block.type, block.html))
            .join('')}</div>`;

        editor.setComponents(wrapperHtml);

        availableTypes.forEach((type) => {
            editor.BlockManager.add(type, {
                label: labels[type] ?? type,
                category: 'Sections',
                content: {
                    type: 'page-block',
                    attributes: { 'data-block-uuid': 'new', 'data-block-type': type },
                },
            });
        });

        editor.on('block:drag:stop', (component) => {
            if (!component) return;

            const attrs = component.getAttributes();

            if (attrs['data-block-uuid'] !== 'new') return;

            const type = attrs['data-block-type'];
            const index = component.index();
            const parent = component.parent();

            wire.addBlock(type).then(({ uuid, html }) => {
                component.remove();
                parent.append(blockWrapperHtml(uuid, type, html), { at: index });
            });
        });

        editor.on('component:selected', (component) => {
            const attrs = component.getAttributes();

            if (component.get('type') !== 'page-block') return;
            if (!attrs['data-block-uuid'] || attrs['data-block-uuid'] === 'new') return;

            wire.selectBlock(attrs['data-block-uuid']);
        });

        editor.on('component:remove', (component) => {
            const attrs = component.getAttributes();

            if (component.get('type') !== 'page-block') return;

            const uuid = attrs['data-block-uuid'];

            if (!uuid || uuid === 'new') return;
            if (uuid === suppressRemoveFor) return;

            wire.removeBlock(uuid);
        });

        Livewire.on('block-updated', ({ uuid, html }) => {
            const component = findComponentByUuid(uuid);

            if (!component) return;

            const type = component.getAttributes()['data-block-type'];
            const index = component.index();
            const parent = component.parent();

            try {
                suppressRemoveFor = uuid;
                component.remove();
            } finally {
                suppressRemoveFor = null;
            }

            parent.append(blockWrapperHtml(uuid, type, html), { at: index });
        });
    },
};
