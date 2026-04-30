  <div class="tab-pane fade show active" id="border-top-dados-pessoal" role="tabpanel"
      aria-labelledby="border-top-dados-pessoais">
      <blockquote class="blockquote">
          <div class="row mb-4">
              <!-- Coluna para foto do usuário -->
              <div class="col-xl-3 text-center">
                  <input type="hidden" name="membro_id" value="{{ $pessoa->id }}">
                  <img src="{{ $pessoa->foto ? asset($pessoa->foto) : asset('theme/images/sem-foto.jpg') }}"
                      id="user-picture" class="rounded-circle img-fluid mb-3" alt="Foto do usuário" width="150px"
                      height="150px;">
                  <div>
                      <button class="btn btn-primary btn-sm" id="upload-picture">Trocar Foto</button>
                      <input type="file" name="foto" class="d-none" id="upload-picture-input">
                      <button class="btn btn-danger btn-sm" id="delete-picture">Apagar Foto</button>
                  </div>
              </div>
              <!-- Conteúdo do formulário -->
              <div class="col-xl-9">
                  <div class="form-group @error('nome') has-error @enderror">
                      <div class="row mb-4">
                          <div class="col-xl-9">
                              <label for="nome">* Nome</label>
                              <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                  id="nome" name="nome" value="{{ old('nome', $pessoa->nome) }}" maxlength="100">
                              @error('nome')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>

                            <div class="col-xl-3">
                                <label for="novo_convertido">* Novo Convertido</label>
                                <select class="form-control" id="novo_convertido" name="novo_convertido">
                                <option value="">Selecione</option>
                                <option value="S" {{ $pessoa->novo_convertido == 'S' ? 'selected' : '' }}>Sim</option>
                                <option value="N" {{ $pessoa->novo_convertido == 'N' ? 'selected' : '' }}>Não</option>
                                </select>
                                @error('novo_convertido')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-4">
                          <div class="col-xl-4">
                              <label for="nascimento">* Data de Nascimento</label>
                              <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror"
                                  id="data_nascimento" name="data_nascimento"
                                  value="{{ old('data_nascimento', optional($pessoa->data_nascimento)->format('Y-m-d')) }}" min="1900-01-01">
                              @error('data_nascimento')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>

                          <div class="col-xl-4">
                              <label for="sexo">Sexo</label>
                              <select class="form-control" id="sexo" name="sexo">
                                  <option value="">Selecione</option>
                                  <option value="M" {{ $pessoa->sexo == 'M' ? 'selected' : '' }}>Masculino</option>
                                  <option value="F" {{ $pessoa->sexo == 'F' ? 'selected' : '' }}>Feminino</option>
                              </select>
                              @error('sexo')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                          <div class="col-xl-4">
                              <label for="estado_civil">* Estado Civíl</label>
                              <select class="form-control @error('estado_civil') is-invalid @enderror" id="estado_civil"
                                  name="estado_civil">
                                  <option value="">Selecione</option>
                                  <option value="S" {{ $pessoa->estado_civil == 'S' ? 'selected' : '' }}>Solteiro
                                  </option>
                                  <option value="C" {{ $pessoa->estado_civil == 'C' ? 'selected' : '' }}>Casado
                                  </option>
                                  <option value="D" {{ $pessoa->estado_civil == 'D' ? 'selected' : '' }}>
                                      Divorciado</option>
                                  <option value="V" {{ $pessoa->estado_civil == 'V' ? 'selected' : '' }}>Viúvo
                                  </option>
                              </select>
                              @error('estado_civil')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>

                      <div class="row mb-4">
                          <div class="col-xl-4">
                              <label for="nacionalidade">* Nacionalidade</label>
                              <select class="form-control @error('estado_civil') is-invalid @enderror"
                                  id="nacionalidade" name="nacionalidade">
                                  <option value="">Selecione</option>
                                  @php
                                      //Colocar no banco de dados
                                      $paises = [
                                          'BR' => 'Brasil',
                                          'AF' => 'Afeganistão',
                                          'ZA' => 'África do Sul',
                                          'AL' => 'Albânia',
                                          'DE' => 'Alemanha',
                                          'AD' => 'Andorra',
                                          'AO' => 'Angola',
                                          'AI' => 'Anguilla',
                                          'AQ' => 'Antártida',
                                          'AG' => 'Antígua e Barbuda',
                                          'SA' => 'Arábia Saudita',
                                          'DZ' => 'Argélia',
                                          'AR' => 'Argentina',
                                          'AM' => 'Armênia',
                                          'AW' => 'Aruba',
                                          'AU' => 'Austrália',
                                          'AT' => 'Áustria',
                                          'AZ' => 'Azerbaijão',
                                          'BS' => 'Bahamas',
                                          'BD' => 'Bangladesh',
                                          'BB' => 'Barbados',
                                          'BH' => 'Bahrein',
                                          'BY' => 'Belarus',
                                          'BE' => 'Bélgica',
                                          'BZ' => 'Belize',
                                          'BJ' => 'Benin',
                                          'BM' => 'Bermudas',
                                          'BO' => 'Bolívia',
                                          'BA' => 'Bósnia e Herzegovina',
                                          'BW' => 'Botsuana',
                                          'BN' => 'Brunei',
                                          'BG' => 'Bulgária',
                                          'BF' => 'Burkina Faso',
                                          'BI' => 'Burundi',
                                          'BT' => 'Butão',
                                          'CV' => 'Cabo Verde',
                                          'KH' => 'Camboja',
                                          'CM' => 'Camarões',
                                          'CA' => 'Canadá',
                                          'KZ' => 'Cazaquistão',
                                          'EA' => 'Ceuta e Melilha',
                                          'TD' => 'Chade',
                                          'CL' => 'Chile',
                                          'CN' => 'China',
                                          'CY' => 'Chipre',
                                          'SG' => 'Cingapura',
                                          'CO' => 'Colômbia',
                                          'KM' => 'Comores',
                                          'CG' => 'Congo - Brazzaville',
                                          'CD' => 'Congo - Kinshasa',
                                          'KP' => 'Coreia do Norte',
                                          'KR' => 'Coreia do Sul',
                                          'CI' => 'Costa do Marfim',
                                          'CR' => 'Costa Rica',
                                          'HR' => 'Croácia',
                                          'CU' => 'Cuba',
                                          'CW' => 'Curaçao',
                                          'DG' => 'Diego Garcia',
                                          'DK' => 'Dinamarca',
                                          'DJ' => 'Djibuti',
                                          'DM' => 'Dominica',
                                          'EG' => 'Egito',
                                          'SV' => 'El Salvador',
                                          'AE' => 'Emirados Árabes Unidos',
                                          'EC' => 'Equador',
                                          'ER' => 'Eritreia',
                                          'SK' => 'Eslováquia',
                                          'SI' => 'Eslovênia',
                                          'ES' => 'Espanha',
                                          'US' => 'Estados Unidos',
                                          'EE' => 'Estônia',
                                          'ET' => 'Etiópia',
                                          'EU' => 'União Europeia',
                                          'FO' => 'Ilhas Faroe',
                                          'FJ' => 'Fiji',
                                          'PH' => 'Filipinas',
                                          'FI' => 'Finlândia',
                                          'FR' => 'França',
                                          'GA' => 'Gabão',
                                          'GM' => 'Gâmbia',
                                          'GH' => 'Gana',
                                          'GE' => 'Geórgia',
                                          'GS' => 'Geórgia do Sul e Ilhas Sandwich do Sul',
                                          'GI' => 'Gibraltar',
                                          'GD' => 'Granada',
                                          'GR' => 'Grécia',
                                          'GL' => 'Groênlandia',
                                          'GP' => 'Guadalupe',
                                          'GU' => 'Guam',
                                          'GT' => 'Guatemala',
                                          'GG' => 'Guernsey',
                                          'GY' => 'Guiana',
                                          'GF' => 'Guiana Francesa',
                                          'GN' => 'Guiné',
                                          'GW' => 'Guiné Bissau',
                                          'GQ' => 'Guiné Equatorial',
                                          'HT' => 'Haiti',
                                          'NL' => 'Holanda',
                                          'HN' => 'Honduras',
                                          'HK' => 'Hong Kong, RAE da China',
                                          'HU' => 'Hungria',
                                          'YE' => 'Iêmen',
                                          'IM' => 'Ilha de Man',
                                          'NF' => 'Ilha Norfolk',
                                          'AX' => 'Ilhas Åland',
                                          'KY' => 'Ilhas Cayman',
                                          'CC' => 'Ilhas Cocos (Keeling)',
                                          'CK' => 'Ilhas Cook',
                                          'FO' => 'Ilhas Faroe',
                                          'HM' => 'Ilhas Heard e McDonald',
                                          'FK' => 'Ilhas Malvinas',
                                          'MP' => 'Ilhas Marianas do Norte',
                                          'MH' => 'Ilhas Marshall',
                                          'UM' => 'Ilhas Menores Distantes dos EUA',
                                          'PN' => 'Ilhas Pitcairn',
                                          'SB' => 'Ilhas Salomão',
                                          'TC' => 'Ilhas Turks e Caicos',
                                          'VG' => 'Ilhas Virgens Britânicas',
                                          'VI' => 'Ilhas Virgens dos EUA',
                                          'IN' => 'Índia',
                                          'ID' => 'Indonésia',
                                          'IR' => 'Irã',
                                          'IQ' => 'Iraque',
                                          'IE' => 'Irlanda',
                                          'IS' => 'Islândia',
                                          'IL' => 'Israel',
                                          'IT' => 'Itália',
                                          'JM' => 'Jamaica',
                                          'JP' => 'Japão',
                                          'JE' => 'Jersey',
                                          'JO' => 'Jordânia',
                                          'XK' => 'Kosovo',
                                          'KW' => 'Kuwait',
                                          'LA' => 'Laos',
                                          'LS' => 'Lesoto',
                                          'LV' => 'Letônia',
                                          'LB' => 'Líbano',
                                          'LR' => 'Libéria',
                                          'LY' => 'Líbia',
                                          'LI' => 'Liechtenstein',
                                          'LT' => 'Lituânia',
                                          'LU' => 'Luxemburgo',
                                          'MO' => 'Macau, RAE da China',
                                          'MK' => 'Macedônia do Norte',
                                          'MG' => 'Madagascar',
                                          'MW' => 'Malawi',
                                          'MV' => 'Maldivas',
                                          'MY' => 'Malásia',
                                          'ML' => 'Mali',
                                          'MT' => 'Malta',
                                          'MA' => 'Marrocos',
                                          'MQ' => 'Martinica',
                                          'MU' => 'Maurício',
                                          'MR' => 'Mauritânia',
                                          'YT' => 'Mayotte',
                                          'MX' => 'México',
                                          'MM' => 'Mianmar (Birmânia)',
                                          'FM' => 'Micronésia',
                                          'MZ' => 'Moçambique',
                                          'MD' => 'Moldávia',
                                          'MC' => 'Mônaco',
                                          'MN' => 'Mongólia',
                                          'ME' => 'Montenegro',
                                          'MS' => 'Montserrat',
                                          'NA' => 'Namíbia',
                                          'NR' => 'Nauru',
                                          'NP' => 'Nepal',
                                          'NI' => 'Nicarágua',
                                          'NE' => 'Níger',
                                          'NG' => 'Nigéria',
                                          'NU' => 'Niue',
                                          'NO' => 'Noruega',
                                          'NC' => 'Nova Caledônia',
                                          'NZ' => 'Nova Zelândia',
                                          'OM' => 'Omã',
                                          'PW' => 'Palau',
                                          'PA' => 'Panamá',
                                          'PG' => 'Papua-Nova Guiné',
                                          'PK' => 'Paquistão',
                                          'PY' => 'Paraguai',
                                          'PE' => 'Peru',
                                          'PF' => 'Polinésia Francesa',
                                          'PL' => 'Polônia',
                                          'PR' => 'Porto Rico',
                                          'PT' => 'Portugal',
                                          'KE' => 'Quênia',
                                          'KG' => 'Quirguistão',
                                          'KI' => 'Quiribati',
                                          'QO' => 'Oceania Remota',
                                          'GB' => 'Reino Unido',
                                          'CF' => 'República Centro-Africana',
                                          'CZ' => 'República Tcheca',
                                          'DO' => 'República Dominicana',
                                          'RE' => 'Reunião',
                                          'RO' => 'Romênia',
                                          'RW' => 'Ruanda',
                                          'RU' => 'Rússia',
                                          'EH' => 'Saara Ocidental',
                                          'KN' => 'Saint Kitts e Nevis',
                                          'SM' => 'San Marino',
                                          'PM' => 'Saint Pierre e Miquelon',
                                          'VC' => 'São Vicente e Granadinas',
                                          'SH' => 'Santa Helena',
                                          'LC' => 'Santa Lúcia',
                                          'ST' => 'São Tomé e Príncipe',
                                          'SN' => 'Senegal',
                                          'SL' => 'Serra Leoa',
                                          'RS' => 'Sérvia',
                                          'SC' => 'Seychelles',
                                          'SX' => 'Sint Maarten',
                                          'SY' => 'Síria',
                                          'SO' => 'Somália',
                                          'LK' => 'Sri Lanka',
                                          'SZ' => 'Suazilândia',
                                          'SD' => 'Sudão',
                                          'SS' => 'Sudão do Sul',
                                          'SE' => 'Suécia',
                                          'CH' => 'Suíça',
                                          'SR' => 'Suriname',
                                          'SJ' => 'Svalbard e Jan Mayen',
                                          'TJ' => 'Tajiquistão',
                                          'TH' => 'Tailândia',
                                          'TW' => 'Taiwan',
                                          'TZ' => 'Tanzânia',
                                          'IO' => 'Território Britânico do Oceano Índico',
                                          'TF' => 'Territórios Franceses do Sul',
                                          'PS' => 'Territórios palestinos',
                                          'TL' => 'Timor-Leste',
                                          'TG' => 'Togo',
                                          'TK' => 'Tokelau',
                                          'TO' => 'Tonga',
                                          'TT' => 'Trinidad e Tobago',
                                          'TN' => 'Tunísia',
                                          'TM' => 'Turcomenistão',
                                          'TC' => 'Turks e Caicos',
                                          'TV' => 'Tuvalu',
                                          'UA' => 'Ucrânia',
                                          'UG' => 'Uganda',
                                          'UY' => 'Uruguai',
                                          'UZ' => 'Uzbequistão',
                                          'VU' => 'Vanuatu',
                                          'VA' => 'Vaticano',
                                          'VE' => 'Venezuela',
                                          'VN' => 'Vietnã',
                                          'WF' => 'Wallis e Futuna',
                                          'ZM' => 'Zâmbia',
                                          'ZW' => 'Zimbábue',
                                      ];

                                  @endphp
                                  @foreach ($paises as $sigla => $nome)
                                      <option value="{{ $sigla }}"
                                          {{ old('nacionalidade', $pessoa->nacionalidade) == $sigla ? 'selected' : '' }}>
                                          {{ $nome }}</option>
                                  @endforeach
                              </select>
                              @error('nacionalidade')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>

                          <div class="col-xl-4">
                              <label for="naturalidade">* Naturalidade</label>
                              <input type="text" class="form-control @error('naturalidade') is-invalid @enderror"
                                  id="naturalidade" name="naturalidade"
                                  value="{{ old('naturalidade', $pessoa->naturalidade) }}" maxlength="50">
                              @error('naturalidade')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>

                          <div class="col-xl-4">
                              <label for="uf">* UF</label>
                              <select class="form-control @error('uf') is-invalid @enderror" id="uf" name="uf" {{ $pessoa->nacionalidade != 'BR' ? 'disabled' : '' }}>
                                  <option value="">Selecione</option>
                                  @php
                                      //Colocar no banco de dados , esta estranho assim
                                      $ufs = [
                                          'AC' => 'Acre',
                                          'AL' => 'Alagoas',
                                          'AP' => 'Amapá',
                                          'AM' => 'Amazonas',
                                          'BA' => 'Bahia',
                                          'CE' => 'Ceará',
                                          'DF' => 'Distrito Federal',
                                          'ES' => 'Espírito Santo',
                                          'GO' => 'Goiás',
                                          'MA' => 'Maranhão',
                                          'MT' => 'Mato Grosso',
                                          'MS' => 'Mato Grosso do Sul',
                                          'MG' => 'Minas Gerais',
                                          'PA' => 'Pará',
                                          'PB' => 'Paraíba',
                                          'PR' => 'Paraná',
                                          'PE' => 'Pernambuco',
                                          'PI' => 'Piauí',
                                          'RJ' => 'Rio de Janeiro',
                                          'RN' => 'Rio Grande do Norte',
                                          'RS' => 'Rio Grande do Sul',
                                          'RO' => 'Rondônia',
                                          'RR' => 'Roraima',
                                          'SC' => 'Santa Catarina',
                                          'SP' => 'São Paulo',
                                          'SE' => 'Sergipe',
                                          'TO' => 'Tocantins',
                                      ];
                                  @endphp
                                  @foreach ($ufs as $key => $value)
                                      <option value="{{ $key }}"
                                          {{ old('uf', $pessoa->uf) == $key ? 'selected' : '' }}>{{ $value }}
                                      </option>
                                  @endforeach
                              </select>
                              @error('uf')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>

                      <div class="row mb-4">
                          <div class="col-xl-3">
                              <label for="escolaridade_id">Escolaridade</label>
                              <select class="form-control" name="escolaridade_id" id="escolaridade_id">
                                  <option value="">Selecione</option>
                                  @foreach ($formacoes as $formacao)
                                      <option value="{{ $formacao->id }}"
                                          {{ $formacao->id == old('escolaridade_id', $pessoa->escolaridade_id) ? 'selected' : '' }}>
                                          {{ $formacao->descricao }}
                                      </option>
                                  @endforeach
                              </select>
                              @error('escolaridade_id')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>

                          <div class="col-xl-3">
                              <label for="profissao">Profissão</label>
                              <input type="text" class="form-control" id="profissao" name="profissao"
                                  value="{{ old('profissao', $pessoa->profissao) }}" maxlength="100">
                              @error('profissao')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>

                          <div class="col-xl-6">
                              <label for="funcao_eclesiastica_id">Função Eclesiástica</label>
                              <select class="form-control" name="funcao_eclesiastica_id" id="funcao_eclesiastica_id">
                                  <option value="">Selecione</option>
                                  @foreach ($funcoesEclesiasticas as $funcaoEclesiastica)
                                      <option value="{{ $funcaoEclesiastica->id }}"
                                          {{ $funcaoEclesiastica->id == old('funcao_eclesiastica_id', $pessoa->funcao_eclesiastica_id) ? 'selected' : '' }}>
                                          {{ $funcaoEclesiastica->descricao }}
                                      </option>
                                  @endforeach
                              </select>
                              @error('funcao_eclesiastica_id')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>

                      <div class="row mb-4">
                          <div class="col-xl-3">
                              <label for="cpf">CPF</label>
                              <input type="text" class="form-control @error('cpf') is-invalid @enderror"
                                  id="cpf" name="cpf" value="{{ old('cpf', $pessoa->cpf) }}" maxlength="100">
                              @error('cpf')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>

                          <div class="col-xl-3">
                              <label for="tipo_documento">Tipo Documento</label>
                              <select class="form-control" name="tipo_documento" id="tipo_documento">
                                  <option value="">Selecione</option>
                                  <option value="RG">RG</option>
                                  <option value="CNH">CNH</option>
                              </select>
                              @error('tipo_documento')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>

                          <div class="col-xl-3">
                              <label for="documento">Documento</label>
                              <input type="text" class="form-control" id="documento" name="documento"
                                  value="{{ old('documento', $pessoa->documento) }}" maxlength="30">
                              @error('documento')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>

                          <div class="col-xl-3">
                              <label for="documento_complemento">Complemento Documento</label>
                              <input type="text" class="form-control" id="documento_complemento"
                                  name="documento_complemento"
                                  value="{{ old('documento_complemento', $pessoa->documento_complemento) }}" maxlength="50">
                              @error('documento_complemento')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>
                      <div class="row mb-4">
                          <div class="col-xl-4">
                              <label for="data_conversao">Data de Conversão</label>
                              <input type="date" class="form-control @error('data_conversao') is-invalid @enderror"
                                  id="data_conversao" name="data_conversao"
                                  value="{{ old('data_conversao', $pessoa->data_conversao) }}">
                              @error('data_conversao')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>

                          <div class="col-xl-4">
                              <label for="data_batismo">Data de Batismo</label>
                              <input type="date" class="form-control @error('data_batismo') is-invalid @enderror"
                                  id="data_batismo" name="data_batismo"
                                  value="{{ old('data_batismo', $pessoa->data_batismo) }}">
                              @error('data_batismo')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>

                          <div class="col-xl-4">
                              <label for="data_batismo_espirito">Data de Batismo Espirito Santo</label>
                              <input type="date"
                                  class="form-control @error('data_batismo_espirito') is-invalid @enderror"
                                  id="data_batismo_espirito" name="data_batismo_espirito"
                                  value="{{ old('data_batismo_espirito', $pessoa->data_batismo_espirito) }}">
                              @error('data_batismo_espirito')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>
                      <div class="row mb-4">
                          <div class="col-lg-6">
                              <label for="historico">Pastor Oficiante</label>
                              <input type="text" class="form-control" id="historico" name="historico"
                                  value="{{ old('historico', $pessoa->historico) }}" maxlength="100">
                              @error('historico')
                                  <span class="help-block text-danger">{{ $message }}</span>
                              @enderror
                          </div>

                          <div class="form-group mb-4 col-md-6">
                                <label class="control-label">Congregação:</label>
                                <select id="congregacao_id" name="congregacao_id" class="form-control @error('congregacao_id') is-invalid @enderror" >
                                    <option value="" {{ !$pessoa->congregacao_id ? 'selected' : '' }}>Sede</option>
                                    @foreach ($congregacoes as $congregacao)
                                        <option value="{{ $congregacao->id }}" {{$pessoa->congregacao_id == $congregacao->id ? 'selected' : '' }}>{{ $congregacao->nome }}</option>
                                    @endforeach
                                </select>
                                @error('congregacao_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                      </div>
                  </div>
              </div>
          </div>
      </blockquote>
  </div>

@push('tab-scripts')
<script>
    $('#nacionalidade').change(function () {
      if ($(this).val() != 'BR') {
        $('#uf').attr('disabled', true);
        $('#uf').val('');
        $('#naturalidade').val('');
      } else {
        $('#uf').attr('disabled', false);
      }
    });
</script>
@endpush
