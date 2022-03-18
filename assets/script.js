exportPDF();

function exportPDF() {
    let btn = document.querySelector('#export-pdf');
    if (btn) {
        btn.onclick = function() {
            window.print();
        }
    }
}