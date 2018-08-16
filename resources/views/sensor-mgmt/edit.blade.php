@extends('sensor-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update Sensor {{$sensor->id_sensor}}</div>
                <div class="panel-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ route('sensor-admin.update', ['entry' => $sensor->entry]) }}">
                <input type="hidden" name="_method" value="PATCH">
                    {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label for="status" class="col-md-4 control-label">Status</label>

                            <div class="col-md-6">
                                <!-- <input id="status" type="text" class="form-control" name="status" value="{{ $sensor->status }}" required> -->
                                <input type="radio" name="status" value="0"> 0 <br>
                                <input type="radio" name="status" value="1"> 1 <br>
                                <input type="radio" name="status" value="2"> 2
                                @if ($errors->has('status'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('status') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
