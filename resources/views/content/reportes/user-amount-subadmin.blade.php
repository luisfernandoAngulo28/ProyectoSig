@extends('master::layouts/admin-2')

@section('content')
    <div class="card" style="padding-left: 20px; padding-right: 20px; padding-bottom: 20px">
        <div class="mt-3">
            <h1>Reporte de Monto Gastado por Usuario</h1>
            <p>Esta tabla contiene un resume del monto gastado por los usuarios.</p>
        </div>


        <div class="row d-flex justify-content-start  ">
            <div class="mt-2 ml-2">
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
                    <td>Nombre del User</td>
                    <td>Numero de Celular</td>
                    <td>Total Gastado</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $key => $item)
                    @php
                        $user = \App\User::where('id', $item->user_id)->first();

                    @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->cellphone }}</td>
                        <td>{{ $item->total_amount }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>
    </div>
@endsection
