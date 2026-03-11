@extends('master::layouts/admin-2')

@section('content')
    <div class="card" style="padding-left: 20px; padding-right: 20px; padding-bottom: 20px">

        <div class="mt-3">
            <h1>Reporte de totales de Conductores</h1>
            <p>Esta tabla contiene un resumen de los totales del Conductor.</p>
        </div>


        <div class="row d-flex justify-content-start  ">
            <div class="ml-2 mt-2">
                <div class="dt-buttons btn-group">
                    <a href="{{ url('customer-admin/reportes-totales/excel/') }}"
                        class="btn btn-outline-primary waves-effect waves-light">Descargar Excel</a>
                </div>
            </div>
        </div>


        <table id="general-list"
            class="admin-table editable-list table table-striped table-bordered table-hover @if (config('solunes.list_horizontal_scroll') == 'true') nowrap @else dt-responsive @endif">
            <thead>
                <tr class="title">
                    <td>Id</td>
                    <td>Nombre del Conductor</td>
                    <td>Total Horas Activas</td>
                    <td>Total de Horas Inactivas</td>
                    <td>Total de Calificaciones</td>
                    <td>Monto Generado</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->driver->user->name }}</td>
                        <td>{{ $item->total_active_time }}</td>
                        <td>{{ $item->total_busy_time }}</td>
                        <td>{{ $item->driver->user->total_ratings }}</td>
                        <td>{{ $item->total_price }}</td>

                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>
    </div>
@endsection
