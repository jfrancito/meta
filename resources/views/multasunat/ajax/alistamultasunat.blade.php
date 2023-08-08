<div class="panel panel-default">
    <div class="tab-container">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#multa-sunat" data-toggle="tab">Lista Pagos Multa Sunat</a></li>
      </ul>
  
      <div class="tab-content">
        <div id="multa-sunat" class="tab-pane active cont">
          @include('multasunat.ajax.multasunat')
        </div>
      </div>
      
    </div>
  </div>
  <input type="hidden" name="periodo_registrado" id="periodo_registrado" value='{{$periodo_id}}'>
  
  @if(isset($ajax))
    <script type="text/javascript">
      $(document).ready(function(){
         App.dataTables();
      });
    </script> 
  @endif