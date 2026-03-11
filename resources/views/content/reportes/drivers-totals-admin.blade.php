@extends('master::layouts/admin')

@section('content')
<h1>Reporte de Conductores</h1>
<div>
  <p>Esta tabla contiene un resumen de los totales por Conductor.</p>
  <div class="row">
    <a href="{{ url('admin/reportes-totales/excel/') }}">Descargar Excel</a>
  </div>
  <table id="general-list" class="admin-table editable-list table table-striped table-bordered table-hover @if(config('solunes.list_horizontal_scroll')=='true') nowrap @else dt-responsive @endif">
    <thead>
      <tr class="title">
        <td>#</td>
        <td>Nombre del Conductor</td>
        <td>Total Horas Activas</td>
        <td>Total de Horas Inactivas</td>
        <td>Total de Calificaciones</td>
        <td>Monto Generado</td>
      </tr>
    </thead>
    <tbody>
        @foreach($items as $key => $item)
        <tr>
          <td>{{ $key+1 }}</td>
          <td>{{ $item->driver->user->name }}</td>
          <td>{{ $item->total_active_time  }}</td>
          <td>{{ $item->total_busy_time }}</td>
          <td>{{ $item->driver->user->total_ratings}}</td>
          <td>{{ $item->total_price}}</td>
         
        </tr>
        @endforeach
    </tbody>
    
  </table>
</div>
@endsection
