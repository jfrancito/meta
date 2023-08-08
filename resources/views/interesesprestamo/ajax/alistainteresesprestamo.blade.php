<div class="panel panel-default">
    <div class="tab-container">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#intereses-prestamo" data-toggle="tab">Lista Pagos Intereses Pr&eacute;stamo</a></li>
      </ul>
  
      <div class="tab-content">
        <div id="intereses-prestamo" class="tab-pane active cont">
          @include('interesesprestamo.ajax.interesesprestamo')
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