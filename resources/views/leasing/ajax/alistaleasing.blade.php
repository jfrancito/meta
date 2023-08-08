<div class="panel panel-default">
    <div class="tab-container">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#leasing" data-toggle="tab">Lista Pagos Leasing</a></li>
      </ul>
  
      <div class="tab-content">
        <div id="leasing" class="tab-pane active cont">
          @include('leasing.ajax.leasing')
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