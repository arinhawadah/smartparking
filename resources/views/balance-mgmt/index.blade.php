@extends('balance-mgmt.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of balance</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('balance-admin.create') }}">Add new balances</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('balance-search') }}">
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
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2">Action</th>
                <th width="5%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Id: activate to sort column descending">Id</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Email</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Balance: activate to sort column ascending">Balance</th>
                <th width="15%" class="hidden-xs" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Time: activate to sort column ascending">Time</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($balance as $balances)
                <tr role="row" class="odd">
                  <td>
                        <a href="{{ route('balance-admin.edit', ['id_balance' => $balances->id_balance]) }}" class="btn btn-warning col-sm-8 col-xs-8 btn-margin">
                        Update
                        </a>
                  </td>
                  <td>
                    <form class="row" method="POST" action="{{ route('balance-admin.destroy', ['id_balance' => $balances->id_balance]) }}" onsubmit = "return confirm('Are you sure?')">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        @if ($balances->email != Auth::user()->email)
                         <button type="submit" class="btn btn-danger col-sm-8 col-xs-8 btn-margin">
                          Delete
                        </button>
                        @endif
                    </form>
                  </td>
                  <td>{{ $balances->id_balance }}</td>
                  <td>{{ $balances->email }}</td>
                  <td>{{ $balances->balance }}</td>
                  <td class="hidden-xs">{{ $balances->updated_at }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($balance)}} of {{count($balance)}} entries</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $balance->links() }}
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