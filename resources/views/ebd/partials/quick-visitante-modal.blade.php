<div class="modal fade" id="visitanteRapidoModal" tabindex="-1" role="dialog" aria-labelledby="visitanteRapidoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="visitanteRapidoModalLabel">Cadastro Rápido de Visitante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="visitanteRapidoErro"></div>

                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" id="vr_nome" class="form-control">
                </div>
                <div class="form-group">
                    <label>Sexo *</label>
                    <select id="vr_sexo" class="form-control">
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>CPF</label>
                    <input type="text" id="vr_cpf" class="form-control">
                </div>
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" id="vr_telefone" class="form-control">
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" id="vr_email" class="form-control">
                </div>
                <div class="form-group mb-0">
                    <label>Data de nascimento</label>
                    <input type="date" id="vr_data_nascimento" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnCadastrarVisitanteRapido">Salvar e Vincular</button>
            </div>
        </div>
    </div>
</div>
