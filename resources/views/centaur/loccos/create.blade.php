@php	
	if(isset($car_id) && $car_id != null) {
		$car_employee = $cars->where('id', $car_id)->first();
	} else {
		$car_id = null;
		if( $registracija != null ) {
			$car_employee = $cars->where('registration', $registracija)->first();
		} else {
			$car_employee = $cars->where('employee_id', Sentinel::getUser()->employee->id )->first();
		}
		if ($car_employee) {
			$car_id = $car_employee->id;
		}
	}
@endphp
<div class="modal-header">
	<h3 class="panel-title">@lang('basic.add_locco')</h3>
</div>
<div class="modal-body">
	<form accept-charset="UTF-8" role="form" method="post" action="{{ route('loccos.store') }}" >
		<fieldset>
			<div class="form-group {{ ($errors->has('car_id')) ? 'has-error' : '' }}">
				<label>@lang('basic.car')</label>
				<select class="form-control" name="car_id" id="car_id" value="{{ old('car_id') }}" required >
					<option selected disabled ></option>
					@if(count($cars)>0)
					@foreach ($cars as $car)
						<option name="car_id" value="{{ $car->id }}" {!! isset($car_id) && $car_id == $car->id  ? 'selected' : '' !!} >{{ $car->registration }}</option>
					@endforeach
					@endif
				</select>
				{!! ($errors->has('vozilo_id') ? $errors->first('vozilo_id', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('employee_id')) ? 'has-error' : '' }}">
				<label>@lang('basic.employee')</label>
				<select  class="form-control" name="employee_id" value="{{ old('employee_id') }}" required>
					<label>@lang('basic.employee')</label>
					<option value="" selected disabled></option>
					@foreach ($employees as $employee)
						<option value="{{ $employee->id }}" {!! Sentinel::getUser()->employee->id == $employee->id ? 'selected' : '' !!} >{{ $employee->user['first_name'] . ' ' .  $employee->user['last_name'] }}</option>
					@endforeach
				</select>
				{!! ($errors->has('department_id') ? $errors->first('department_id', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('date')) ? 'has-error' : '' }}">
				<label for="">@lang('basic.date')</label>
				<input class="form-control" name="date" type="datetime-local" value="{!! old('date') ? old('date') : Carbon\Carbon::now()->format('Y-m-d\TH:i') !!}" required />
				{!! ($errors->has('date') ? $errors->first('date', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('end_date')) ? 'has-error' : '' }}">
				<label for="">@lang('absence.end_date')</label>
				<input class="form-control" name="end_date" type="datetime-local" value="{{ old('end_date') }}" />
				{!! ($errors->has('date') ? $errors->first('date', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('starting_point')) ? 'has-error' : '' }}">
				<label>@lang('basic.starting_point')</label>
				<input class="form-control" placeholder="{{ __('basic.starting_point') }}" name="starting_point" type="text" value="{{ old('starting_point') }}" required />
				{!! ($errors->has('starting_point') ? $errors->first('starting_point', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('destination')) ? 'has-error' : '' }}">
				<label>@lang('basic.destination')</label>
				<input class="form-control" placeholder="{{ __('basic.destination') }}" name="destination" type="text" value="{{ old('destination') }}"  />
				{!! ($errors->has('destination') ? $errors->first('destination', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('start_km'))  ? 'has-error' : '' }}">
				<label>@lang('basic.start_km')</label>
				<input class="form-control" name="start_km" type="{!! isset($car_employee) && $car_employee->private_car == 1 ? 'text' : 'hidden' !!}" id="start_km" required value="{!! isset($car_employee) && $car_employee ? $car_employee->current_km : '' !!}" />	
		
				<p id="start_km_text" style="display:{!!  isset($car_employee) && $car_employee->private_car != 1 ? 'block' : 'none' !!}">{!! isset($car_employee) && $car_employee ? $car_employee->current_km : '' !!}</p>
			
				{!! ($errors->has('start_km') ? $errors->first('start_km', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('end_km'))  ? 'has-error' : '' }}">
				<label>@lang('basic.end_km')</label>
				<input class="form-control" name="end_km" id="end_km" type="number" value="{{ old('end_km') }}"/>
				{!! ($errors->has('end_km') ? $errors->first('end_km', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('distance'))  ? 'has-error' : '' }}">
				<label>@lang('basic.distance')</label>
				<input class="form-control" name="distance" id="distance" type="number"  value="{{ old('distance') }}" readonly />	
				{!! ($errors->has('distance') ? $errors->first('distance', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group">
				<label>@lang('basic.comment')</label>
				<textarea class="form-control" name="comment" id="comment">{{ old('comment') }}</textarea>
			</div>
			<div class="servis form-group">
				<label for="wrong_km">@lang('basic.wrong_km')</label>
				<input class="" type="checkbox" name="wrong_km"  id="wrong_km" value=""/>
			</div>
			<div class="servis form-group">
				<label for="travel">@lang('basic.create_travel')</label>
				<input class="" type="checkbox" name="travel" value="travel" id="travel" value=""/>
			</div>
			{{ csrf_field() }}
			<input class="btn-submit" type="submit" id="submit" value="{{ __('basic.save')}}">
		</fieldset>
	</form>
</div>
<span hidden class="locale" >{{ App::getLocale() }}</span>
<script>
	$(function() {
		var current_km;

		$('#end_km').change(function() {
			var poc_km = $('#start_km').val();
			var zav_km = $('#end_km').val();
			var udaljenost = zav_km - poc_km;
			$('#distance').val(udaljenost);
			if (udaljenost < 0 ) {
				$('#distance').css('border','1px solid red');
				$('.btn-submit').attr('disabled', 'disabled');
			} else {
				$('#distance').css('border','1px solid #F0F4FF');
				$('.btn-submit').attr('disabled', 'false');
			}
		});

		$('#car_id').change(function(){
			var car_id = $( this ).val();
			try {
				var token = $('meta[name="csrf-token"]').attr('content');
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					url:  "last_km", 
					type: 'post',
					data: {
							'_token':  token,
							'car_id': car_id,                   
						}
				})
				.done(function( car ) {     
					current_km = car.current_km;
					$('#start_km').val(current_km);
					$('#start_km_text').text(current_km);
					if(car.private_car == 1) {
						$("#start_km_text").hide();						
						$("#start_km").attr('type','number');

					} else {
						$("#start_km_text").show();						
						$("#start_km").attr('type','hidden');
					}
				})
				.fail(function() {
					alert( "Nije uspjelo" );
				})
			} catch (error) {
				
			}
		});

		$('#wrong_km').change(function(){
			if ( $( this ).prop( "checked" ) ) {
				$("#start_km_text").hide();
				$("#start_km").attr('type','number');
				$( '#comment').attr('required', 'true');
			} else {
				$("#start_km_text").show();
				$("#start_km").val( current_km );
				$("#start_km").attr('type','hidden');
				$( '#comment').attr('required', 'false');
			}
		});
	});

	$.getScript( '/../js/validate.js');
	
</script>