

$(document).ready(function() {

  $(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $('[data-toggle="popover"]').popover();
    $('.popover-dismiss').popover({
        trigger: 'focus'
    });
  });
  
});


function toggleTrashed() {
    $('.trashed').toggle();
    if ($('#toggleBtn').text() == 'Show') {
        $('#toggleBtn').text('Hide');
    } else {
        $('#toggleBtn').text('Show');
    }
}


function blink(selector){
    $(selector).animate({opacity:0}, 150, "linear", function(){
        $(this).delay(50);
        $(this).animate({opacity:1}, 150, function(){
            blink(this);
        });
        $(this).delay(500);
    });
}


/***
 * 
 */
function showNextSelect(fromOrTo, what) {
    book = $('#from-book').val();

    // make sure all fields are visible now
    $('.select-version').show();
    $('#'+fromOrTo+'-'+what).show();

    // decide which url is needed to get the data
    if (what=='chapter') {
        url = '/bible/books/'+book;
    } else {
        chapter = $('#from-chapter').val();
        url = '/bible/books/'+book+'/chapter/'+chapter;
    }

    // API call to get the books/chapter/verses data
    $.get(url, function(data, status) {

        // access the <select> element 
        var x = document.getElementById(fromOrTo+'-'+what);

        // clear the element of all current options
        for (i=x.length; i>=0; i--) {
            x.remove(i);
        }

        if ( status == 'success') {
            minNumber = 1
            if (fromOrTo=='to' && what=='verse') {
                minNumber = $('#from-verse').val();
            }
            for (var i = minNumber; i <= data; i++) {
                var option = document.createElement("option");
                option.text = i;
                option.value = i;
                x.add(option);
            }
            // if book has only one chapter, populate the verses right now
            if (what=='chapter' && data==1) {
                showNextSelect(fromOrTo, 'verse');
            }
            if (fromOrTo=='from' && what=='verse') {
                showNextSelect('to', 'verse');
            }
        }
    });
}
function populateComment() {
    // set default and minimum value identical with 'from' value
    $('#comment').val( $('#from-book').val()+' '+$('#from-chapter').val()+':'+$('#from-verse').val()+'-'+$('#to-verse').val() );
}




var timeoutID;
function delayedCloseFlashingModals(selector) {
    timeoutID = window.setTimeout( closeMyModal, 2000, selector);
}
function closeMyModal(selector) {
    $(selector).modal('hide');
    // set focus again on main input field
    $('.main-input').focus();    
}