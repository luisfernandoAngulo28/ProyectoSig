<li class="navigation-header"><span>PARAMETRIZAR</span></li>
<li class="nav-item @if(url()->current()==url('customer-admin/model-list/driver-vehicle')) active @endif " title="Vehículos">
    <a href="{{url('customer-admin/model-list/driver-vehicle')}}">
      <i class="fa fa-th"></i>
      <span class="menu-title">
        Vehículos
      </span>
    </a>
</li>
<li class="nav-item @if(url()->current()==url('customer-admin/model-list/request')) active @endif " title="Solicitudes">
    <a href="{{url('customer-admin/model-list/request')}}">
      <i class="fa fa-th"></i>
      <span class="menu-title">
        Solicitudes
      </span>
    </a>
</li>