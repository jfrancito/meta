<div class="panel panel-default">
  <div class="tab-container">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#ctransicion" data-toggle="tab">Lista compra transicion</a></li>
      <li><a href="#cterminado" data-toggle="tab">Lista compra terminado</a></li>
    </ul>

    <div class="tab-content">
      <div id="ctransicion" class="tab-pane active cont">
        @include('compras.ajax.acompratransision')
      </div>
      <div id="cterminado" class="tab-pane cont">
        @include('compras.ajax.acompraterminado')
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







