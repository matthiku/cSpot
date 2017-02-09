<?php 
    $count = Auth::user()->newThreadsCount(); 
?>
@if($count > 0)
    (unread: <span class="label label-danger">{!! $count !!}</span>)
@endif