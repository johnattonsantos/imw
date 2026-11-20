<style>
    @media (min-width: 576px) {
        #mensagemPastorModal .modal-dialog {
            max-width: 600px;
        }
    }
    #mensagemPastorModal .modal-title {
        font-size: 22px;
    }
    #mensagemPastorModal .modal-body {
        padding: 24px;
        font-size: 16px;
        line-height: 1.7;
    }
</style>
<div class="modal fade" id="mensagemPastorModal" tabindex="-1" role="dialog" aria-labelledby="mensagemPastorTitulo" aria-describedby="mensagemPastorTexto" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mensagemPastorTitulo">Pastor, conte conosco!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="mensagemPastorTexto">
                <p>Estamos acompanhando seu trabalho no sistema. Ele é seu aliado no ministério, para acompanhar a evolução de sua igreja, analisar o progresso de cada setor e planejar suas ações.</p>
                <p class="mb-0">Parabéns, estamos aqui para lhe dar suporte. Deus o abençoe!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Continuar</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#mensagemPastorModal').modal('show');
    });
</script>
