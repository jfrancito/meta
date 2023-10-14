<div class="panel panel-default">
  <div class="tab-container">
    <ul class="nav nav-tabs">

      <li class="active"><a href="#saldoinicial" data-toggle="tab">Saldo Inicial</a></li>
    </ul>
    <div class="tab-content">

      <div id="saldoinicial" class="tab-pane active cont">
        @include('kardex.ajax.asaldoinicialcascara')
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