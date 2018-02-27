document.body.addEventListener('toggle', e => {
    if (e.target.tagName === 'DETAILS') {
        const template = e.target.querySelector('template');

        if (template) {
            const body = document.importNode(template.content, true);
            template.replaceWith(body);
        }
    }
}, true);

document.body.addEventListener('submit', e => {
    if (e.target.matches('form.entry-save')) {
        e.preventDefault();

        fetch(e.target.action, {
            method: "POST",
            body: new FormData(e.target)
        })
        .then(response => response.text())
        .then(text => e.target.classList.toggle('is-saved', text == 1));
    }
}, true);