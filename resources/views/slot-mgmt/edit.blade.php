@extends('slot-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update Slot {{$slot->id_slot}}</div>
                <div class="panel-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ route('slot-admin.update', ['id_slot' => $slot->id_slot]) }}">
                <input type="hidden" name="_method" value="PATCH">
                    {{ csrf_field() }}
                    <div class="form-group">
                            <label class="col-md-4 control-label">Id Sensor</label>
                            <div class="col-md-6">
                                <select class="form-control" name="id_sensor">
                                    @foreach ($sensor as $sensors)
                                        <option value="{{$sensors->id_sensor}}" {{$slot->id_sensor ==  $sensors->id_sensor ? 'selected' : ''}}>{{$sensors->id_sensor}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label for="status" class="col-md-4 control-label">Status</label>

                            <div class="col-md-6">
                                <input id="status" type="text" class="form-control" name="status" value="{{ $slot->status }}" required>

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
