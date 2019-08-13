@extends('Centaur::layout')

@section('title', __('absence.add_abs_type'))

@section('content')
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">@lang('absence.add_abs_type')</h3>
            </div>
            <div class="panel-body">
                <form accept-charset="UTF-8" role="form" method="post" action="{{ route('absence_types.store') }}">
					<div class="form-group {{ ($errors->has('name'))  ? 'has-error' : '' }}">
						<label>@lang('basic.name')</label>
						<input name="name" type="text" class="form-control" value="{{ old('name') }}">
						{!! ($errors->has('name') ? $errors->first('name', '<p class="text-danger">:message</p>') : '') !!}
					</div>
					<div class="form-group {{ ($errors->has('mark'))  ? 'has-error' : '' }}">
						<label>@lang('absence.mark')</label>
						<input name="mark" type="text" class="form-control" maxlength="5" value="{{ old('mark') }}">
						{!! ($errors->has('mark') ? $errors->first('mark', '<p class="text-danger">:message</p>') : '') !!}
					</div>
					<div class="form-group {{ ($errors->has('min_days'))  ? 'has-error' : '' }}">
						<label>@lang('absence.min_days')</label>
						<input name="min_days" type="text" class="form-control" maxlength="2" value="{{ old('min_days') }}">
						{!! ($errors->has('min_days') ? $errors->first('min_days', '<p class="text-danger">:message</p>') : '') !!}
					</div>
					<div class="form-group {{ ($errors->has('max_days'))  ? 'has-error' : '' }}">
						<label>@lang('absence.max_days')</label>
						<input name="max_days" type="text" class="form-control" maxlength="2" value="{{ old('max_days') }}">
						{!! ($errors->has('max_days') ? $errors->first('max_days', '<p class="text-danger">:message</p>') : '') !!}
					</div>
					{{ csrf_field() }}
					<input class="btn-submit" type="submit" value="{{ __('basic.save')}}" id="stil1">
				</form>
            </div>
        </div>
    </div>
</div>
@stop