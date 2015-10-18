@extends('base')

@section('main')
        <form method="POST" action="/list" style="margin-bottom: 70px;>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div id="create-list-form-left">
                <h2>Create a List</h2>
                <div class="form-group">
                    <label class="inline-label">List Name:</label>
                    <input type="text" pattern="[a-zA-Z]{1,30}" class="form-control" placeholder="Up to 30 Characters" name="name">
                </div>
                <div class="form-group">
                    <label class="inline-label">Add Label:</label>
                    <input type="text" pattern="[a-zA-Z]{1,30}" class="form-control" placeholder="Up to 30 Characters" name="name">
                </div>
                <div class="form-group">
                    <label class="inline-label">Description:</label>
                    <input type="text" pattern="[a-zA-Z]{1,30}" class="form-control" placeholder="Up to 30 Characters" name="name">
                </div>
                <div class="form-group">
                    <label class="inline-label">Reference:</label>
                    <input type="text" pattern="[a-zA-Z]{1,30}" class="form-control" placeholder="Up to 30 Characters" name="name">
                </div>
                <div class="form-group">
                    <label class="inline-label">Privacy:</label>
                    <input type="text" pattern="[a-zA-Z]{1,30}" class="form-control" placeholder="Up to 30 Characters" name="name">
                </div>
                <div class="form-group">
                    <label class="inline-label">Add to Folder:</label>
                    <input type="text" pattern="[a-zA-Z]{1,30}" class="form-control" placeholder="Up to 30 Characters" name="name">
                </div>
            </div>
            <div id="create-list-form-right"></div>
        </form>
@endsection