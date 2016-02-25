
$(document).ready(function() {

  $(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $('[data-toggle="popover"]').popover();
    $('.popover-dismiss').popover({
        trigger: 'focus'
    });
  });
  
});


/***
 * Build a Bible Reference string
 */
function showNextSelect(fromOrTo, what) {
    book = $('#from-book').val();

    // make sure all fields are visible now
    $('.select-reference').show();

    // decide which url is needed to get the data
    if (what=='chapter') {
        url = '/bible/books/'+book;
    } else {
        chapter = $('#from-chapter').val();
        url = '/bible/books/'+book+'/chapter/'+chapter;
    }

    // remove old options from select box
    emptyRefSelect(fromOrTo, what);
    var x = document.getElementById(fromOrTo+'-'+what);

    // API call to get the books/chapter/verses data
    $.get(__app_url+url, function(data, status) {

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
            if (what=='chapter') {
                showNextSelect(fromOrTo, 'verse');
            }
            if (fromOrTo=='from' && what=='verse') {
                showNextSelect('to', 'verse');
                $('.select-version').show();                
            }
            $('#'+fromOrTo+'-'+what).show();
        }
    });
}
function populateComment() {
    // ignore if nothing was selected
    if ($('#from-book').val()==null || $('#from-book').val()==' ') { 
        return; }

    // check existing comment
    oldComment = $('#comment').val();
    if (oldComment.length>0) {
        oldComment += '; ';
    }

    // set default and minimum value identical with 'from' value
    $('#comment').val( oldComment
        + $('#from-book').val()+' '
        + $('#from-chapter').val()+':'
        + $('#from-verse').val() 
        +($('#to-verse').val() != $('#from-verse').val() ? '-'+$('#to-verse').val() : '') + ' ('
        + $('#version').val() + ')'
        );

    $('#from-book').val('');
    emptyRefSelect('from', 'chapter');
    emptyRefSelect('from', 'verse');
    emptyRefSelect('to', 'verse');
    $('#version').val('');
    $('.select-reference').hide();
    $('.select-version').hide();
    $('#col-2-song-search').hide();
}
function emptyRefSelect(fromOrTo, what) {
    // get the <select> element 
    var x = document.getElementById(fromOrTo+'-'+what);
    $(x).hide();
    // clear the element of all current options
    for (i=x.length; i>=0; i--) {
        x.remove(i);
    }
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

