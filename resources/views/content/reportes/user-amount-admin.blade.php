@extends('master::layouts/admin')

@section('content')
<h1>Reporte de Monto Gastado por Usuario</h1>
<div>
  <p>Esta tabla contiene un resume del monto gastado por los usuarios.</p>
  <div class="row">
    <a href="{{ url('admin/reportes-organization-total/excel/') }}">Descargar Excel</a>
  </div>
  <table id="general-list" class="admin-table editable-list table table-striped table-bordered table-hover @if(config('solunes.list_horizontal_scroll')=='true') nowrap @else dt-responsive @endif">
    <thead>
      <tr class="title">
        <td>#</td>
        <td>Nombre del User</td>
        <td>Numero de Celular</td>
        <td>Total Gastado</td>
      </tr>
    </thead>
    <tbody>
        @foreach($items as $key => $item)
        <tr>
          <td>{{ $key+1 }}</td>
          <td>{{ $item->name }}</td>
          <td>{{ $item->cellphone  }}</td>
          <td>{{ $item->monto_gastado }}</td>
        </tr>
        @endforeach
    </tbody>
    
  </table>
</div>
@endsection
