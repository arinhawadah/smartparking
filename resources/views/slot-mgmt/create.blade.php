@extends('slot-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new slot</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('slot-admin.store') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group{{ $errors->has('slot_name') ? ' has-error' : '' }}">
                            <label for="slot_name" class="col-md-4 control-label">Slot Name</label>

                            <div class="col-md-6">
                                <input id="slot_name" type="text" class="form-control" name="slot_name" value="{{ old('slot_name') }}" required>

                                @if ($errors->has('slot_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('slot_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    <div class="form-group">
                            <label class="col-md-4 control-label">Id Sensor</label>
                            <div class="col-md-6">
                                <select class="form-control" name="id_sensor">
                                    @foreach ($sensor as $sensors)
                                        <option value="{{$sensors->id_sensor}}" >{{$sensors->id_sensor}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label for="status" class="col-md-4 control-label">Status</label>

                            <div class="col-md-6">
                                <input id="status" type="text" class="form-control" name="status" value="{{ old('status') }}" required>

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
                                    Create
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
