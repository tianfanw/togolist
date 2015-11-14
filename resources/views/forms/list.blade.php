<form id="list-form" method="POST" action="/list{{ isset($loc_list) ? '/'.$loc_list['id'] : '' }}" enctype="multipart/form-data" >
    <div id="list-form-container" class="clearfix">
        <div id="list-form-left">

            <h2>{{ isset($loc_list) ? 'Edit List' : 'Create a List' }}</h2>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="validation" value="1">
            <div class="error-message">
                @if (count($errors) > 0)
                    {{ $errors->first() }}
                @endif
            </div>
            <div class="form-group">
                <label class="inline-label">List Name:</label>
                <div class="input-wrapper">
                    <input type="text" pattern="[a-zA-Z0-9 ]{2,30}" class="form-control" style="width: 95%"
                           placeholder="Up to 30 Characters" name="name" value="{{ isset($loc_list) ? $loc_list['name'] : old('name') }}" required>
                </div>
            </div>
            <div class="form-group">
                <label class="inline-label">Add Label:</label>
                <div class="input-wrapper">
                    <div id="category" class="select">

                        <div class="select-dropdown">
                            @foreach ($list_categories as $category)
                                <div>{{ $category }}</div>
                            @endforeach
                        </div>
                        <div class="selected">
                            <span class="selected-option"></span>
                            <span class="glyphicon glyphicon-triangle-bottom select-icon"></span>
                        </div>
                        <input type="hidden" name="category" value="{{  isset($loc_list) ? $loc_list['category'] : old('category') }}">
                    </div>
                    <div id="labels" class="clearfix">
                        <input type="hidden" name="labels" value="{{  isset($loc_list) ?  implode(",", $loc_list['labels']) : old('labels') }}">
                    </div>
                    <div class="form-group" id="label-edit">
                        <div class="error-message"></div>
                        <input type="text" class="form-control" placeholder="Add label" name="label-edit">
                        <span class="glyphicon glyphicon-plus-sign plus-button"></span>
                    </div>
                    <div id="add-more-labels">
                        <span class="button" id="add-label-button">Add More Labels</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="inline-label">Description:</label>
                <div class="input-wrapper">
                <textarea class="form-control" style="width: 95%; max-width: 95%" rows="5"
                          placeholder="Up to 500 Characters" name="description">{{ isset($loc_list) ? $loc_list['description'] : old('description') }}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="inline-label">Reference:</label>
                <div class="input-wrapper">
                    <?php $last_ref_index=1; ?>
                    @for ($i = 2; $i <= 5; $i++)
                        @if ( (isset($loc_list) ? $loc_list['reference'.$i] : old('reference'.$i)) )
                            <?php $last_ref_index = $i; ?>
                        @endif
                    @endfor
                    @for ($i = 1; $i <= 5; $i++)
                        <input type="text" class="form-control" placeholder="Paste a link to add more info about this list"
                               style="width: 95%; margin-bottom: 10px; display:{{ $i > $last_ref_index ? 'none':'block' }};"
                               name="{{ 'reference'.$i }}" value="{{ isset($loc_list) ? $loc_list['reference'.$i] : old('reference'.$i) }}">
                    @endfor
                    @if ($last_ref_index < 5)
                        <span class="button" id="add-reference-button">Add More reference</span>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label class="inline-label">Privacy:</label>
                <div class="input-wrapper">
                    @if ( (isset($loc_list) ? $loc_list['private'] : old('private') ) == 0)
                        <input type="radio" name="private" value="0" checked>Public
                        <input type="radio" name="private" value="1">Private
                    @else
                        <input type="radio" name="private" value="0">Public
                        <input type="radio" name="private" value="1" checked>Private
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label class="inline-label">Add to Folder:</label>
                <select id="folder-select" multiple="multiple">
                    <option value="aaa">aaa</option>
                    <option value="bbb">bbb</option>
                    <option value="aaa">ccc</option>
                    <option value="bbb">ddd</option>
                </select>
            </div>
            <div class="form-group form-submit" style="text-align: right; margin-top: 100px;">
                @include('partials.submit-button', ['name' => (isset($loc_list) ? 'Save' : 'Create') ])
            </div>

        </div>
        <div id="list-form-right">
            <h2>Add Locations</h2>
            <div id="map-wrapper" style="margin-top: 8px;">
                <div id="location-name-list">
                    <div class="hint">Search to add locations here...</div>
                </div>
                <div style="overflow:hidden;">
                    <input id="map-input" class="controls" type="text" placeholder="Search a location">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="locations">
    </div>
</form>