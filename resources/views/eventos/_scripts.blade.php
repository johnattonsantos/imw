<script src="{{ asset('gceu/tinymce/tinymce.min.js') }}?time={{ time() }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const uploadImageUrl = "{{ route('eventos.upload-image') }}";
        const csrfToken = "{{ csrf_token() }}";

        if (typeof window.tinymce !== 'undefined') {
            window.tinymce.init({
                selector: '#descricao, #observacoes',
                height: 280,
                menubar: true,
                language: 'pt_BR',
                theme: 'modern',
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code',
                ],
                toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor | image link table | code',
                relative_urls: false,
                remove_script_host: false,
                image_advtab: true,
                automatic_uploads: true,
                images_upload_handler: function (blobInfo, success, failure, progress) {
                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', uploadImageUrl);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

                    xhr.upload.onprogress = function (event) {
                        progress(event.loaded / event.total * 100);
                    };

                    xhr.onload = function () {
                        if (xhr.status < 200 || xhr.status >= 300) {
                            failure('Falha no upload: HTTP ' + xhr.status);
                            return;
                        }

                        const json = JSON.parse(xhr.responseText || '{}');
                        if (!json.location) {
                            failure('Resposta inválida do servidor');
                            return;
                        }

                        success(json.location);
                    };

                    xhr.onerror = function () {
                        failure('Falha no upload da imagem.');
                    };

                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.send(formData);
                },
            });
        }

        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function () {
                if (typeof window.tinymce !== 'undefined') {
                    window.tinymce.triggerSave();
                }
            });
        });

        function applyMasks(scope) {
            if (!window.jQuery || typeof window.jQuery.fn.mask !== 'function') {
                return;
            }

            const $scope = window.jQuery(scope || document);
            const celularMask = function (value) {
                return value.replace(/\D/g, '').length > 10 ? '(00) 00000-0000' : '(00) 0000-00009';
            };
            const celularOptions = {
                onKeyPress: function (value, event, field, options) {
                    field.mask(celularMask.apply({}, arguments), options);
                }
            };

            $scope.find('.data-ptbr').mask('00/00/0000');
            $scope.find('.hora-ptbr').mask('00:00');
            $scope.find('.contato-celular').mask(celularMask, celularOptions);
        }

        applyMasks(document);

        const container = document.getElementById('equipe-container');
        const addButton = document.getElementById('add-equipe-row');
        const template = document.getElementById('equipe-row-template');

        if (!container || !addButton || !template) {
            return;
        }

        let nextIndex = container.querySelectorAll('.equipe-row').length;

        function normalizeLeaderSelection(changed) {
            if (!changed.checked) {
                return;
            }

            container.querySelectorAll('.equipe-lider').forEach(function (checkbox) {
                if (checkbox !== changed) {
                    checkbox.checked = false;
                }
            });
        }

        addButton.addEventListener('click', function () {
            const html = template.innerHTML.replaceAll('__name__', 'equipe[' + nextIndex + ']');
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const row = wrapper.firstElementChild;
            container.appendChild(row);
            applyMasks(row);
            nextIndex++;
        });

        container.addEventListener('click', function (event) {
            if (!event.target.classList.contains('remove-equipe-row')) {
                return;
            }

            const rows = container.querySelectorAll('.equipe-row');
            if (rows.length === 1) {
                event.target.closest('.equipe-row').querySelectorAll('input[type="text"]').forEach(function (input) {
                    input.value = '';
                });
                event.target.closest('.equipe-row').querySelectorAll('select').forEach(function (select) {
                    select.value = '';
                });
                event.target.closest('.equipe-row').querySelector('.equipe-lider').checked = false;
                return;
            }

            event.target.closest('.equipe-row').remove();
        });

        container.addEventListener('change', function (event) {
            if (event.target.classList.contains('equipe-lider')) {
                normalizeLeaderSelection(event.target);
            }
        });
    });
</script>
