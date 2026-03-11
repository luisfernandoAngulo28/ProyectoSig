@extends('master::layouts/admin-2')

@section('content')
    <div class="card" style="padding-left: 20px; padding-right: 20px; padding-bottom: 20px">

        <div class="mt-3">
            <h1>Reporte de Solicitudes por empresa</h1>
            <p>Esta tabla contiene un resume de los totales de solicitudes por empresa.</p>
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
                    <td>Nombre de la Empresa</td>
                    <td>Total de Solicitudes</td>
                    <td>Total de Solicitudes de tipo Viaje</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->request_count }}</td>
                        <td>{{ $item->total_viajes }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>
    </div>
@endsection
