var locale = $('.locale').text();
var saved;

if(locale == 'en') {
    saved = "Data saved successfully.";
} else {
    saved = "Podaci su spremljeni";
}
$('.close_travel').click(function(e){
    console.log("close_travel");
    e.preventDefault();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var url = $(this).attr('href');
    console.log(url);
    $.ajax({
        url: url,
        type: "get",
        success: function( response ) {

            $('tbody').load(location.origin + '/travel_orders' + ' tbody>tr',function(){
                $.getScript( '/../restfulizer.js');
                $.getScript( '/../js/travel.js');
            });
            $('<div><div class="modal-header"><span class="img-success"></span></div><div class="modal-body"><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>success:</strong>' + response + '</div></div></div>').appendTo('body').modal();
        },
        error: function(jqXhr, json, errorThrown) {
            console.log(jqXhr.responseJSON); 
        }

    });
}); 