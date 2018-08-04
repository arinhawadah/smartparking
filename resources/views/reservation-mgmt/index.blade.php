@extends('reservation-mgmt.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of reservations</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" 
          href="{{ route('reservation-admin.create') }}">Add new reservation</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('reservation-search') }}">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Search'])
          @component('layouts.two-cols-search-row', ['items' => ['Name'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['name'] : '']])
          @endcomponent
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12 box-body table-responsive">
          <table class="table table-hover">
            <thead>
              <tr role="row">
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
                <th width="5%" class="hidden-xs" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-sort="ascending">Id</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Name</th>
                <th width="5%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Slot Name</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Arrival Time</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Leaving Time</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Price</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr role="row" class="odd">
                <td>
                    <a href="{{ route('reservation-admin.edit', ['id_user_park' => $user->id_user_park]) }}" class="btn btn-warning col-sm-8 col-xs-8 btn-margin">
                        Update
                    </a>
                  </td>
                  <td>
                    <form class="row" method="POST" action="{{ route('reservation-admin.destroy', ['id_user_park' => $user->id_user_park]) }}" onsubmit = "return confirm('Are you sure?')">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                         <button type="submit" class="btn btn-danger col-sm-8 col-xs-8 btn-margin">
                          Delete
                        </button>
                    </form>
                  </td>
                  <td class="hidden-xs">{{ $user->id_user_park }}</td>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->slot_name }}</td>
                  <td>{{ $user->arrive_time }}</td>
                  <td>{{ $user->leaving_time }}</td>
                  <td>Rp {{ $user->price }}</td>
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