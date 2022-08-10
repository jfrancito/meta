<div class="panel panel-default">
  <div class="tab-container">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#inventariofinal" data-toggle="tab">Inventario Final</a></li>
      <li><a href="#saldoinicial" data-toggle="tab">Saldo Inicial</a></li>
      <li><a href="#ventas" data-toggle="tab">Ventas</a></li>
      <li><a href="#compras" data-toggle="tab">Compras</a></li>
      <li><a href="#inventariofinalc" data-toggle="tab">Costo Inventario Final</a></li>
      <li><a href="#ventasc" data-toggle="tab">Costos Ventas </a></li>
      <li><a href="#comprasc" data-toggle="tab">Costos Compras </a></li>
    </ul>
    <div class="tab-content">
      <div id="inventariofinal" class="tab-pane active cont">
        @include('kardex.ajax.ainventariofinal')
      </div>
      <div id="saldoinicial" class="tab-pane cont">
        @include('kardex.ajax.asaldoinicial')
      </div> 
      <div id="ventas" class="tab-pane cont">
        @include('kardex.ajax.aventas')
      </div>
      <div id="compras" class="tab-pane">
        @include('kardex.ajax.acompras')
      </div>
      <div id="inventariofinalc" >
        @include('kardex.ajax.ainventariofinalcosto')
      </div>

      <div id="ventasc" class="tab-pane cont">
        @include('kardex.ajax.aventascosto')
      </div>

      <div id="comprasc" class="tab-pane cont">
        @include('kardex.ajax.acomprascosto')
      </div>

    </div>
  </div>
</div>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif