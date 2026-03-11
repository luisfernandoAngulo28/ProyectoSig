@extends('master::layouts/admin')

@section('content')
<h1>Reporte de Solicitudes por empresa</h1>
<div>
  <p>Esta tabla contiene un resume de los totales de solicitudes por empresa.</p>
  <div class="row">
    <a href="{{ url('admin/reportes-organization-total/excel/') }}">Descargar Excel</a>
  </div>
  <table id="general-list" class="admin-table editable-list table table-striped table-bordered table-hover @if(config('solunes.list_horizontal_scroll')=='true') nowrap @else dt-responsive @endif">
    <thead>
      <tr class="title">
        <td>#</td>
        <td>Nombre de la Empresa</td>
        <td>Total de Solicitudes</td>
        <td>Total de Solicitudes de tipo Viaje</td>
      </tr>
    </thead>
    <tbody>
        @foreach($items as $key => $item)
        <tr>
          <td>{{ $key+1 }}</td>
          <td>{{ $item->name }}</td>
          <td>{{ $item->request_count  }}</td>
          <td>{{ $item->total_viajes }}</td>
        </tr>
        @endforeach
    </tbody>
    
  </table>
</div>
@endsection
