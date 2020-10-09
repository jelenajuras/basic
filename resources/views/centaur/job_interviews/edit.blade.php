
<span hidden class="locale" >{{ App::getLocale() }}</span>
<div class="modal-header">
	<h3 class="panel-title">@lang('basic.add_employee')</h3>
</div>
<div class="modal-body">
	<form accept-charset="UTF-8" role="form" method="post" action="{{ route('job_interviews.update', $job_interview->id) }}">
		<fieldset>
			<div class="form-group  {{ ($errors->has('first_name')) ? 'has-error' : '' }}">
                <label for="first_name">@lang('basic.f_name')</label>
                <input class="form-control" id="first_name" name="first_name" maxlength="20" type="text" value="{{ $job_interview->first_name }}" required />
                {!! ($errors->has('first_name') ? $errors->first('first_name', '<p class="text-danger">:message</p>') : '') !!}
            </div>
            <div class="form-group  {{ ($errors->has('last_name')) ? 'has-error' : '' }}">
                <label for="last_name">@lang('basic.l_name')</label>
                <input class="form-control" name="last_name" maxlength="20" id="last_name" type="text" value="{{ $job_interview->last_name }}" required />
                {!! ($errors->has('last_name') ? $errors->first('last_name', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('date')) ? 'has-error' : '' }}">
				<label>@lang('basic.date')</label>
				<input class="form-control" name="date" type="date" value="{{ $job_interview->date }}"  />
				{!! ($errors->has('date') ? $errors->first('date', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('oib')) ? 'has-error' : '' }}">
				<label>@lang('basic.oib')</label>
				<input class="form-control" name="oib" type="number" step="1" maxlength="20" value="{{ $job_interview->oib }}" required />
				{!! ($errors->has('oib') ? $errors->first('oib', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}">
				<label>e-mail</label>
				<input class="form-control" name="email" type="email" maxlength="50" value="{!! isset($user1) ? $user1->email : $job_interview->email !!}" required />
				{!! ($errors->has('email') ? $errors->first('email', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('phone')) ? 'has-error' : '' }}">
				<label>@lang('basic.mobile')</label>
				<input class="form-control" name="phone" type="text" maxlength="50" value="{{ $job_interview->phone }}"  />
				{!! ($errors->has('phone') ? $errors->first('phone', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('language')) ? 'has-error' : '' }}">
				<label>@lang('basic.language')</label>
				<input class="form-control" name="language" type="text" maxlength="50" value="{{ $job_interview->language }}"  />
				{!! ($errors->has('language') ? $errors->first('language', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('title')) ? 'has-error' : '' }}">
				<label>@lang('basic.metier')</label>
				<input name="title" type="text" class="form-control" maxlength="150" value="{{ $job_interview->title }}"   >
				{!! ($errors->has('title') ? $errors->first('title', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('qualifications')) ? 'has-error' : '' }}">
				<label>@lang('basic.qualifications')</label>
				<input name="qualifications" type="text" class="form-control" maxlength="20" value="{{ $job_interview->qualifications }}"   >
				{!! ($errors->has('qualifications') ? $errors->first('qualifications', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('work_id'))  ? 'has-error' : '' }}">
				<label>@lang('basic.work')</label>
				<select class="form-control" name="work_id" required >
					<option selected="selected" disabled></option>
					@foreach($works as $work)
						<option name="work_id" value="{{ $work->id }}" {!! $work->id == $job_interview->work_id ? 'selected' : '' !!}>{{ $work->name . ' - '. $work->department['name'] }}</option>
					@endforeach	
				</select>
				{!! ($errors->has('work_id') ? $errors->first('work_id', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('years_service'))  ? 'has-error' : '' }}">
				<label>@lang('basic.years_experience')</label><br>
				<input name="years_service" type="number" value="{{ $job_interview->years_service }}"  >				
				{!! ($errors->has('years_service') ? $errors->first('years_service', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('salary')) ? 'has-error' : '' }}">
				<label>@lang('basic.salary')</label>
				<input class="form-control" name="salary" type="number" step="0.01" value="{{ $job_interview->salary }}" pattern="[-+]?[0-9]*[.,]?[0-9]+"
				title="This must be a number with up to 2 decimal places" />
				{!! ($errors->has('salary') ? $errors->first('salary', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('comment'))  ? 'has-error' : '' }}" style="padding-top: 10px">
				<label>@lang('basic.comment') </label>
				<textarea class="form-control" maxlength="65535" name="comment">{{ $job_interview->comment }}</textarea>
				{!! ($errors->has('comment') ? $errors->first('comment', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			{{ method_field('PUT') }}
			{{ csrf_field() }}
			<input class="btn-submit" type="submit" value="{{ __('basic.edit')}}">
		</fieldset>
	</form>
</div>
<span hidden class="locale" >{{ App::getLocale() }}</span>