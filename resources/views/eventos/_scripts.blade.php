<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery && typeof window.jQuery.fn.mask === 'function') {
            window.jQuery('.data-ptbr').mask('00/00/0000');
            window.jQuery('.hora-ptbr').mask('00:00');
        }

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
            container.appendChild(wrapper.firstElementChild);
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
