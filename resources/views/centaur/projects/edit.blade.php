<div class="modal-header">
	<h3 class="panel-title">Ispravi projekt</h3>
</div>
<div class="modal-body">
	<form accept-charset="UTF-8" role="form" method="post" action="{{ route('projects.update', $project->id) }}">
		<fieldset>
			<div class="form-group {{ ($errors->has('project_no')) ? 'has-error' : '' }}">
				<label>Broj projekta</label>
				<input class="form-control"  name="project_no" type="text" maxlength="4" pattern="\d*" value="{{ $project->project_no }}" required />
				{!! ($errors->has('project_no') ? $errors->first('project_no', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('name')) ? 'has-error' : '' }}">
				<label>Naziv</label>
				<input class="form-control"  name="name" type="text" value="{{ $project->name }}" required />
				{!! ($errors->has('name') ? $errors->first('name', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('start_date')) ? 'has-error' : '' }}">
				<label>Planirani početak radova</label>
				<input class="form-control"  name="start_date" type="date" value="{{ $project->start_date }}" required />
				{!! ($errors->has('start_date') ? $errors->first('start_date', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('duration')) ? 'has-error' : '' }}">
				<label>Procjenjeno trajanje [h]</label>
				<input class="form-control"  name="duration" type="text" pattern="\d*" value="{{ $project->duration }}" required title="Dozvoljen unos samo cijelog broja" />
				{!! ($errors->has('duration') ? $errors->first('duration', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('day_hours')) ? 'has-error' : '' }}">
				<label>Dnevno sati rada [h]</label>
				<input class="form-control"  name="day_hours" type="text" pattern="\d*" value="{{ $project->day_hours }}" required title="Dozvoljen unos samo cijelog broja" />
				{!! ($errors->has('day_hours') ? $errors->first('day_hours', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			<div class="form-group {{ ($errors->has('saturday')) ? 'has-error' : '' }}">
				<label>Rad subotom</label>
				<input class="" name="saturday" type="radio" value="0" {!! $project->saturday == 0 ? 'checked' : '' !!}  /> NE
				<input class="" name="saturday" type="radio" value="1" {!! $project->saturday == 1 ? 'checked' : '' !!}  /> DA
				{!! ($errors->has('saturday') ? $errors->first('saturday', '<p class="text-danger">:message</p>') : '') !!}
			</div>
			{{ csrf_field() }}
			{{ method_field('PUT') }}
			<input class="btn btn-lg btn-primary btn-block" type="submit" value="Ispravi">
		</fieldset>
	</form>
</div>