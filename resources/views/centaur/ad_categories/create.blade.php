<div class="modal-header">
    <h3 class="panel-title">@lang('basic.add_category')</h3>
</div>
<div class="modal-body">
    <form accept-charset="UTF-8" role="form" method="post" action="{{ route('ad_categories.store') }}">
        <div class="form-group {{ ($errors->has('name'))  ? 'has-error' : '' }}">
            <label>@lang('basic.name')</label>
            <input name="name" type="text" class="form-control" value="{{ old('name') }}">
            {!! ($errors->has('name') ? $errors->first('name', '<p class="text-danger">:message</p>') : '') !!}
        </div>
        {{ csrf_field() }}
        <input class="btn-submit" type="submit" value="{{ __('basic.save')}}" id="stil1">
    </form>
</div>