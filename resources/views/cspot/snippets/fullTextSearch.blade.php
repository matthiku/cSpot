
<span id="fulltext-search" class="nav-link link" data-toggle="tooltip" 


    @if ( Request::has('filterby') && Request::get('filterby')=='fulltext' )
            title="Clear filter">

        Search: <span class="bg-info" onclick="showFilterField('fulltext', this)">&nbsp;{{ Request::get('filtervalue') }} </span>
        
        <i id="filter-fulltext-clear" class="fa fa-close ml-1" title="reset the search" 
        	onclick="showFilterField('fulltext', 'clear')"> </i>

    @else
    	  onclick="showFilterField('fulltext', this)"
            title="Full-text search in titles, author lyrics etc">
        <i id="filter-fulltext-show" class="fa fa-search"> </i>

    @endif


</span>

