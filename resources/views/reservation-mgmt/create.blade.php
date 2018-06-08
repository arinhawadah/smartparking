@extends('reservation-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new user</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('reservation-admin.store') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Email User</label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                    <div class="form-group">
                            <label class="col-md-4 control-label">Slot Name</label>
                            <div class="col-md-6">
                                <select class="form-control" name="id_slot">
                                    @foreach ($slot as $slots)
                                        <option value="{{$slots->id_slot}}" >{{$slots->slot_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('arrive_time') ? ' has-error' : '' }}">
                            <label for="arrive_time" class="col-md-4 control-label">Arrival Time</label>

                            <div class="col-md-6">
                                <input id="arrive_time" type="text" class="form-control" name="arrive_time" value="{{ old('arrive_time') }}" required>

                                @if ($errors->has('arrive_time'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('arrive_time') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('leaving_time') ? ' has-error' : '' }}">
                            <label for="leaving_time" class="col-md-4 control-label">Leaving Time</label>

                            <div class="col-md-6">
                                <input id="leaving_time" type="text" class="form-control" name="leaving_time" value="{{ old('leaving_time') }}" required>

                                @if ($errors->has('leaving_time'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('leaving_time') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                            <label for="price" class="col-md-4 control-label">Price</label>

                            <div class="col-md-6">
                                <input id="price" type="price" class="form-control" name="price" value="{{ old('price') }}">

                                @if ($errors->has('price'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('price') }}</strong>
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
