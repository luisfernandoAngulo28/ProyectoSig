@extends('master::layouts/admin')

@section('content')
<style>
    .detail-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 25px; margin-bottom: 20px; }
    .detail-card h3 { color: #333; border-bottom: 2px solid #7c4dff; padding-bottom: 10px; margin-bottom: 20px; }
    .detail-row { display: flex; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
    .detail-label { font-weight: bold; color: #555; min-width: 200px; }
    .detail-value { color: #333; flex: 1; }
    .doc-images { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 10px; }
    .doc-images .doc-item { text-align: center; }
    .doc-images img { max-width: 250px; max-height: 200px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; transition: transform 0.2s; }
    .doc-images img:hover { transform: scale(1.05); border-color: #7c4dff; }
    .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 14px; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d4edda; color: #155724; }
    .btn-approve { background: #28a745; color: #fff; border: none; padding: 12px 30px; border-radius: 5px; font-size: 16px; cursor: pointer; margin-right: 10px; display: inline-block; }
    .btn-approve:hover { background: #218838; color: #fff; text-decoration: none; }
    .btn-reject { background: #dc3545; color: #fff; border: none; padding: 12px 30px; border-radius: 5px; font-size: 16px; cursor: pointer; display: inline-block; }
    .btn-reject:hover { background: #c82333; color: #fff; text-decoration: none; }
    .btn-back { background: #6c757d; color: #fff; border: none; padding: 10px 25px; border-radius: 5px; cursor: pointer; margin-bottom: 20px; display: inline-block; }
    .btn-back:hover { background: #545b62; color: #fff; text-decoration: none; }
    .btn-whatsapp { background: #25D366; color: #fff; border: none; padding: 12px 30px; border-radius: 5px; font-size: 16px; cursor: pointer; display: inline-block; margin-top: 15px; }
    .btn-whatsapp:hover { background: #20bd5a; color: #fff; text-decoration: none; }
    .vehicle-card { background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 10px; border-left: 4px solid #7c4dff; }
    .no-image { background: #f0f0f0; width: 200px; height: 150px; display: flex; align-items: center; justify-content: center; border-radius: 8px; color: #999; }
    .approval-section { background: #f8f9fa; border-radius: 8px; padding: 25px; margin-top: 20px; text-align: center; border: 2px dashed #7c4dff; }
    .img-modal { display: none; position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); justify-content: center; align-items: center; }
    .img-modal img { max-width: 90%; max-height: 90%; border-radius: 8px; }
    .img-modal.active { display: flex; }
    .whatsapp-section { background: #e8f5e9; border: 2px solid #25D366; border-radius: 8px; padding: 20px; margin-top: 15px; }
</style>

<div class="container-fluid" style="padding: 20px;">

    @if(session('message_success'))
        <div class="alert alert-success">{!! session('message_success') !!}</div>
    @endif
    @if(session('message_error'))
        <div class="alert alert-danger">{{ session('message_error') }}</div>
    @endif

    <a href="{{ url('customer-admin/model-list/driver') }}" class="btn btn-back">
        <i class="fa fa-arrow-left"></i> Volver a la lista de Conductores
    </a>

    <h2 style="margin-bottom: 5px;">Solicitud de Registro - Conductor #{{ $driver->id }}</h2>
    <p style="color: #777; margin-bottom: 20px;">Registrado el: {{ $driver->created_at ? $driver->created_at->format('d/m/Y H:i') : 'N/A' }}</p>

    {{-- ESTADO ACTUAL --}}
    <div style="margin-bottom: 20px;">
        @if($driver->active == 1)
            <span class="status-badge status-approved"><i class="fa fa-check-circle"></i> APROBADO</span>
            @if($driver->free_trial_until)
                <span style="margin-left: 10px; color: #155724;">Gratis hasta: {{ \Carbon\Carbon::parse($driver->free_trial_until)->format('d/m/Y') }}</span>
            @endif
        @else
            <span class="status-badge status-pending"><i class="fa fa-clock-o"></i> PENDIENTE DE APROBACIÓN</span>
        @endif
    </div>

    {{-- DATOS PERSONALES --}}
    <div class="detail-card">
        <h3><i class="fa fa-user"></i> Datos Personales</h3>
        <div class="detail-row">
            <span class="detail-label">Nombre Completo:</span>
            <span class="detail-value">{{ $driver->first_name }} {{ $driver->last_name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Correo Electrónico:</span>
            <span class="detail-value">{{ $driver->email ?: 'No registrado' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Celular:</span>
            <span class="detail-value">{{ $driver->cellphone ?: 'No registrado' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">CI:</span>
            <span class="detail-value">{{ $driver->ci_number ?: 'No registrado' }} {{ $driver->ci_exp ? '('.$driver->ci_exp.')' : '' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Género:</span>
            <span class="detail-value">{{ $driver->gender == 'male' ? 'Masculino' : 'Femenino' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Ciudad:</span>
            <span class="detail-value">{{ $driver->city ? $driver->city->name : ($driver->city_id ?: 'No registrada') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Empresa/Organización:</span>
            <span class="detail-value">{{ $driver->organization ? $driver->organization->name : 'Libre / Sin empresa' }}</span>
        </div>

        @if($driver->image)
        <div style="margin-top: 15px;">
            <strong>Foto del Conductor:</strong><br>
            <img src="{{ Storage::url('driver-image/normal/'.$driver->image) }}" alt="Foto conductor" style="max-width: 200px; border-radius: 8px; margin-top: 8px; cursor: pointer;" onclick="openModal(this.src)">
        </div>
        @endif
    </div>

    {{-- LICENCIA DE CONDUCIR --}}
    <div class="detail-card">
        <h3><i class="fa fa-id-card"></i> Licencia de Conducir</h3>
        <div class="detail-row">
            <span class="detail-label">Número de Licencia:</span>
            <span class="detail-value">{{ $driver->license_number ?: 'No registrado' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha de Vencimiento:</span>
            <span class="detail-value">
                @if($driver->license_expiration_date)
                    {{ \Carbon\Carbon::parse($driver->license_expiration_date)->format('d/m/Y') }}
                    @php
                        $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($driver->license_expiration_date), false);
                    @endphp
                    @if($daysLeft < 0)
                        <span style="color: red; font-weight: bold;">(VENCIDA hace {{ abs($daysLeft) }} días)</span>
                    @elseif($daysLeft <= 30)
                        <span style="color: orange; font-weight: bold;">(Vence en {{ $daysLeft }} días)</span>
                    @else
                        <span style="color: green;">({{ $daysLeft }} días restantes)</span>
                    @endif
                @else
                    No registrada
                @endif
            </span>
        </div>
        <div class="doc-images">
            <div class="doc-item">
                <p><strong>Licencia (Frente)</strong></p>
                @if($driver->license_front_image)
                    <img src="{{ Storage::url('driver-license_front_image/normal/'.$driver->license_front_image) }}" alt="Licencia frente" onclick="openModal(this.src)">
                @else
                    <div class="no-image">Sin imagen</div>
                @endif
            </div>
            <div class="doc-item">
                <p><strong>Licencia (Reverso)</strong></p>
                @if($driver->license_back_image)
                    <img src="{{ Storage::url('driver-license_back_image/normal/'.$driver->license_back_image) }}" alt="Licencia reverso" onclick="openModal(this.src)">
                @else
                    <div class="no-image">Sin imagen</div>
                @endif
            </div>
        </div>
    </div>

    {{-- CARNET DE IDENTIDAD --}}
    <div class="detail-card">
        <h3><i class="fa fa-address-card"></i> Carnet de Identidad</h3>
        <div class="doc-images">
            <div class="doc-item">
                <p><strong>CI (Frente)</strong></p>
                @if($driver->ci_front_image)
                    <img src="{{ Storage::url('driver-ci_front_image/normal/'.$driver->ci_front_image) }}" alt="CI frente" onclick="openModal(this.src)">
                @else
                    <div class="no-image">Sin imagen</div>
                @endif
            </div>
            <div class="doc-item">
                <p><strong>CI (Reverso)</strong></p>
                @if($driver->ci_back_image)
                    <img src="{{ Storage::url('driver-ci_back_image/normal/'.$driver->ci_back_image) }}" alt="CI reverso" onclick="openModal(this.src)">
                @else
                    <div class="no-image">Sin imagen</div>
                @endif
            </div>
        </div>
    </div>

    {{-- DATOS BANCARIOS --}}
    @if($driver->bank_account_number || $driver->name_titular)
    <div class="detail-card">
        <h3><i class="fa fa-university"></i> Datos Bancarios</h3>
        <div class="detail-row">
            <span class="detail-label">Titular:</span>
            <span class="detail-value">{{ $driver->name_titular ?: 'No registrado' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">CI Titular:</span>
            <span class="detail-value">{{ $driver->ci_number_titular ?: 'No registrado' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Nro. Cuenta Bancaria:</span>
            <span class="detail-value">{{ $driver->bank_account_number ?: 'No registrado' }}</span>
        </div>
        <div class="doc-images">
            @if($driver->ci_front_image_titular)
            <div class="doc-item">
                <p><strong>CI Titular (Frente)</strong></p>
                <img src="{{ Storage::url('driver-ci_front_image/normal/'.$driver->ci_front_image_titular) }}" alt="CI titular frente" onclick="openModal(this.src)">
            </div>
            @endif
            @if($driver->ci_back_image_titular)
            <div class="doc-item">
                <p><strong>CI Titular (Reverso)</strong></p>
                <img src="{{ Storage::url('driver-ci_back_image/normal/'.$driver->ci_back_image_titular) }}" alt="CI titular reverso" onclick="openModal(this.src)">
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- VEHÍCULOS --}}
    <div class="detail-card">
        <h3><i class="fa fa-car"></i> Vehículos Registrados ({{ count($vehicles) }})</h3>
        @if(count($vehicles) > 0)
            @foreach($vehicles as $vehicle)
            <div class="vehicle-card">
                <div class="detail-row">
                    <span class="detail-label">Placa:</span>
                    <span class="detail-value" style="font-weight: bold; font-size: 18px;">{{ $vehicle->number_plate }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Marca:</span>
                    <span class="detail-value">{{ $vehicle->vehicle_brand ? $vehicle->vehicle_brand->name : 'ID: '.$vehicle->vehicle_brand_id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Modelo:</span>
                    <span class="detail-value">{{ $vehicle->vehicle_model ? $vehicle->vehicle_model->name : 'No especificado' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tipo:</span>
                    <span class="detail-value">{{ ucfirst($vehicle->type ?: 'No especificado') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Color:</span>
                    <span class="detail-value">
                        <span style="display: inline-block; width: 20px; height: 20px; background: {{ $vehicle->color ?: '#ccc' }}; border: 1px solid #999; border-radius: 3px; vertical-align: middle;"></span>
                        {{ $vehicle->color ?: 'No especificado' }}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Año:</span>
                    <span class="detail-value">{{ $vehicle->model_year ?: 'No especificado' }}</span>
                </div>
                <div class="doc-images">
                    @if($vehicle->vehicle_image)
                    <div class="doc-item">
                        <p><strong>Foto del Vehículo</strong></p>
                        <img src="{{ Storage::url('vehicle-vehicle_image/normal/'.$vehicle->vehicle_image) }}" alt="Vehículo" onclick="openModal(this.src)">
                    </div>
                    @endif
                    @if($vehicle->side_image)
                    <div class="doc-item">
                        <p><strong>Foto Lateral</strong></p>
                        <img src="{{ Storage::url('vehicle-side_image/normal/'.$vehicle->side_image) }}" alt="Lateral" onclick="openModal(this.src)">
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        @else
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle"></i> Este conductor no tiene vehículos registrados.
            </div>
        @endif
    </div>

    {{-- SECCIÓN DE APROBACIÓN --}}
    <div class="approval-section">
        <h3 style="margin-bottom: 15px;">Acción de Aprobación</h3>
        
        @if(count($vehicles) == 0)
            <div class="alert alert-danger">
                <strong>No se puede aprobar:</strong> El conductor debe tener al menos un vehículo registrado.
            </div>
        @endif

        @if($driver->active != 1)
            <p style="color: #666; margin-bottom: 20px;">¿Ha revisado todos los datos y documentos del conductor? Si todo está correcto, puede aprobar el registro.</p>
            <a href="{{ url('customer-admin/approve-driver/'.$driver->id) }}" 
               class="btn btn-approve" 
               onclick="return confirm('¿Está seguro que desea APROBAR a este conductor? Se le enviará un mensaje por WhatsApp.')"
               @if(count($vehicles) == 0) style="pointer-events: none; opacity: 0.5;" @endif>
                <i class="fa fa-check"></i> Aprobar Conductor
            </a>
            <a href="{{ url('customer-admin/reject-driver/'.$driver->id) }}" 
               class="btn btn-reject"
               onclick="return confirm('¿Está seguro que desea RECHAZAR a este conductor?')">
                <i class="fa fa-times"></i> Rechazar / Bloquear
            </a>
        @else
            <p style="color: #155724; font-size: 18px;"><i class="fa fa-check-circle"></i> Este conductor ya está <strong>APROBADO</strong>.</p>
            
            {{-- Botón para enviar WhatsApp manualmente --}}
            @if($driver->cellphone)
            @php
                $driverName = trim($driver->first_name . ' ' . $driver->last_name);
                $freeUntil = $driver->free_trial_until ? date('d/m/Y', strtotime($driver->free_trial_until)) : date('d/m/Y', strtotime('+30 days'));
                $waMessage = "🎉 *¡Hola {$driverName}!*\n\n"
                    . "Te informamos que tu solicitud como conductor en *AnDre* ha sido *APROBADA* ✅\n\n"
                    . "📱 Ya puedes iniciar sesión en la app AnDre Driver con tu número *{$driver->cellphone}*\n\n"
                    . "🎁 Tienes *30 días GRATIS* para usar la app (hasta el {$freeUntil}). Después podrás recargar tu saldo.\n\n"
                    . "¡Bienvenido al equipo! 🚗\n"
                    . "_Equipo AnDre - La App Móvil del Pueblo_";
                $waUrl = "https://wa.me/591" . $driver->cellphone . "?text=" . urlencode($waMessage);
            @endphp
            <div class="whatsapp-section" style="margin-top: 15px;">
                <p><i class="fa fa-whatsapp" style="color: #25D366; font-size: 20px;"></i> <strong>Enviar mensaje de bienvenida por WhatsApp</strong></p>
                <a href="{{ $waUrl }}" target="_blank" class="btn btn-whatsapp">
                    <i class="fa fa-whatsapp"></i> Enviar WhatsApp a {{ $driver->first_name }}
                </a>
            </div>
            @endif

            <div style="margin-top: 15px;">
                <a href="{{ url('customer-admin/reject-driver/'.$driver->id) }}" 
                   class="btn btn-reject"
                   onclick="return confirm('¿Está seguro que desea BLOQUEAR a este conductor? No podrá iniciar sesión.')">
                    <i class="fa fa-ban"></i> Bloquear Conductor
                </a>
            </div>
        @endif
    </div>

</div>

{{-- Modal para ver imágenes --}}
<div class="img-modal" id="imgModal" onclick="closeModal()">
    <img id="modalImg" src="" alt="Imagen ampliada">
</div>

<script>
function openModal(src) {
    document.getElementById('modalImg').src = src;
    document.getElementById('imgModal').classList.add('active');
}
function closeModal() {
    document.getElementById('imgModal').classList.remove('active');
}
</script>
@endsection
