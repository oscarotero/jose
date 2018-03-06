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
            body: new FormData(e.target),
            credentials: 'same-origin'
        })
        .then(response => response.text())
        .then(text => e.target.classList.toggle('is-saved', text == 1));
    }
}, true);

document.getElementById('refresh-form').addEventListener('submit', e => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    btn.disabled = true;
    btn.innerText = 'Refreshing...';

    fetch(e.target.action, {
        method: "POST",
        body: new FormData(e.target),
        credentials: 'same-origin'
    })
    .then(response => {
        btn.innerText = 'OK!';

        setTimeout(() => {
            document.location = document.location;
        }, 1000)
    });
});
