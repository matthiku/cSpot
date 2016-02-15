

$(document).ready(function() {
  $(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
    $('.popover-dismiss').popover({
        trigger: 'focus'
    });
  });
});



function blink(selector){
    $(selector).animate({opacity:0}, 150, "linear", function(){
        $(this).delay(50);
        $(this).animate({opacity:1}, 150, function(){
            blink(this);
        });
        $(this).delay(950);
    });
}


var timeoutID;
function delayedCloseFlashingModals(selector) {
    timeoutID = window.setTimeout( closeMyModal, 2000, selector);
}
function closeMyModal(selector) {
    $(selector).modal('hide');
}