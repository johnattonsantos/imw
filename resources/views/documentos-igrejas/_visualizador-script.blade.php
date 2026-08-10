@once
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = $('#documentoVisualizadorModal');
        const frame = document.getElementById('documentoVisualizadorFrame');
        const title = document.getElementById('documentoVisualizadorTitulo');

        if (!modal.length || !frame || !title) {
            return;
        }

        const previewUrl = function (url) {
            if (!url) {
                return 'about:blank';
            }

            if (url.indexOf('#') !== -1) {
                return url;
            }

            return url + '#toolbar=0&navpanes=0&scrollbar=1';
        };

        document.addEventListener('click', function (event) {
            const button = event.target.closest('.btn-visualizar-documento');

            if (!button) {
                return;
            }

            event.preventDefault();

            const url = button.getAttribute('data-documento-url');
            const name = button.getAttribute('data-documento-nome') || window.__('Visualização do documento');
            const currentModal = button.closest('.modal.show');

            const openViewer = function () {
                title.textContent = name;
                frame.setAttribute('src', previewUrl(url));
                modal.modal('show');
            };

            if (currentModal && currentModal.id !== 'documentoVisualizadorModal') {
                $(currentModal).one('hidden.bs.modal', openViewer).modal('hide');
                return;
            }

            openViewer();
        });

        modal.on('hidden.bs.modal', function () {
            frame.setAttribute('src', 'about:blank');
        });
    });
</script>
@endonce
