<style>
#showImage {
    /* border-radius: 10% */
    width: 210px;
    height: 268px;
}
</style>

<div class="tab-pane fade show active" id="border-top-dados-pessoal" role="tabpanel"
    aria-labelledby="border-top-dados-pessoais">
    <blockquote class="blockquote">
        <div class="row mb-4">
            <!-- Coluna para foto do usuário -->
            <div class="col-xl-3 text-center">
                <div>

                    <div class="col-md-12">
                        <img id="showImage" src="{{ url('theme/images/sem-foto.jpg')}}" alt="{{ __('Admin') }}" width="150" height="150">
                    </div>
                    <div class="form-group col-md-12" style="margin-top: 10px;">
                        <label for="image" class="form-label"></label>
                        <div class="input_container">
                            <input name="image" type="file" class="d-none" id="upload-picture-input">
                            <button class="btn btn-primary btn-sm" id="upload-picture">{{ __('Trocar Foto') }}</button>
                            <button class="btn btn-danger btn-sm" id="delete-picture">{{ __('Apagar Foto') }}</button>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Conteúdo do formulário -->
            <div class="col-xl-9">
                <div class="form-group @error('nome') has-error @enderror">
                    <input type="hidden" id="regiao_id" value="23">
                    <div class="row mb-4">
                        <div class="col-xl-6">
                            <label for="nome">{{ __('* Nome') }}</label>
                            <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome"
                                name="nome" value="{{ old('nome') }}" maxlength="100">
                            @error('nome')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-xl-6">
                            <label for="email">{{ __('Email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" maxlength="255" value="{{ old('email') }}">
                        </div>
                        @error('email')
                            <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row mb-4">
                        @php
                            $situacaoSelecionada = $situacoes->firstWhere('id', old('situacao'));
                            $cpfOpcional = $situacaoSelecionada ? $situacaoSelecionada->cpfOpcional() : false;
                        @endphp
                        <div class="col-xl-4">
                            <label for="cpf" id="cpf-label">{{ __($cpfOpcional ? 'CPF' : '* CPF') }}</label>
                            <input type="text" class="form-control @error('cpf') is-invalid @enderror" id="cpf"
                                name="cpf" value="{{ old('cpf') }}" maxlength="100" @unless($cpfOpcional) required @endunless>
                            @error('cpf')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-xl-4">
                            <label for="categoria">{{ __('* Categoria') }}</label>
                            <select class="form-control @error('categoria') is-invalid @enderror" type="text"
                                id="categoria" name="categoria">
                                <option value="">{{ __('Selecione') }}</option>
                                <option value="missionária" {{ old('categoria') == 'missionária' ? 'selected' : '' }}>
                                    {{ __('Missionária') }}</option>
                                <option value="pastor" {{ old('categoria') == 'pastor' ? 'selected' : '' }}>{{ __('Pastor') }}</option>
                                <option value="ministro" {{ old('categoria') == 'ministro' ? 'selected' : '' }}>{{ __('Ministro') }}
                                </option>
                                <option value="bispo" {{ old('categoria') == 'bispo' ? 'selected' : '' }}>{{ __('Bispo') }}</option>
                            </select>
                            @error('categoria')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-xl-4">
                            <label for="categoria">{{ __('* Situação') }}</label>
                            <select class="form-control @error('categoria') is-invalid @enderror" type="text"
                                id="situacao" name="situacao">
                                <option value="">{{ __('Selecione') }}</option>
                                @foreach($situacoes as $situacao)
                                    <option value="{{  $situacao->id }}" data-cpf-opcional="{{ $situacao->cpfOpcional() ? '1' : '0' }}" {{ old('situacao') ==  $situacao->id ? 'selected' : '' }}> {{  $situacao->descricao }}</option>
                                @endforeach
                            </select>
                            @error('situacao')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-xl-4">
                            <label for="sexo">{{ __('* Sexo') }}</label>
                            <select class="form-control @error('sexo') is-invalid @enderror" id="sexo" name="sexo">
                                <option value="">{{ __('Selecione') }}</option>
                                <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>{{ __('Masculino') }}</option>
                                <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>{{ __('Feminino') }}</option>
                            </select>
                            @error('sexo')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-xl-4">
                            <label for="estado_civil">{{ __('* Estado Civil') }}</label>
                            <select class="form-control @error('estado_civil') is-invalid @enderror" id="estado_civil"
                                name="estado_civil">
                                <option value="">{{ __('Selecione') }}</option>
                                <option value="S" {{ old('estado_civil') == 'S' ? 'selected' : '' }}>{{ __('Solteiro') }}</option>
                                <option value="C" {{ old('estado_civil') == 'C' ? 'selected' : '' }}>{{ __('Casado') }}</option>
                                <option value="D" {{ old('estado_civil') == 'D' ? 'selected' : '' }}>{{ __('Divorciado') }}
                                </option>
                                <option value="V" {{ old('estado_civil') == 'V' ? 'selected' : '' }}>{{ __('Viúvo') }}</option>
                            </select>
                            @error('estado_civil')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-xl-4">
                            <label for="formacao_id">{{ __('* Formação') }}</label>
                            <select class="form-control @error('formacao_id') is-invalid @enderror" name="formacao_id"
                                id="formacao_id">
                                <option value="">{{ __('Selecione') }}</option>
                                @foreach ($formacoes as $formacao)
                                    <option value="{{ $formacao->id }}"
                                        {{ old('formacao_id') == $formacao->id ? 'selected' : '' }}>{{ $formacao->nivel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('formacao_id')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12 mt-3 col-md-4">
                            <label for="nome_mae">{{ __('Nome da Mãe') }}</label>
                            <input type="text" class="form-control" id="nome_mae" name="nome_mae" maxlength="50"
                                value="{{ old('nome_mae') }}">
                        </div>
                        <div class="col-12 mt-3 col-md-4">
                            <label for="nome_pai">{{ __('Nome do Pai') }}</label>
                            <input type="text" class="form-control" id="nome_pai" name="nome_pai" maxlength="50"
                                value="{{ old('nome_pai') }}">
                        </div>
                        <div class="col-12 mt-3 col-md-4">
                            <label for="data_nascimento">{{ __('Nascimento') }}</label>
                            <input type="date" class="form-control @error('email') is-invalid @enderror" id="data_nascimento" name="data_nascimento"
                                value="{{ old('data_nascimento') }}">
                            @error('data_nascimento')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @else
                                <span class="help-block text-danger text-danger-nascimento"></span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-12 mt-3 col-md-3">
                            <label for="data_consagracao">{{ __('* Data de Consagração') }}</label>
                            <input type="date" class="form-control @error('data_consagracao') is-invalid @enderror"
                                id="data_consagracao" name="data_consagracao"
                                value="{{ old('data_consagracao') }}">
                            @error('data_consagracao')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @else
                                <span class="help-block text-danger text-danger-consagracao"></span>
                            @enderror
                        </div>
                        <div class="col-12 mt-3 col-md-3">
                            <label for="data_ordenacao">{{ __('Data de Ordenação') }}</label>
                            <input type="date" class="form-control @error('data_ordenacao') is-invalid @enderror"
                                id="data_ordenacao" name="data_ordenacao"
                                value="{{ old('data_ordenacao') }}">
                            @error('data_ordenacao')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @else
                                <span class="help-block text-danger text-danger-ordenacao"></span>
                            @enderror
                        </div>
                        <div class="col-12 mt-3 col-md-3">
                            <label for="data_integralizacao">{{ __('Data de Integralização') }}</label>
                            <input type="date" class="form-control @error('data_integralizacao') is-invalid @enderror"
                                id="data_integralizacao" name="data_integralizacao"
                                value="{{ old('data_integralizacao') }}">
                            @error('data_integralizacao')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @else
                                <span class="help-block text-danger text-danger-integralizacao"></span>
                            @enderror
                        </div>
                        <div class="col-12 mt-3 col-md-3">
                            <label for="rol">{{ __('* Rol') }}</label>
                            <input type="number" class="form-control @error('rol') is-invalid @enderror" id="rol" name="rol"
                                value="{{ old('rol') }}" onKeyPress="if(this.value.length==4) return false;">
                            @error('rol')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </blockquote>
</div>

<script>

    $("#data_consagracao").change(function(e){
        let data_nascimento = $('#data_nascimento').val();
        if(!data_nascimento){
            toastr.error("Primeiro informe a data de nascimento.")
            $('.text-danger-nascimento').html('Informe a data de nascimento')
			$('#data_nascimento').addClass('is-invalid')
            $('.text-danger').show();
            $("#data_consagracao").val('')
			return false
		}

        let dataNascimento = new Date($('#data_nascimento').val());
		let dataConsagracao = new Date($("#data_consagracao").val());
		if (dataConsagracao < dataNascimento) {
			toastr.error("A data da consagração tem que ser maior que a data de nascimento.")
            $("#data_consagracao").val('')
            $('.text-danger-consagracao').html('A data da consagração tem que ser maior que a data de nascimento.')
			$('#data_consagracao').addClass('is-invalid')
            $('.text-danger-consagracao').show();
            $('.text-danger-nascimento').hide()
			return false
		}else{
            $('#data_consagracao').removeClass('is-invalid')
            $('.text-danger-consagracao').hide();
        }
	})

    $("#data_ordenacao").change(function(e){
        let data_consagracao = $('#data_consagracao').val();
        if(!data_consagracao){
            toastr.error("Primeiro informe a data de consagração.")
            $('.text-danger-consagracao').html('Informe a data da consagração')
			$('#data_consagracao').addClass('is-invalid')
            $('.text-danger-consagracao').show();
            $("#data_ordenacao").val('')
			return false
		}

        let dataConsagracao = new Date($('#data_consagracao').val());
		let dataOrdenacao = new Date($("#data_ordenacao").val());
		if (dataOrdenacao < dataConsagracao) {
			toastr.error("A data da ordenação tem que ser maior que a data da consagração.")
            $("#data_ordenacao").val('')
            $('.text-danger-ordenacao').html('A data da ordenação tem que ser maior que a data da consagração.')
			$('#data_ordenacao').addClass('is-invalid')
            $('.text-danger-ordenacao').show();
            $('.text-danger-nascimento').hide()
			return false
		}else{
            $('#data_ordenacao').removeClass('is-invalid')
            $('.text-danger-ordenacao').hide();
        }
	})

    $("#data_integralizacao").change(function(e){
        let data_consagracao = $('#data_consagracao').val();
        if(!data_consagracao){
            toastr.error("Primeiro informe a data de consagração.")
            $('.text-danger-consagracao').html('Informe a data da consagração')
			$('#data_consagracao').addClass('is-invalid')
            $('.text-danger-consagracao').show();
            $("#data_integralizacao").val('')
			return false
		}

        let dataConsagracao = new Date($('#data_consagracao').val());
		let dataIntegralizacao = new Date($("#data_integralizacao").val());
		if (dataIntegralizacao < dataConsagracao) {
			toastr.error("A data da integralizacao tem que ser maior que a data da consagração.")
            $("#data_integralizacao").val('')
            $('.text-danger-integralizacao').html('A data da integralizacao tem que ser maior que a data da consagração.')
			$('#data_integralizacao').addClass('is-invalid')
            $('.text-danger-integralizacao').show();
            $('.text-danger-nascimento').hide()
			return false
		}else{
            $('#data_integralizacao').removeClass('is-invalid')
            $('.text-danger-integralizacao').hide();
        }
	})

    $("#data_nascimento").change(function(e){
        let dataNascimento = new Date($('#data_nascimento').val());
        let dataConsagracao = new Date($('#data_consagracao').val());
		if (dataNascimento > dataConsagracao) {
			toastr.error("A data de nascimento tem que ser menor que a data da consagração.")
            $("#data_nascimento").val('')
            $('.text-danger-nascimento').html('A data de nascimento tem que ser menor que a data da consagração.')
			$('#data_nascimento').addClass('is-invalid')
            $('.text-danger-nascimento').show();
			return false
		}else{
            $('#data_nascimento').removeClass('is-invalid')
            $('.text-danger-nascimento').hide();
        }

    })

</script>
