<div class="modal-header">
	<h3 class="panel-title">@lang('absence.edit_absence')</h3>
</div>
<div class="modal-body">
	<form role="form" method="post" name="myForm" accept-charset="UTF-8" action="{{ route('absences.update', $absence->id ) }}" onsubmit="return validateForm()">
		@if (Sentinel::inRole('administrator'))
			<div class="form-group {{ ($errors->has('employee_id')) ? 'has-error' : '' }}">
				<label>@lang('basic.employee')</label>
				<select class="form-control" name="employee_id" value="{{ old('employee_id') }}" id="sel1" size="10" autofocus required >
					<option value="" disabled></option>
					@foreach ($employees as $employee)
						<option name="employee_id" value="{{ $employee->id }}" {!! $absence->employee_id ==  $employee->id ? 'selected' : '' !!}>{{ $employee->user['last_name']  . ' ' . $employee->user['first_name'] }}</option>
						
					@endforeach	
				</select>
				{!! ($errors->has('employee_id') ? $errors->first('employee_id', '<p class="text-danger">:message</p>') : '') !!}
			</div>
		@else
			<p class="padd_10">Ja, {{ $absence->employee->user['first_name']  . ' ' . $absence->employee->user['last_name'] }} 
				<span class="">molim da mi se odobri</span>
			</p>
			<input name="employee_id" type="hidden" value="{{  $absence->employee_id }}" />
		@endif
		<div class="form-group {{ ($errors->has('type')) ? 'has-error' : '' }}">
			<label>@lang('absence.abs_type')</label>
			<select class="form-control"  name="type" value="{{ old('type') }}" id="request_type" required >
				<option disabled selected value></option>
				@foreach($absenceTypes as $absenceType)
					<option value="{{ $absenceType->mark }}" {!! $absence->type ==  $absenceType->id ? 'selected' : '' !!} >{{ $absenceType->name}}</option>
				@endforeach
			</select> 
			{!! ($errors->has('type') ? $errors->first('type', '<p class="text-danger">:message</p>') : '') !!}	
		</div>
		<div class="form-group datum date1 float_l {{ ($errors->has('start_date')) ? 'has-error' : '' }}" >
			<label>@lang('absence.start_date')</label>
			<input name="start_date" id="start_date" class="form-control" type="date" value="{{ $absence->start_date }}" required>
			{!! ($errors->has('start_date') ? $errors->first('start_date', '<p class="text-danger">:message</p>') : '') !!}
		</div>
		<div class="form-group datum date2 float_r {{ ($errors->has('end_date')) ? 'has-error' : '' }}" >
			<label>@lang('absence.end_date')</label>
			<input name="end_date" id="end_date" class="form-control" type="date" value="{{ $absence->end_date }}" required>
			{!! ($errors->has('end_date') ? $errors->first('end_date', '<p class="text-danger">:message</p>') : '') !!}
		</div>
		<div class="col-md-12 clear_l overflow_hidd padd_0" >
			<div class="form-group time col-md-6 {{ ($errors->has('start_time')) ? 'has-error' : '' }}" hidden>
				<label>@lang('absence.start_time')</label>
				<input name="start_time" class="form-control" type="time" value="{{ $absence->start_time }}" required>
				{!! ($errors->has('start_time') ? $errors->first('start_time', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group time col-md-6 {{ ($errors->has('end_time')) ? 'has-error' : '' }}" hidden >
				<label>@lang('absence.end_time')</label>
				<input name="end_time" class="form-control" type="time" value="{{ $absence->end_time }}"required>
				{!! ($errors->has('end_time') ? $errors->first('end_time', '<p class="text-danger">:message</p>') : '') !!}
			</div>
		</div>
		<div class="form-group {{ ($errors->has('comment')) ? 'has-error' : '' }}">
			<label>@lang('basic.comment')</label>
			<textarea rows="4" name="comment" type="text" class="form-control" value="{{ old('comment') }}" required>{{ $absence->comment }}</textarea>
			{!! ($errors->has('comment') ? $errors->first('comment', '<p class="text-danger">:message</p>') : '') !!}
		</div>
		@if (Sentinel::inRole('administrator'))
			<div class="form-group">
				<label for="email">Slanje emaila:</label>
				<input type="radio" name="email" value="DA"  /> @lang('basic.send_mail') 
				<input type="radio" name="email" value="NE" checked /> @lang('basic.dont_send_mail')
			</div>
		@else
			<input type="hidden" name="email" value="DA">
		@endif
		{{ csrf_field() }}
		{{ method_field('PUT') }}
		<input class="btn-submit" type="submit" value="{{ __('basic.edit')}}" id="stil1">
		<a href="#" rel="modal:close" class="btn-close">@lang('basic.cancel')</a>
	</form>
</div>
<script  >
	$( document ).ready(function() {
		$( "#request_type" ).change(function() {
			if($(this).val() == 'IZL') {
				$('.form-group.time').show();
				$('.form-group.date2').hide();
				var start_date = $( "#start_date" ).val();
				var end_date = $( "#end_date" );
				end_date.val(start_date);
			} else {
				$('.form-group.time').hide();
				$('.form-group.date2').show();
			}
		});
		$( "#start_date" ).change(function() {
			var start_date = $( this ).val();
			var end_date = $( "#end_date" );
			end_date.val(start_date);
		
		});
	});
</script>
