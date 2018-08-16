@extends('sensor-mgmt.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of sensors</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('sensor-admin.create') }}">Add new sensor</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12 box-body table-responsive">
          <table id="example2" class="table table-hover" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
                <th width="5%"tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Entry</th>
                <th width="10%"tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Id Sensor</th>
                <th width="20%"tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Time</th>
                <th width="20%"tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Status</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($slot as $slots)
                <tr role="row" class="odd">
                <td>
                        <a href="{{ route('sensor-admin.edit', ['entry' => $slots->entry]) }}" class="btn btn-warning col-sm-8 col-xs-8 btn-margin">
                        Update
                        </a>
                  </td>
                  <td>
                    <form class="row" method="POST" action="{{ route('sensor-admin.destroy', ['id_sensor' => $slots->id_sensor]) }}" onsubmit = "return confirm('Are you sure?')">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        @if ($slots->email != Auth::user()->email)
                         <button type="submit" class="btn btn-danger col-sm-8 col-xs-8 btn-margin">
                          Delete
                        </button>
                        @endif
                    </form>
                  </td>
                  <td>{{ $slots->entry }}</td>
                  <td>{{ $slots->id_sensor }}</td>
                  <td>{{ $slots->time }}</td>
                  <td>{{ $slots->status }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($slot)}} of {{count($slot)}} entries</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $slot->links() }}
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