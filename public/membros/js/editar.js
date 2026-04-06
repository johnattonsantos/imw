$(document).ready(function () {
    // Máscaras
    var maskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 13 ? '+00 (00) 00000-0000' : '+00 (00) 0000-00009';
    },
        options = {
            onKeyPress: function (val, e, field, options) {
                field.mask(maskBehavior.apply({}, arguments), options);
            }
        };

    $('#telefone_preferencial, #telefone_alternativo, #telefone_whatsapp').mask(maskBehavior, options);
    $('#cpf').mask('000.000.000-00');
    $('#cep').mask('00000-000');

    function limparCampoInvalidoSeVazio($campo) {
        if (!$campo || !$campo.length) {
            return;
        }

        var id = $campo.attr('id');
        var valor = ($campo.val() || '').toString();

        if (['telefone_preferencial', 'telefone_alternativo', 'telefone_whatsapp', 'cpf', 'cep'].includes(id)) {
            var apenasDigitos = valor.replace(/\D/g, '');
            if (apenasDigitos.length === 0) {
                $campo.val('');
                valor = '';
            }
        }

        if (valor.trim() === '') {
            $campo.removeClass('is-invalid');
            if (typeof $campo[0].setCustomValidity === 'function') {
                $campo[0].setCustomValidity('');
            }
        }
    }

    $('body').on('input change search', 'input, select, textarea', function () {
        limparCampoInvalidoSeVazio($(this));
    });

    // Adicionar e remover linhas na tabela de Ministério
    // Função para adicionar linha de ministério
    $('body').on('click', '.adicionar-linha-ministerial', function () {
        var novaLinha = $('#ministerial-tbody tr:last').clone();
        novaLinha.find('input').val('');
        novaLinha.find('select').prop('selectedIndex', 0);
        $('#ministerial-tbody').append(novaLinha);
        atualizarBotoesMinisterial();
    });

    // Função para remover linha de ministério com confirmação
    $('body').on('click', '.apagar-linha-ministerial', function () {
        if ($('#ministerial-tbody tr').length > 1) {
            var confirma = confirm("Tem certeza que deseja remover esta linha?");
            if (confirma) {
                $(this).closest('tr').remove();
                atualizarBotoesMinisterial();
            }
        } else {
            alert("Não é possível remover a última linha.");
        }
    });

    // Adicionar e remover linhas na tabela de Formação Eclesiástica
    // Função para adicionar linha de formação
    $('body').on('click', '.adicionar-formacaoEclesiastica', function () {
        var novaLinha = $('#formacao-tbody tr:last').clone();
        novaLinha.find('input').val('');
        novaLinha.find('select').prop('selectedIndex', 0);
        $('#formacao-tbody').append(novaLinha);
        atualizarBotoesFormacao();
    });

    // Função para remover linha de formação com confirmação
    $('body').on('click', '.apagar-linha-formacaoEclesiastica', function () {
        if ($('#formacao-tbody tr').length > 1) {
            var confirma = confirm("Tem certeza que deseja remover esta linha?");
            if (confirma) {
                $(this).closest('tr').remove();
                atualizarBotoesFormacao();
            }
        } else {
            alert("Não é possível remover a última linha.");
        }
    });

    // Função para adicionar linha de gceuIndex
    $('body').on('click', '.adicionar-linha-gceuIndex', function () {
        var novaLinha = $('#gceuIndex-tbody tr:last').clone();
        novaLinha.find('input').val('');
        novaLinha.find('select').prop('selectedIndex', 0);
        $('#gceuIndex-tbody').append(novaLinha);
        atualizarBotoesGceuIndex();
    });

    // Função para remover linha de gceuIndex com confirmação
    $('body').on('click', '.apagar-linha-gceuIndex', function () {
        if ($('#gceuIndex-tbody tr').length > 1) {
            var confirma = confirm("Tem certeza que deseja remover esta linha?");
            if (confirma) {
                $(this).closest('tr').remove();
                atualizarBotoesGceuIndex();
            }
        } else {
            alert("Não é possível remover a última linha.");
        }
    });

    // Função para atualizar a visibilidade dos botões de ministério
    function atualizarBotoesGceuIndex() {
        $('.adicionar-linha-gceuIndex, .apagar-linha-gceuIndex').hide();
        $('#gceuIndex-tbody tr:last .adicionar-linha-gceuIndex').show();
        if ($('#gceuIndex-tbody tr').length > 1) {
            $('.apagar-linha-gceuIndex').show();
        }
    }

    // Função para atualizar a visibilidade dos botões de ministério
    function atualizarBotoesMinisterial() {
        $('.adicionar-linha-ministerial, .apagar-linha-ministerial').hide();
        $('#ministerial-tbody tr:last .adicionar-linha-ministerial').show();
        if ($('#ministerial-tbody tr').length > 1) {
            $('.apagar-linha-ministerial').show();
        }
    }

    // Função para atualizar a visibilidade dos botões de formação
    function atualizarBotoesFormacao() {
        $('.adicionar-formacaoEclesiastica, .apagar-linha-formacaoEclesiastica').hide();
        $('#formacao-tbody tr:last .adicionar-formacaoEclesiastica').show();
        if ($('#formacao-tbody tr').length > 1) {
            $('.apagar-linha-formacaoEclesiastica').show();
        }
    }

    // Inicializar visibilidade dos botões
    atualizarBotoesMinisterial();
    atualizarBotoesFormacao();


    //Foto Usuario
    // Adiciona um evento de clique ao botão "upload-picture"
    $('#upload-picture').click(function (event) {
        event.preventDefault();
        // Dispara o clique no elemento de entrada de arquivo
        $('#upload-picture-input').click();
    });

    $('#upload-picture-input').change(function () {
        // Verifica se o arquivo foi selecionado
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function (event) {
            // Define a URL da imagem carregada como a fonte da imagem de visualização
            $('#user-picture').attr('src', event.target.result);
        };
        reader.readAsDataURL(file);

    });

    // Retorna foto para o padrão
    $('#delete-picture').click(function (event) {
        event.preventDefault();
        $('#user-picture').attr('src', "https://via.placeholder.com/150");
        $('#upload-picture-input').val('');
    });
});
