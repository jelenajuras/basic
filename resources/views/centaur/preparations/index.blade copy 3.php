@extends('Centaur::layout')

@section('title', 'Priprema i mehanička obrada')

@section('content')
@php  
set_time_limit(120);
use App\Models\PreparationRecord;
use App\Models\EquipmentList;  @endphp
<span hidden class="today">{{ date('Y-m-d') }}</span>
<div class="page-header">
<div style="float:right"><span class="alert alert-danger" style="display: block; margin: 0;">Molim obrisati cache sa ctrl+f5 da se povuće novi dizajn</span></div>
    <h1>Priprema i mehanička obrada</h1>
     <div class='btn-toolbar pull-right'>
        <span class="show_inactive">Prikaži neaktivne</span>
        <label class="filter_empl">
            <input type="search" placeholder="Traži..." id="mySearch_preparation">
            <i class="clearable__clear">&times;</i>
        </label>
         <!--  <a href="{{ route('preparations.create') }}" rel="modal:open"><img class="" src="{{ URL::asset('icons/plus.png') }}" alt="arrow" /></a>-->
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="table-responsive">
            <div class="table table-hover table_preparations" id="index_table">
                <div class="thead">
                    <p class="tr">
                        <span class="th file_input"></span>
                        <span class="th project_no_input">Broj</span>
                        <span class="th name_input">Naziv</span>
                        <span class="th delivery_input">Datum isporuke</span>
                        <span class="th manager_input">Voditelj projekta</span>
                        <span class="th designed_input">Projektirao</span>
                        <span class="th date_input">Datum</span>
                        <span class="th preparation_input">Priprema</span>
                        <span class="th mechanical_input">Mehanička obrada</span>
                        <span class="th mechanical_input">Oznake i dokumentacija</span>
                        <span class="th equipment_input">Oprema</span>
                        <span class="th history_input">Povijest</span>
                       <span class="th option_input">Opcije</span>
                    </p>
                </div>
                
                <div class="tbody">
                    @foreach ($preparations as $proj_no => $preparation1)
                        @if($preparation1->toArray()[0]['active'] == 1)
                            <h4 class="collapsible_project">{{ $proj_no }}</h4>
                        @endif
                        @foreach ($preparation1 as $preparation)
                            @if (Sentinel::getUser()->id == $preparation->project_manager || 
                            Sentinel::getUser()->id == $preparation->designed_by || 
                            Sentinel::inRole('administrator') || 
                            Sentinel::inRole('subscriber') || 
                            Sentinel::inRole('priprema') || 
                            Sentinel::inRole('list_view') || 
                            Sentinel::inRole('upload_list') )
                                <!-- Ispis pripreme -->  
                                @php
                                    $preparationRecords1 = PreparationRecord::where('preparation_id',$preparation->id)->get();
                                    $preparationRecord_today = $preparationRecords1->where('preparation_id',$preparation->id)->where('date', date('Y-m-d'))->first();
                                @endphp
                               
                                <p class="tr row_preparation_text {!! $preparation->active == 1 ? 'active' : 'inactive' !!} {{ str_replace(':','_', $proj_no)  }}">
                                     @if ( $preparation->active == 1) 
                                        <a class="open_upload_link"><i class="fas fa-upload"></i><span class="preparation_id"></span></a>
                                    @endif
                                    <span class="td text_preparation file_input">
                                    </span>
                                    <span class="td text_preparation project_no_input">{{ $preparation->project_no  }}</span>
                                    <span class="td text_preparation name_input">{{ $preparation->name }}</span>
                                    <span class="td text_preparation delivery_input">{!! $preparation->delivery ? date('d.m.Y', strtotime($preparation->delivery)) : '' !!}</span>
                                    <span class="td text_preparation manager_input">{{ $preparation->manager['first_name'] . ' ' . $preparation->manager['last_name']  }}</span>
                                    <span class="td text_preparation designed_input">{{ $preparation->designed['first_name'] . ' ' . $preparation->designed['last_name']  }}</span>
                                    <span class="td text_preparation date_input">{{ date('d.m.Y')}}</span>
                                    <span class="td text_preparation date_change preparation_input"   >
                                        @if ( $preparation->active == 1)
                                            @if (!Sentinel::inRole('moderator') && ! Sentinel::inRole('list_view') )
                                                @if ($preparationRecord_today)
                                                    <span class="date_{{ $preparationRecord_today->date }}">{{ $preparationRecord_today->preparation }}</span>
                                                @endif
                                            @endif
                                        @endif
                                    </span>
                                    <!-- Mehanička obrada -->
                                    <span class="td text_preparation date_change mechanical_input">
                                        @if ( $preparation->active == 1)
                                            @if (!Sentinel::inRole('moderator')&& ! Sentinel::inRole('list_view') )
                                                @if ($preparationRecord_today)
                                                    <span class="date_{{ $preparationRecord_today->date }}">{{ $preparationRecord_today->mechanical_processing }}</span>
                                                @endif
                                            @endif
                                        @endif
                                    </span>
                                    <!-- Oznake i dokumentacija -->
                                    <span class="td text_preparation date_change mechanical_input">
                                        @if ( $preparation->active == 1)
                                            @if (!Sentinel::inRole('moderator')&& ! Sentinel::inRole('list_view') )
                                                @if ($preparationRecord_today)
                                                    <span class="date_{{ $preparationRecord_today->date }}">{{ $preparationRecord_today->marks_documentation }}</span>
                                                @endif
                                            @endif
                                        @endif
                                    </span>
                                    <!-- Upis opreme -->                                      
                                    <span class="td text_preparation equipment_input">    
                                        @if ( $preparation->active == 1)
                                            @php
                                                $equipmentLists = EquipmentList::where('preparation_id', $preparation->id )->get();
                                            @endphp
                                            @if( count($equipmentLists)>0)
                                                @if ( $equipmentLists->where('level1',1)->first())
                                                    @foreach ($equipmentLists->where('level1', 1) as $equipment_level1)
                                                        <a href="{{ route('equipment_lists.edit', ['id' => $preparation->id, 'equipment_level1' => $equipment_level1 ] ) }}" class="equipment_lists_open" rel="modal:open">{{ $equipment_level1->product_number }}</a>
                                                    @endforeach
                                                @else
                                                    <a href="{{ route('equipment_lists.edit', $preparation->id ) }}" class="equipment_lists_open" rel="modal:open">Upis opreme</a>
                                                @endif
                                            
                                                <a href="{{ route('multiReplaceItem', ['preparation_id' => $preparation->id] ) }}" class="equipment_lists_open multi_replace" rel="modal:open">Zamjena</a> 
                                                @if (! Sentinel::inRole('list_view'))
                                                    @if($equipmentLists->where('preparation_id', $preparation->id )->first()->mark != null )
                                                        <a class="btn-file-input equipment_lists_mark" href="{{ action('EquipmentListController@export', ['id' => $preparation->id ]   ) }}" ><i class="fas fa-download"></i> Preuzmi oznake</a>
                                                    @endif
                                                @endif
                                            @else
                                                <small>Nema zapisa</small>
                                            @endif
                                        @endif
                                    </span>
                                    <!-- Povijest zapisa -->
                                    <span class="td text_preparation history_input">
                                        @if ( !Sentinel::inRole('moderator') && ! Sentinel::inRole('list_view'))
                                            @if ($preparationRecords1->where('date', '<>', date('Y-m-d'))->first())
                                                <button class="arrow_collaps {!! $preparationRecords1->where('date', '<>', date('Y-m-d'))->first() ? 'collapsible' : '' !!}" id="{{ $preparation->id }}" type="button" {!! $preparationRecords1->where('date', '<>', date('Y-m-d'))->first() ? 'style="cursor:pointer"' : '' !!}><i class="fas fa-caret-down"></i></button> 
                                            @else
                                                <small>Nema povijesti</small>
                                            @endif
                                        @endif
                                    </span>
                                    <!-- Opcije -->
                                    <span class="td text_preparation option_input">
                                        @if (! Sentinel::inRole('list_view') )
                                            <a href="#" class="btn btn-edit">
                                                <span class="glyphicon glyphicon-edit" aria-hidden="true" title="Ispravi"></span>
                                            </a>
                                            @if (Sentinel::inRole('administrator'))   
                                                <a href="{{ route('preparations.destroy', $preparation->id) }}" class="action_confirm btn btn-delete" data-method="delete" data-token="{{ csrf_token() }}" title="Obriši">
                                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                </a>
                                                <a href="{{ action('PreparationController@close_preparation', $preparation->id) }}" class="btn" class="action_confirm">
                                                    <i class="fas fa-check"></i>
                                                    @if ($preparation->active == 1)Završi @else Vrati @endif                                                       
                                                </a>
                                            @endif
                                        @endif                                           
                                    </span>
                                </p>
                                <!-- Edit pripreme -->
                                    @include('centaur.preparation_edit')
                                @if ($preparationRecords1->where('date', '<>', date('Y-m-d'))->first())
                                    <!-- Zapisi pripreme -->
                                    <div class="content" id="content_{{ $preparation->id }}">
                                        @foreach ( $preparationRecords1->where('date', '<>', date('d-m-Y')) as $record )
                                            @include('centaur.preparation_record')
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @endforeach              
                    
                    <!-- Novi unos -->     
                    @if( Sentinel::inRole('moderator') || Sentinel::inRole('voditelj') || Sentinel::inRole('administrator') || Sentinel::inRole('upload_list'))
                        @include('centaur.preparation_create')
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="upload_links" >
    <h3>Upload</h3>
    @if(! Sentinel::inRole('subscriber'))
        <form class="upload_file" action="{{ action('EquipmentListController@import') }}" method="POST" enctype="multipart/form-data">
            <div class="file-input-wrapper">
                <button class="btn-file-input"><i class="fas fa-upload"></i> Upload</button>
                <input type="file" name="file" required />
                <input type="text" class="prep_id" name="preparation_id" hidden />
            </div>
            @csrf
        </form>
        @if( Sentinel::inRole('list_view') ||  Sentinel::inRole('administrator'))
            <form class="upload_file_replace" action="{{ action('EquipmentListController@import_with_replace') }}" method="POST" enctype="multipart/form-data" title ="Multiple replace">
                <div class="file-input-wrapper">
                    <button class="btn-file-input"><i class="fas fa-exchange-alt"></i> Upload sa zamjenom</button>
                    <input type="file" name="file" required />
                    <input type="text" class="prep_id" name="preparation_id" hidden />
                </div>
                @csrf
            </form>
        @endif
        @if( Sentinel::inRole('administrator') || Sentinel::inRole('moderator') || Sentinel::inRole('upload_list'))
            <form class="upload_file_replace" action="{{ action('EquipmentListController@importSiemens') }}" method="POST" enctype="multipart/form-data" title ="Multiple replace">
                <div class="file-input-wrapper">
                    <button class="btn-file-input"><i class="fas fa-upload"></i> Upload Siemens Linde</button>
                    <input type="file" name="file" required />
                    <input type="text" class="prep_id" name="preparation_id" hidden />
                </div>
                @csrf
            </form>
        @endif 
    @endif           
