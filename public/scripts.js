document.body.addEventListener('toggle', e => {
    if (e.target.tagName === 'DETAILS') {
        const template = e.target.querySelector('template');

        if (template) {
            const body = document.importNode(template.content, true);
            template.replaceWith(body);
        }
    }
}, true);