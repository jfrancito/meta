
<div class="col-md-8">

      <div class="col-md-12">
          <p><b>Nombre Proveedor :</b> {{$compra->NOM_PROVEEDOR}}</p>
      </div>
      <div class="col-md-4">
        <p><b>Serie :</b> {{$compra->NRO_SERIE}}</p>
      </div>
      <div class="col-md-4">
        <p><b>Correlativo :</b> {{$compra->NRO_DOC}}</p>
      </div>
      <div class="col-md-4">
        <p><b>Fecha Emision :</b> {{date_format(date_create($compra->FEC_EMISION), 'd-m-Y')}}</p>
      </div>

      <div class="col-md-4">
        <p><b>Tipo de cliente :</b> {{$compra->NOM_MONEDA}} <i class="mdi mdi-check-circle"></i></p>
      </div>

      <div class="col-md-4">
        <p><b>Moneda :</b> {{$compra->NOM_MONEDA}} <i class="mdi mdi-check-circle"></i></p>
      </div>

</div>

<div class="col-md-4">
  <div class="panel">
    <div class="panel-heading">Asiento</div>
    <div class="panel-body">




    </div>
  </div>
</div>