</div>
<script>
    
    $('.open_upload_link').click(function(){
        var preparation_id = $( this ).find('.preparation_id').text();
        console.log(preparation_id);
        $('.upload_links .prep_id').val(preparation_id);
        $('.upload_links').modal();
        return false;
    });
    $('.table_preparations .inactive').hide();
    $('.show_inactive').click(function(){
        $('.table_preparations .inactive').toggle();
        $('.table_preparations .active').toggle();
        if($(this).text() == 'Prikaži neaktivne') {
            $(this).text('Prikaži aktivne');
            $('.upload_file').hide();
            $('.upload_file_replace').hide();
        } else {
            $(this).text('Prikaži neaktivne');
            $('.upload_file').show();
            $('.upload_file_replace').show();
        }
    });
    $('.upload_file input[type=file]').change(function(){
        $(this).parent().parent().submit();
    });
    $('.upload_file_replace input[type=file]').change(function(){
        $(this).parent().parent().submit();
    });
    $('.collapsible').click(function(){
        var id = $(this).attr('id');
      
        $(this).parent().parent().siblings('#content_'+id).toggle();
    });
    $('.collapsible_project').click(function(){
        var id = $(this).text();
        id = id.replace(':','_');

        if( $('p.row_preparation_text.'+id).css('display') == 'flex' ) {
          
            $('p.row_preparation_text.'+id).css('display','none');
        } else {
           
            $('p.row_preparation_text.'+id).css('display','flex');
        }
      //  $(this).next('.open_upload_link').toggle();
        
    });
    
    $('a.btn-edit').click(function(event ){
        event.preventDefault();
        $(this).parent().parent().next('.form_preparation').css('display','flex');
        $(this).parent().parent().hide();
    });
    $('a.btn-cancel').click(function(event ){
        event.preventDefault();
        $(this).parent().parent().prev('.row_preparation_text').show();
        $(this).parent().parent().hide();
    });
    $('a.btn-cancel2').click(function(event ){
        event.preventDefault();
        $(this).parent().parent().prev('p').show();
        $(this).parent().parent().hide();
    });
    $('.equipment_lists_open').click(function(){
        $.modal.defaults = {
            closeExisting: false,    // Close existing modals. Set this to false if you need to stack multiple modal instances.
            escapeClose: true,      // Allows the user to close the modal by pressing `ESC`
            clickClose: false,       // Allows the user to close the modal by clicking the overlay
            closeText: 'Close',     // Text content for the close <a> tag.
            closeClass: '',         // Add additional class(es) to the close <a> tag.
            showClose: true,        // Shows a (X) icon/link in the top-right corner
            modalClass: "modal equipment_lists",    // CSS class added to the element being displayed in the modal.
            // HTML appended to the default spinner during AJAX requests.
            spinnerHtml: "<div id='loader'><span class='ajax-loader1'></span></div>",
        
            showSpinner: true,      // Enable/disable the default spinner during AJAX requests.
            fadeDuration: null,     // Number of milliseconds the fade transition takes (null means no transition)
            fadeDelay: 0.5          // Point during the overlay's fade-in that the modal begins to fade in (.5 = 50%, 1.5 = 150%, etc.)
            };
    });
    $('.open_upload_link').click(function(){
        $.modal.defaults = {
            closeExisting: false,    // Close existing modals. Set this to false if you need to stack multiple modal instances.
            escapeClose: true,      // Allows the user to close the modal by pressing `ESC`
            clickClose: false,       // Allows the user to close the modal by clicking the overlay
            closeText: 'Close',     // Text content for the close <a> tag.
            closeClass: '',         // Add additional class(es) to the close <a> tag.
            showClose: true,        // Shows a (X) icon/link in the top-right corner
            modalClass: "modal",    // CSS class added to the element being displayed in the modal.
            // HTML appended to the default spinner during AJAX requests.
            spinnerHtml: "<div id='loader'><span class='ajax-loader1'></span></div>",
        
            showSpinner: true,      // Enable/disable the default spinner during AJAX requests.
            fadeDuration: null,     // Number of milliseconds the fade transition takes (null means no transition)
            fadeDelay: 0.5          // Point during the overlay's fade-in that the modal begins to fade in (.5 = 50%, 1.5 = 150%, etc.)
            };
    });
  
    $('#mySearch_preparation').keyup(function() {
        text = $('.show_inactive').text();
       
        var trazi = $( this ).val().toLowerCase();
        if(text == 'Prikaži neaktivne') {
            trazi_status = '.active';
        } else {
            trazi_status = '.inactive';
        }

        $('.row_preparation_text' + trazi_status).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(trazi) > -1)
        });
        $('.form_preparation:visible').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(trazi) > -1)
        });
    });	
    
    $('.clearable__clear').click(function(){
        $('#mySearch_preparation').val('');
        $('.row_preparation_text' + trazi_status).show();
 //       $('.form_preparation').hide();
    });
</script>
@stop