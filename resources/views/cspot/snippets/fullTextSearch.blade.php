
    <span id="fulltext-search" class="link m-l-1 pull-xs-left" onclick="showFilterField('fulltext')" data-toggle="tooltip" 
        @if ( Request::has('filterby') && Request::get('filterby')=='fulltext' )
                title="Clear filter">
            Search: <span class="bg-info">&nbsp;{{ Request::get('filtervalue') }} </span>
            <i id="filter-fulltext-clear" class="fa fa-close"> </i>
        @else
                title="Full-text search in titles, author lyrics etc">
            <i id="filter-fulltext-show" class="fa fa-search"> </i>
        @endif
    </span>

