document.body.addEventListener('toggle', function e => {
    if (e.target.tagName === 'DETAILS') {
        const template = e.target.querySelector('template');

        if (template) {
            const body = document.importNode(template.content, true);
        }
    }
}, true);