<!-- TABELA -->

  

      <div id="conteudo-lista">
        <div class="widget-content widget-content-area" style="padding-top: 0;">
           <div id="conteudo">
            <div class="table-responsive">
              <div class="table_fixed_box">
                <table class="table table-bordered table-striped table-hover mb-4">
                    <thead>
                        <tr>
                            <th width=100px>
                              {{ __('DISTRITO') }}
                              <span id="distrito-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="distrito-down"></i>
                              </span>
                            </th>
                            <th width=100px>
                              {{ __('IGREJA') }}
                              <span id="igreja-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="igreja-down"></i>
                              </span>
                            </th>
                            <th width=55px>
                              {{ __('ROL') }}
                              <span id="rol-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="rol-down"></i>
                              </span>
                            </th>
                            <th width=100px>
                              {{ __('NOME') }}
                              <span id="nome-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="nome-down"></i>
                              </span>
                            </th>
                            <th width=100px>
                              {{ __('TELEFONE') }}
                              <span id="telefone-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="telefone-down"></i>
                              </span>
                            </th>
                            <th width=100px>
                              {{ __('SITUAÇÃO') }}
                              <span id="situacao-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="situacao-down"></i>
                              </span>
                            </th>
                            <th width=90px>
                              {{ __('VÍNCULO') }}
                              <span id="vinculo-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="vinculo-down"></i>
                              </span>
                            </th>
                            <th width=120px>
                              {{ __('NASCIMENTO') }}
                              <span id="nascimento-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="nascimento-down"></i>
                              </span>
                            </th>
                            <th width=110px>{{ __('RECEPÇÃO') }}
                              <span id="recepcao-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="recepcao-down"></i>
                              </span>
                            </th>
                            <th width=100px>
                              {{ __('MODO') }}
                              <span id="modo-recepcao-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="modo-recepcao-down"></i>
                              </span>
                            </th>
                            <th width=100px>
                              {{ __('EXCLUSÃO') }}
                              <span id="exclusao-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="exclusao-down"></i>
                              </span>
                            </th>
                            <th width=100px>
                              {{ __('MODO') }}
                              <span id="modo-exclusao-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="modo-exclusao-down"></i>
                              </span>
                            </th>
                            <th width=100px>
                              {{ __('LOCAL') }}
                              <span id="local-order">
                                  <i class="fa-solid fa-caret-right float-right cursor-pointer-ordem ordenar" data-ordem="local-down"></i>
                              </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                      @forelse ($membros as $membro)
                        <tr>
                          <td>{{ $membro->distrito_nome }}</td>
                          <td>{{ $membro->igreja_nome }}</td>
                          <td>{{ $membro->rol_atual ?? 0 }}</td>
                          <td>{{ $membro->nome }}</td>
                          <td>{{ formatStr($membro->telefone, '## (##) #####-####') }}</td>
                          <td>
                            @if($membro->vinculo == 'M')
                              {{ $membro->status == 'A' ? 'ATIVO' : 'INATIVO' }}
                            @else
                              {{ $membro->statusText }}
                            @endif
                          </td>
                          <td>{{ $membro->vinculoText }}</td>
                          <td>{{ optional($membro->data_nascimento)->format('d/m/Y') }}</td>
                          <td>
                            @if($membro->vinculo == 'M')
                                {{ $membro->dt_recepcao ? formatDate($membro->dt_recepcao) : '-' }}
                            @else
                              {{ optional($membro->created_at)->format('d/m/Y') }}
                            @endif
                          </td>
                          <td>{{ $membro->recepcao_modo ? $membro->recepcao_modo : '-' }}</td>
                          <td>
                            @if($membro->vinculo == 'M')
                              {{ $membro->dt_exclusao ? formatDate($membro->dt_exclusao) : '-'}}
                            @else
                              {{ optional($membro->deleted_at)->format('d/m/Y') }}
                            @endif
                          </td>
                          <td>{{ $membro->exclusao_modo ? $membro->exclusao_modo : '-' }}</td>
                          <td>{{ $membro->congregacao_nome ?? 'SEDE' }}</td>
                        </tr>
                      @empty
                      <tr>
                          <td colspan="13">{{ __('Nenhum dado encontrado.') }}</td>
                      </tr>
                      @endforelse
                    </tbody>
                </table>
            </div>
            </div>
            <p style="background-color: #ececec85;">
                 Total de registros encontrados: {{ request()->totalPorPagina ? (request()->totalPorPagina > $total ? $total : request()->totalPorPagina) : 10 }} @if($total > 0)na página {{ request()->page ? request()->page : 1}} de {{ $total }}  @endif
            </p>
            <div>
               <select class="form-control " style="width: 10%; float: left;  font-size: 15px; height: 36px; padding: .0 1rem;" id="totalPorPagina">
                  @php
                      $numeros = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100];
                  @endphp
                  @foreach($numeros as $numero)
                      <option value='{{ $numero }}' {{ request()->input('totalPorPagina') == $numero ? 'selected' : '' }}>{{ $numero }}</option>
                  @endforeach
              </select>
              <input type="hidden" id="ordem" value="{{ request()->ordem }}">
              @if($links)
                  {{ $links->links('vendor.pagination.index') }}
              @endif
            </div>
        </div>
    </div>
  </div>


