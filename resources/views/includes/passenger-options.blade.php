<li class="navigation-header"><span>Mis viajes</span></li>
<li class="nav-item @if(url()->current()==url('customer-admin/model-list/request')) active @endif " title="Solicitudes de viajes">
    <a href="{{url('customer-admin/model-list/request')}}">
      <i class="fa fa-th"></i>
      <span class="menu-title">
        Solicitudes de viaje
      </span>
    </a>
</li>
