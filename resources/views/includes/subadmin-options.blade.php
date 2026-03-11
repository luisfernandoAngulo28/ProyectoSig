<li class="navigation-header"><span>PARAMETRIZAR</span></li>



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
