<div class="panel panel-default">
  <div class="tab-container">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#inventariofinal" data-toggle="tab">Inventario Final</a></li>
      <li><a href="#saldoinicial" data-toggle="tab">Saldo Inicial</a></li>
      <li><a href="#ventas" data-toggle="tab">Ventas</a></li>
      <li><a href="#compras" data-toggle="tab">Compras</a></li>
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