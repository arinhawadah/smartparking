@extends('users-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update {{$user->name}}</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('user-admin.update', ['id_user' => $user->id_user]) }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="PATCH">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="name" class="form-control" name="name" value="{{ $user->name }}">

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('car_type') ? ' has-error' : '' }}">
                            <label for="car_type" class="col-md-4 control-label">Car Type</label>

                            <div class="col-md-6">
                                <input id="car_type" type="car_type" class="form-control" name="car_type" value="{{ $user->car_type }}">

                                @if ($errors->has('car_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('car_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('license_plate_number') ? ' has-error' : '' }}">
                            <label for="license_plate_number" class="col-md-4 control-label">Plate Number</label>

                            <div class="col-md-6">
                                <input id="license_plate_number" type="license_plate_number" class="form-control" name="license_plate_number" value="{{ $user->license_plate_number }}">

                                @if ($errors->has('license_plate_number'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('license_plate_number') }}</strong>
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
