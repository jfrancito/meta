<div class="panel panel-default">
  <div class="tab-container">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#mobilidad" data-toggle="tab">Lista registro mobilidad</a></li>
      <li><a href="#cc1" data-toggle="tab">Lista Mobilidad <b>(GENERAL)</b></a></li>
      <li><a href="#cc2" data-toggle="tab">Lista Mobilidad <b>(REPARACION)</b></a></li>
    </ul>

    <div class="tab-content">
      <div id="mobilidad" class="tab-pane active cont">
        @include('movilidad.ajax.amobiliddad')
      </div>
      <div id="cc1" class="tab-pane cont">
        @include('movilidad.ajax.acc1')
      </div> 
      <div id="cc2" class="tab-pane cont">
        @include('movilidad.ajax.acc2')
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






