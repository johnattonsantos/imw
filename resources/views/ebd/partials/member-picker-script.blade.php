<script>
    (function() {
        const searchUrl = @json(route('ebd.buscar-membro'));
        const allowedVinculos = @json($allowedVinculos ?? []);
        const createUrl = @json(route('ebd.cadastrar-visitante'));
        const csrf = @json(csrf_token());

        const input = document.getElementById('membroBuscaInput');
        const button = document.getElementById('membroBuscaBtn');
        const results = document.getElementById('membroBuscaResultados');
        const hidden = document.getElementById('membro_id');
        const selectedBox = document.getElementById('membroSelecionado');
        const selectedName = document.getElementById('membroSelecionadoNome');

        const showSelected = (membro) => {
            hidden.value = membro.id;
            selectedName.textContent = `${membro.nome} (${membro.vinculo ?? '-'})`;
            selectedBox.classList.remove('d-none');
        };

        const renderResults = (items) => {
            results.innerHTML = '';
            if (!items.length) {
                results.innerHTML = `<li class="list-group-item text-muted">Nenhum membro encontrado.</li>`;
                return;
            }

            items.forEach((m) => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `<div>
                    <strong>${m.nome}</strong><br>
                    <small>CPF: ${m.cpf ?? '-'} | Tel: ${m.telefone_preferencial ?? '-'} | E-mail: ${m.email_preferencial ?? '-'}</small>
                </div>`;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm btn-primary';
                btn.textContent = 'Selecionar';
                btn.addEventListener('click', () => showSelected(m));

                li.appendChild(btn);
                results.appendChild(li);
            });
        };

        const doSearch = async () => {
            const q = input.value.trim();
            const vinculosQuery = Array.isArray(allowedVinculos) && allowedVinculos.length
                ? `&vinculos=${encodeURIComponent(allowedVinculos.join(','))}`
                : '';
            try {
                const response = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}${vinculosQuery}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const payload = await response.json();
                renderResults(payload.data ?? []);
            } catch (error) {
                results.innerHTML = `<li class="list-group-item text-danger">Não foi possível realizar a busca agora. Tente novamente.</li>`;
            }
        };

        button?.addEventListener('click', doSearch);
        input?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                doSearch();
            }
        });

        const btnCreate = document.getElementById('btnCadastrarVisitanteRapido');
        const errBox = document.getElementById('visitanteRapidoErro');

        btnCreate?.addEventListener('click', async () => {
            errBox.classList.add('d-none');
            errBox.textContent = '';

            const body = {
                nome: document.getElementById('vr_nome').value,
                sexo: document.getElementById('vr_sexo').value,
                cpf: document.getElementById('vr_cpf').value,
                telefone_preferencial: document.getElementById('vr_telefone').value,
                email_preferencial: document.getElementById('vr_email').value,
                data_nascimento: document.getElementById('vr_data_nascimento').value,
            };

            const response = await fetch(createUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body)
            });

            const payload = await response.json();
            if (!response.ok) {
                errBox.textContent = payload.message ?? 'Erro ao cadastrar visitante.';
                errBox.classList.remove('d-none');
                return;
            }

            showSelected(payload.membro);
            if (window.jQuery) {
                window.jQuery('#visitanteRapidoModal').modal('hide');
            }
        });
    })();
</script>