<script>
  $(document).ready(function(){
    let ordem = $('#ordem').val()
    if(ordem == 'distrito-down'){
        $('#distrito-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="distrito-down"></i>`)
    }else if(ordem == 'distrito-up'){
        $('#distrito-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="distrito-up"></i>`)
    }else if(ordem == 'igreja-down'){
        $('#igreja-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="igreja-down"></i>`)
    }else if(ordem == 'igreja-up'){
        $('#igreja-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="igreja-up"></i>`)
    }else if(ordem == 'rol-down'){
        $('#rol-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="rol-down"></i>`)
    }else if(ordem == 'rol-up'){
        $('#rol-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="rol-up"></i>`)
    }else if(ordem == 'nome-down'){
        $('#nome-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="nome-down"></i>`)
    }else if(ordem == 'nome-up'){
        $('#nome-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="telefone-up"></i>`)
    }else if(ordem == 'telefone-down'){
        $('#telefone-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="telefone-down"></i>`)
    }else if(ordem == 'telefone-up'){
        $('#telefone-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="telefone-up"></i>`)
    }else if(ordem == 'situacao-down'){
        $('#situacao-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="situacao-down"></i>`)
    }else if(ordem == 'situacao-up'){
        $('#situacao-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="situacao-up"></i>`)
    }else if(ordem == 'vinculo-down'){
        $('#vinculo-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="vinculo-down"></i>`)
    }else if(ordem == 'vinculo-up'){
        $('#vinculo-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="vinculo-up"></i>`)
    }else if(ordem == 'nascimento-down'){
        $('#nascimento-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="nascimento-down"></i>`)
    }else if(ordem == 'nascimento-up'){
        $('#nascimento-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="nascimento-up"></i>`)
    }else if(ordem == 'recepcao-down'){
        $('#recepcao-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="recepcao-down"></i>`)
    }else if(ordem == 'recepcao-up'){
        $('#recepcao-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="recepcao-up"></i>`)
    }else if(ordem == 'modo-recepcao-down'){
        $('#modo-recepcao-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="modo-recepcao-down"></i>`)
    }else if(ordem == 'modo-recepcao-up'){
        $('#modo-recepcao-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="modo-recepcao-up"></i>`)
    }else if(ordem == 'exclusao-down'){
        $('#exclusao-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="exclusao-down"></i>`)
    }else if(ordem == 'exclusao-up'){
        $('#exclusao-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="exclusao-up"></i>`)
    }else if(ordem == 'modo-exclusao-down'){
        $('#modo-exclusao-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="modo-exclusao-down"></i>`)
    }else if(ordem == 'modo-exclusao-up'){
        $('#modo-exclusao-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="modo-exclusao-up"></i>`)
    }else if(ordem == 'local-down'){
        $('#local-order').html(`<i class="fa-solid fa-caret-down float-right cursor-pointer-ordem ordenar" data-ordem="local-down"></i>`)
    }else if(ordem == 'local-up'){
        $('#local-order').html(`<i class="fa-solid fa-caret-up cursor-pointer-ordem float-right ordenar" data-ordem="local-up"></i>`)
    }
  });
</script>