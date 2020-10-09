<div class="modal-header">
	<h3 class="panel-title">@lang('basic.add_customer')</h3>
</div>
<div class="modal-body">
	<form accept-charset="UTF-8" role="form" method="post" action="{{ route('customers.update', $customer->id) }}" enctype="multipart/form-data">
		<fieldset>
			<div class="form-group {{ ($errors->has('name')) ? 'has-error' : '' }}">
				<input class="form-control" placeholder="{{ __('basic.name')}}" name="name" type="text" maxlength="100" value="{{ $customer->name }}" required />
				{!! ($errors->has('name') ? $errors->first('name', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('address')) ? 'has-error' : '' }}">
				<input class="form-control" placeholder="{{ __('basic.address')}}" name="address" type="text" maxlength="100" value="{{ $customer->address }}" required />
				{!! ($errors->has('address') ? $errors->first('address', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('city')) ? 'has-error' : '' }}">
				<input class="form-control" placeholder="{{ __('basic.city')}}" name="city" type="text" maxlength="50" value="{{ $customer->city }}" required />
				{!! ($errors->has('city') ? $errors->first('city', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('oib')) ? 'has-error' : '' }}">
				<input class="form-control" placeholder="{{ __('basic.oib')}}" name="oib" maxlength="20" type="text" value="{{ $customer->oib }}" required />
				{!! ($errors->has('oib') ? $errors->first('oib', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="">
				<label for="active_1">@lang('basic.active')</label>
				<input name="active" type="radio" id="active_1" value="1" {!! $customer->active == 1 ? 'checked' : '' !!} />
				<label for="active_0">@lang('basic.inactive')</label>
				<input  name="active" type="radio" id="active_0" value="0"  {!! $customer->active == 0 ? 'checked' : '' !!} />
			</div>
			{{ csrf_field() }}
			{{ method_field('PUT') }}
			<input class="btn-submit" type="submit" value="{{ __('basic.save')}}">
		</fieldset>
	</form>
</div>
<span hidden class="locale" >{{ App::getLocale() }}</span>
<script>
//$.getScript( '/../js/validate.js');
</script>