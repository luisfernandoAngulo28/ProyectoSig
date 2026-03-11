<li class="navigation-header"><span>PARAMETRIZAR</span></li>

<li class="nav-item @if (url()->current() == url('admin/model-list/organization')) active @endif " title="Ir a administrador">
    <a href="{{ url('admin/model-list/organization') }}">
        <i class="fa fa-th"></i>
        <span class="menu-title">
            Ir a administrador
        </span>
    </a>
</li>

<li class="nav-item @if (url()->current() == url('customer-admin/model-list/city')) active @endif " title="Ciudades">
    <a href="{{ url('customer-admin/model-list/city') }}">
        <i class="fa fa-building"></i>
        <span class="menu-title">
            Ciudades
        </span>
    </a>
</li>

<li class="nav-item @if (url()->current() == url('customer-admin/model-list/organization')) active @endif " title="Empresas">
    <a href="{{ url('customer-admin/model-list/organization') }}">
        <i class="fa fa-building"></i>
        <span class="menu-title">
            Empresas
        </span>
    </a>
</li>

<li class="nav-item @if (url()->current() == url('customer-admin/model-list/vehicle-brand')) active @endif " title="Marca de Vehículos">
    <a href="{{ url('customer-admin/model-list/vehicle-brand') }}">
        <i class="fa fa-th"></i>
        <span class="menu-title">
            Marcas de Vehículos
        </span>
    </a>
</li>

<li class="nav-item @if (url()->current() == url('customer-admin/model-list/vehicle-model')) active @endif " title="Modelos de Vehículos">
    <a href="{{ url('customer-admin/model-list/vehicle-model') }}">
        <i class="fa fa-th"></i>
        <span class="menu-title">
            Modelos de Vehículos
        </span>
    </a>
</li>



<li class="navigation-header"><span>USUARIOS</span></li>

<li class="nav-item @if (url()->current() == url('customer-admin/model-list/driver')) active @endif " title="Conductores">
    <a href="{{ url('customer-admin/model-list/driver') }}">
        <i class="fa fa-user"></i>
        <span class="menu-title">
            Conductores
        </span>
    </a>
</li>
<li class="nav-item @if (url()->current() == url('customer-admin/model-list/driver-vehicle')) active @endif " title="Vehículos">
    <a href="{{ url('customer-admin/model-list/driver-vehicle') }}">
        <i class="fa fa-car"></i>
        <span class="menu-title">
            Vehículos
        </span>
    </a>
</li>


<li class="nav-item @if (url()->current() == url('/customer-admin/model-list/user?passenger=true&f_role_user%5B%5D=3&button=&search=1'))  @endif " title="Pasajeros" id="nav-pasajeros">
    <a href="{{ url('/customer-admin/model-list/user?passenger=true&f_role_user%5B%5D=3&button=&search=1') }}">
        <i class="fa fa-user"></i>
        <span class="menu-title">
            Pasajeros
        </span>
    </a>
</li>

<li class="nav-item @if (url()->current() == url('customer-admin/model-list/user')) active @endif " title="Usuarios" id="nav-users">
    <a href="{{ url('customer-admin/model-list/user') }}">
        <i class="fa fa-users"></i>
        <span class="menu-title">
            Usuarios
        </span>
    </a>
</li>

<li class="navigation-header"><span>OTROS</span></li>

<li class="nav-item @if (url()->current() == url('customer-admin/model-list/request')) active @endif " title="Solicitudes">
    <a href="{{ url('customer-admin/model-list/request') }}">
        <i class="fa fa-th"></i>
        <span class="menu-title">
            Solicitudes
        </span>
    </a>
</li>


{{-- <li class="nav-item @if (url()->current() == url('customer-admin/model-list/sindicato')) active @endif " title="Sindicatos">
    <a href="{{ url('customer-admin/model-list/sindicato') }}">
        <i class="fa fa-building"></i>
        <span class="menu-title">
            Sindicatos
        </span>
    </a>
</li> --}}
<li class="nav-item @if (url()->current() == url('customer-admin/model-list/firebase-notification')) active @endif " title="Usuarios">
    <a href="{{ url('customer-admin/model-list/firebase-notification') }}">
        <i class="fa fa-bell-o"></i>
        <span class="menu-title">
            Notificaciones
        </span>
    </a>
</li>



<style>
    .submenu {
        display: none;
        background-color: #fff0 !important;
        border: none;
    }

    .submenu.show {
        display: block;
    }

    .submenu>li:hover {
        box-shadow: none !important;
        color: transparent !important;
    }
</style>




<li class="nav-item" title="Reportes">
    <a href="#" id="toggleLink">
        <i class="fa fa-file
        "></i>
        <span class="menu-title">
            Reportes
        </span>
    </a>

    <ul class="submenu" id="submenu">
        {{-- <a style="background-color:transparent !important;" href="{{ url('customer-admin/model-list/panic-button') }}">
            Reporte botón de pánico
        </a> --}}
        <li class="nav-item" title="Reportes">
            <a class="sub-item" style="background-color:transparent !important;"
                href="{{ url('customer-admin/model-list/driver-rating') }}">
                Reporte de Calificaciones
            </a>
        </li>
        {{-- <li class="nav-item" title="Reportes">
            <a class="sub-item" style="background-color:transparent !important;"
                href="{{ url('customer-admin/model-list/driver-activation') }}">
                Reporte de activaciones
            </a>
        </li> --}}
        <li class="nav-item" title="Reportes">
            <a class="sub-item" style="background-color:transparent !important;"
                href="{{ url('customer-admin/reportes-totales') }}">
                Reporte de totales conductores
            </a>
        </li>
        <li class="nav-item" title="Reportes">
            <a class="sub-item" style="background-color:transparent !important;"
                href="{{ url('customer-admin/reportes-totales-request') }}">
                Reporte de Solicitudes
            </a>
        </li>
        <li class="nav-item" title="Reportes">
            <a class="sub-item" style="background-color:transparent !important;"
                href="{{ url('customer-admin/reportes-amount-user') }}">
                Reporte de Monto Gastado por Usuario
            </a>
        </li>
        <br><br>
    </ul>
</li>
<br>
<br>
<br>
<script>
    document.getElementById('toggleLink').addEventListener('click', function(event) {
        event.preventDefault();
        var submenu = document.getElementById('submenu');
        submenu.classList.toggle('show');
    });
</script>
