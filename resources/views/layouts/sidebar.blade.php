  <!-- =============================================== -->

  <!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ asset("/bower_components/AdminLTE/dist/img/ava-02.jpg") }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ Auth::user()->name}}</p>
          <p>{{ Auth::user()->email}}</p>
        </div>
      </div>      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      @if (Auth::user()->roles()->pluck('role_name')->first() != 'User')
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <li class="{{ Route::currentRouteName() == 'allreservations' ? 'active' : '' }}"><a href="{{ route('allreservations') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li class="{{ Route::currentRouteName() == 'reservation-admin.index' ? 'active' : '' }}"><a href="{{ route('reservation-admin.index') }}"><i class="fa fa-car"></i> <span>Reservation Management</span></a></li>
        <li class="{{ Route::currentRouteName() == 'slot-admin.index' ? 'active' : '' }}"><a href="{{ route('slot-admin.index') }}"><i class="fa fa-map-pin"></i> <span>Slot Management</span></a></li>
        <li class="{{ Route::currentRouteName() == 'sensor-admin.index' ? 'active' : '' }}"><a href="{{ route('sensor-admin.index') }}"><i class="fa fa-bullseye"></i> <span>Sensor Management</span></a></li>
        <li class="treeview {{ Route::currentRouteName() == 'user-admin.index' || Route::currentRouteName() == 'balance-admin.index'  ? 'active' : '' }}">
          <a href="#"><i class="fa fa-users"></i> <span>User Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{ Route::currentRouteName() == 'user-admin.index' ? 'active' : '' }}"><a href="{{ route('user-admin.index') }}">User Data</a></li>
            <li class="{{ Route::currentRouteName() == 'balance-admin.index'  ? 'active' : '' }}"><a href="{{ route('balance-admin.index') }}">User Balance</a></li>
          </ul>
        </li>
        <!-- <li>
          <a href="{{ url('/calendar') }}">
            <i class="fa fa-calendar"></i> <span>Calendar</span>
          </a>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-folder"></i> <span>Examples</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="../examples/invoice.html"><i class="fa fa-circle-o"></i> Invoice</a></li>
            <li><a href="../examples/profile.html"><i class="fa fa-circle-o"></i> Profile</a></li>
            <li><a href="../examples/login.html"><i class="fa fa-circle-o"></i> Login</a></li>
            <li><a href="../examples/register.html"><i class="fa fa-circle-o"></i> Register</a></li>
            <li><a href="../examples/lockscreen.html"><i class="fa fa-circle-o"></i> Lockscreen</a></li>
            <li><a href="../examples/404.html"><i class="fa fa-circle-o"></i> 404 Error</a></li>
            <li><a href="../examples/500.html"><i class="fa fa-circle-o"></i> 500 Error</a></li>
            <li><a href="../examples/blank.html"><i class="fa fa-circle-o"></i> Blank Page</a></li>
            <li><a href="../examples/pace.html"><i class="fa fa-circle-o"></i> Pace Page</a></li>
          </ul>
        </li> -->
      </ul>
      @endif
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- =============================================== -->