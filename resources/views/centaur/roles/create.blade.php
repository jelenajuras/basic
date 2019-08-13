@extends('Centaur::layout')

@section('title', __('basic.create_role'))

@section('content')
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">@lang('basic.create_role')</h3>
            </div>
            <div class="panel-body">
                <form accept-charset="UTF-8" role="form" method="post" action="{{ route('roles.store') }}">
                <fieldset>
                    <div class="form-group {{ ($errors->has('name')) ? 'has-error' : '' }}">
                    <input class="form-control" placeholder="{{ __('basic.name')}}" name="name" type="text" value="{{ old('name') }}" required />
                        {!! ($errors->has('name') ? $errors->first('name', '<p class="text-danger">:message</p>') : '') !!}
                    </div>
                    <div class="form-group {{ ($errors->has('slug')) ? 'has-error' : '' }}">
                    <input class="form-control" placeholder="{{ __('absence.mark') }}" name="slug" type="text" value="{{ old('slug') }}" required />
                        {!! ($errors->has('slug') ? $errors->first('slug', '<p class="text-danger">:message</p>') : '') !!}
                    </div>
                    <h5>@lang('basic.permissions'):</h5>
					@foreach($tables as $table)
						@foreach($methodes as $methode)
							<div class="checkbox">
								<label>
									<input type="checkbox" name="permissions[{{$table}}.{{$methode}}]" value="1">
									{{$table}}.{{$methode}}
								</label>
							</div>
						@endforeach
					@endforeach
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    <input class="btn btn-lg btn-primary btn-block" type="submit" value="{{ __('basic.save')}}">
                </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
@stop