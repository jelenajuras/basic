@extends('Centaur::layout')

@section('title', __('basic.posts'))

@section('content')
    <div class="page-header">
		@if(Sentinel::getUser()->hasAccess(['posts.create']))
			<div class='btn-toolbar pull-right'>
				<a class="btn btn-primary btn-lg" href="{{ route('posts.create') }}">
					<i class="fas fa-plus"></i>
					@lang('basic.new_post')
				</a>
			</div>
		@endif
        <h1>@lang('basic.posts')</h1>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="table-responsive">
				
				<div class="panel-footer">
					<form class="comment_form" accept-charset="UTF-8" role="form" method="post" action="{{ route('comment.store') }}">
						<div class="form-group {{ ($errors->has('content')) ? 'has-error' : '' }}">
							<textarea name="content" type="text" class="form-control" rows="2" >{{ old('content') }}</textarea>
							{!! ($errors->has('content') ? $errors->first('content', '<p class="text-danger">:message</p>') : '') !!}
							<input name="employee_id" value="{{ Sentinel::getUser()->employee->id }}" hidden >
							<input name="post_id" value="{{ $post->id }}" hidden >
						</div>
						{{ csrf_field() }}
						<input class="btn-submit" type="submit" value="Spremi komentar">
					</form>
				</div>		
				
				@foreach ($comments as $comment)
					<div class="panel-body">
						{!! $comment->content !!} | <small><span class="glyphicon glyphicon-user" aria-hidden="true"></span> {{ $comment->employee->user['first_name'] . ' ' . $comment->employee->user['last_name'] }} | <span class="glyphicon glyphicon-time" aria-hidden="true"></span> {{ \Carbon\Carbon::createFromTimeStamp(strtotime($comment->created_at))->diffForHumans() }} </small>
					</div>	
				@endforeach
				
            </div>
        </div>
    </div>
<script> // on submit ajax store
/*$(document).ready(function(){
	$('.comment_form').on('submit',function(e){
		$(this).addClass('active');
		var comment;
		comment = $(this).find('input.comment_form').html();
		var post;
		post = $(this).find('input#employee_id').val();
		var post_content = $('form.active #post-content').val();
		var form = $(this);
		var url = form.attr('action');
		e.preventDefault();
		var data = form.serialize();
		var url = '/comment/store';
		var post = form.attr('method');
		var umetni = $(this);
		$.ajax({
			type : post,
			url : url,
			data : data,
			success:function(msg) {
				umetni.after(); 
				$('.post-content').html('');
			}
		})
	});
});*/
</script>
@stop
<!--
<div class="post-content">
	@if(count($comments) > 0)
		@foreach ($comments as $comment)
			<div class="media">
				<div class="f_left">
					<h5 class="media-heading">{{ $comment->employee['email'] }} | <small>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($comment->created_at))->diffForHumans() }} </small></h5>
					{{ $comment->content}}
				</div>
			</div>
		@endforeach	

	@else		
		<p>{{'No Comments!'}}</p>	
	@endif

</div>		
-->