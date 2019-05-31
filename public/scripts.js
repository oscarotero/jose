window.onerror = function (message, file, lineNo, colNo) {
    const error = { message, file, lineNo, colNo };
    const blob = new Blob([JSON.stringify(error)], { type: 'application/json' });
    navigator.sendBeacon('/report', blob);
};

document.body.addEventListener('toggle', e => {
    if (e.target.tagName === 'DETAILS') {
        if (!e.target.open) {
            e.target.parentElement.scrollIntoView();
        }
    }
}, true);

document.body.addEventListener('submit', e => {
    if (e.target.matches('form.entry-save')) {
        e.preventDefault();
        const btn = e.target.querySelector('button');

        return fetch(e.target.action, {
            method: 'POST',
            body: new FormData(e.target),
            credentials: 'same-origin'
        })
        .then(response => response.text())
        .then(text => {
            if (text == 1) {
                btn.classList.add('is-active');
                btn.innerText = 'Unsave';
            } else {
                btn.classList.remove('is-active');
                btn.innerText = 'Save';
            }
        });
    }

    if (e.target.matches('form.entry-hide')) {
        e.preventDefault();
        const li = e.target.closest('.entries > li');
        li.hidden = true;

        return fetch(e.target.action, {
            method: 'POST',
            body: new FormData(e.target),
            credentials: 'same-origin'
        })
        .then(response => response.text())
        .then(text => {
            if (text == 1) {
                li.remove();
            } else {
                li.hidden = false;
            }
        });
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
            document.location = document.location.pathname;
        }, 1000)
    });
});

//Service worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(error => console.log('Registration failed with ' + error));
}