@extends('users-mgmt.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of users</h3>
        </div>
        <div class="col-sm-4">
          @if (Auth::user()->roles()->pluck('role_name')->first() == 'Super Admin')
          <a class="btn btn-primary" href="{{ url('admin/createuser') }}">Add new user</a>
          <a class="btn btn-primary" href="{{ route('user-admin.create') }}">Add new admin</a>
          @else
          <a class="btn btn-primary" href="{{ url('admin/createuser') }}">Add new user</a>
          @endif
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('user-search') }}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Search'])
          @component('layouts.two-cols-search-row', ['items' => ['Email'],
          'oldVals' => [isset($searchingVals) ? $searchingVals['email'] : '']])
          @endcomponent
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12 box-body table-responsive">
          <table id="example2" class="table table-hover" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="3">Action</th>
                <th width="5%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Id: activate to sort column descending">Id</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Role Name: activate to sort column ascending">Role</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Email</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending">Name</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Car Type: activate to sort column ascending">Car Type</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Car License: activate to sort column ascending">Car License</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr role="row" class="odd">
                @foreach($user->roles as $role)
                  @if (Auth::user()->roles()->pluck('role_name')->first() == 'Super Admin')
                  <td>
                        <a href="{{ url('user-admin/editpassword', ['id_user' => $user->id_user]) }}" class="btn btn-success col-sm-10 col-xs-10 btn-margin">
                        Update Password
                        </a>
                  </td>
                  <td>
                        <a href="{{ route('user-admin.edit', ['id_user' => $user->id_user]) }}" class="btn btn-warning col-sm-10 col-xs-10 btn-margin">
                        Update Data
                        </a>
                  </td>
                  <td width="15%">
                    <form class="row" method="POST" action="{{ route('user-admin.destroy', ['id_user' => $user->id_user]) }}" onsubmit = "return confirm('Are you sure?')">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        @if ($user->email != Auth::user()->email)
                         <button type="submit" class="btn btn-danger col-sm-9 col-xs-8 btn-margin">
                          Delete
                        </button>
                        @endif
                    </form>
                  </td>
                  <td >{{ $user->id_user }}</td>
                  <td style="color:red;">{{ $role->role_name}}</td>
                  <td>{{ $user->email }}</td>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->car_type }}</td>
                  <td >{{ $user->license_plate_number }}</td>

                  @elseif (Auth::user()->roles()->pluck('role_name')->first() == 'Admin')
                    @if ($role->role_name == 'User')
                    <td>
                        <a href="{{ url('user-admin/editpassword', ['id_user' => $user->id_user]) }}" class="btn btn-success col-sm-10 col-xs-10 btn-margin">
                        Update Password
                        </a>
                  </td>
                    <td>
                          <a href="{{ route('user-admin.edit', ['id_user' => $user->id_user]) }}" class="btn btn-warning col-sm-10 col-xs-10 btn-margin">
                          Update Data
                          </a>
                      </td>
                      <td width="15%">
                      <form class="row" method="POST" action="{{ route('user-admin.destroy', ['id_user' => $user->id_user]) }}" onsubmit = "return confirm('Are you sure?')">
                          <input type="hidden" name="_method" value="DELETE">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          @if ($user->email != Auth::user()->email)
                            <button type="submit" class="btn btn-danger col-sm-9 col-xs-8 btn-margin">
                            Delete
                            </button>
                          @endif
                      </form>
                      </td>
                      <td >{{ $user->id_user }}</td>
                      <td style="color:red;">{{ $role->role_name}}</td>
                      <td>{{ $user->email }}</td>
                      <td>{{ $user->name }}</td>
                      <td>{{ $user->car_type }}</td>
                      <td >{{ $user->license_plate_number }}</td>
                    @endif
                  @endif
                @endforeach
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($users)}} of {{count($users)}} entries</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $users->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
@endsection