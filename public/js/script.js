exportPDF();

if (window.location.pathname === id) {

    let div = document.createElement('a');
    div.setAttribute('id', 'back-to-list');
    div.setAttribute('href', '/perf-reporter');
    div.innerHTML = '<svg viewBox="0 0 24 24"><path d="M20.016 11.016v1.969h-12.188l5.578 5.625-1.406 1.406-8.016-8.016 8.016-8.016 1.406 1.406-5.578 5.625h12.188z"></path></svg>Back to reports list';
    document.body.insertBefore(div, document.body.children[0]);
}

function exportPDF() {
    let btn = document.querySelector('#export-pdf');
    if (btn) {
        btn.onclick = function() {
            window.print();
        }
    }
}