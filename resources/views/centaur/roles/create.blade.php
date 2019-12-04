<div class="modal-header">
	<h3 class="panel-title">Nova uloga</h3>
</div>
<div class="modal-body">
    <form accept-charset="UTF-8" role="form" method="post" action="{{ route('roles.store') }}">
    <fieldset>
        <div class="form-group {{ ($errors->has('name')) ? 'has-error' : '' }}">
            <input class="form-control" placeholder="Name" name="name" type="text" value="{{ old('name') }}" />
            {!! ($errors->has('name') ? $errors->first('name', '<p class="text-danger">:message</p>') : '') !!}
        </div>
        <div class="form-group {{ ($errors->has('slug')) ? 'has-error' : '' }}">
            <input class="form-control" placeholder="slug" name="slug" type="text" value="{{ old('slug') }}" />
            {!! ($errors->has('slug') ? $errors->first('slug', '<p class="text-danger">:message</p>') : '') !!}
        </div>

        <h5>Permissions:</h5>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="permissions[users.create]" value="1">
                users.create
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="permissions[users.update]" value="1">
                users.update
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="permissions[users.view]" value="1">
                users.view
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="permissions[users.destroy]" value="1">
                users.destroy
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="permissions[roles.create]" value="1">
                roles.create
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="permissions[roles.update]" value="1">
                roles.update
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="permissions[roles.view]" value="1">
                roles.view
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="permissions[roles.delete]" value="1">
                roles.delete
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="permissions[employees.create]" value="1">
                employees.create
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="permissions[employees.update]" value="1">
                employees.update
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="permissions[employees.view]" value="1">
                employees.view
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="permissions[employees.delete]" value="1">
                employees.delete
            </label>
        </div>
        <div class="checkbox">
                <label>
                    <input type="checkbox" name="permissions[projects.create]" value="1">
                    projects.create
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="permissions[projects.update]" value="1">
                    projects.update
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="permissions[projects.view]" value="1">
                    projects.view
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="permissions[projects.delete]" value="1">
                    projects.delete
                </label>
            </div>
        {{ csrf_field() }}
        <input class="btn btn-lg btn-primary btn-block" type="submit" value="Create">
    </fieldset>
    </form>
</div>