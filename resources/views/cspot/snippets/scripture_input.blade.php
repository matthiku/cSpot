
<!-- 
    Plan Overview Page 

    scripture reference selection 
-->

@if ($part=='one')
    <h6><i class="fa fa-book">&nbsp;</i>Add Bible Reference:</h6>

    <select name="from-book" id="from-book" class="pull-xs-left" onchange="showNextSelect('from', 'chapter')">
        <option selected="TRUE" value=" ">select book...</option>
        @foreach ($bibleBooks->getArrayOfBooks() as $book)
            <option value="{{ $book }}">{{ $book }}</option>
        @endforeach                        
    </select>&nbsp;

    <span class="select-reference" style="display: none;">                    
        ch.
        <select name="from-chapter" id="from-chapter" style="display: none;" 
                onchange="showNextSelect('from', 'verse')">
            <option selected="" value=" "> </option>
        </select>
        verse 
        <select name="from-verse" id="from-verse" style="display: none;"
                onchange="showNextSelect('to', 'verse')">
            <option selected="" value=" "> </option>
        </select>
        to 
        <select name="to-verse" id="to-verse" style="display: none;">
            <option selected="" value=" "> </option>
        </select>
    </span>
@endif
@if ($part=='two')
    <span class="select-version" style="display: none;>
        {!! Form::label('version', 'Select version:'); !!}
        <select name="version" id="version" onchange="populateComment()">
            <option {{ isset($item) ? '' : 'selected' }}>
            </option>
            @foreach ($versionsEnum as $vers)
                <option value="{{ $vers }}">{{ $vers }}
                </option>
            @endforeach
        </select>
    </span>
@endif