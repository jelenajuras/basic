<div class="modal-header">
	<h3 class="panel-title">Ispravi korisnika</h3>
</div>
<div class="modal-body">
    <form accept-charset="UTF-8" role="form" method="post" action="{{ route('users.update', $user->id) }}">
    <fieldset>
        <div class="form-group {{ ($errors->has('first_name')) ? 'has-error' : '' }}">
            <input class="form-control" placeholder="First Name" name="first_name" type="text" value="{{ $user->first_name }}" />
            {!! ($errors->has('first_name') ? $errors->first('first_name', '<p class="text-danger">:message</p>') : '') !!}
        </div>
        <div class="form-group {{ ($errors->has('last_name')) ? 'has-error' : '' }}">
            <input class="form-control" placeholder="Last Name" name="last_name" type="text" value="{{ $user->last_name }}" />
            {!! ($errors->has('last_name') ? $errors->first('last_name', '<p class="text-danger">:message</p>') : '') !!}
        </div>
        <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}">
            <input class="form-control" placeholder="E-mail" name="email" type="text" value="{{ $user->email }}">
            {!! ($errors->has('email') ? $errors->first('email', '<p class="text-danger">:message</p>') : '') !!}
        </div>
        <h5>Roles</h5>
        @foreach ($roles as $role)
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="roles[{{ $role->slug }}]" value="{{ $role->id }}" {{ $user->inRole($role) ? 'checked' : '' }}>
                    {{ $role->name }}
                </label>
            </div>
        @endforeach
        <hr />
        <div class="form-group  {{ ($errors->has('password')) ? 'has-error' : '' }}">
            <input class="form-control" placeholder="Password" name="password" type="password" value="">
            {!! ($errors->has('password') ? $errors->first('password', '<p class="text-danger">:message</p>') : '') !!}
        </div>
        <div class="form-group {{ ($errors->has('password_confirmation')) ? 'has-error' : '' }}">
            <input class="form-control" placeholder="Confirm Password" name="password_confirmation" type="password" />
            {!! ($errors->has('password_confirmation') ? $errors->first('password_confirmation', '<p class="text-danger">:message</p>') : '') !!}
        </div>
        {{ csrf_field() }}
		{{ method_field('PUT') }}
        <input class="btn btn-lg btn-primary btn-block" type="submit" value="Update">
    </fieldset>
    </form>
</div>