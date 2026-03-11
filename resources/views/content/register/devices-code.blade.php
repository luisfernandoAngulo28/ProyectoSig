@extends('master::layouts/admin-2')

@section('content')
    {{-- {{ dd($ingredients['amount_product_output']) }} --}}
    <h2 class="d-flex justify-content-start align-items-center mb-2 ml-2 content-header-title">
        Codigos de dispositivos
    </h2>


    <div class="modal__header">
        <div class="row m-0 p-0" style="align-items: stretch;">
            {{-- RECETAS --}}
            <div class="col-md-12">
                <div class="modal__body">
                    <section>
                        <div class="match-height">
                            <div class="card" style="height: 88%;">
                                <div class="card-header mt-0">
                                    <div class="table-responsive">
                                        <h3>Lista de todos los codigos...
                                        </h3>
                                        <table
                                            class="mb-2 admin-table table table table-striped table-bordered table-hover dt-responsive dataTable no-footer dtr-inline collapsed">
                                            <thead>
                                                <tr>
                                                    <th scope="row">Nro</th>
                                                    <th>Conductor</th>
                                                    <th>Codigo</th>
                                                    <th>Eliminar</th>
                                                </tr>
                                            </thead>
                                            <tbody ">

                                                          @foreach ($drivercodes as
                                                $codes)
                                                <tr>
                                                    <td scope="row"> {{ $codes['id'] }}</td>
                                                    <td scope="row"> {{ $codes['driver']['email'] }}</td>
                                                    <td scope="row"> {{ $codes['device_code'] }}</td>
                                                    <td class="ineditable delete"> <a
                                                            href='{{ '/customer-admin/delete-code/' . $codes['id'] }}'>
                                                            Eliminar</a></td>

                                                </tr>
                                                @endforeach
                                            </tbody>


                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>


        </div>
    </div>
@endsection
