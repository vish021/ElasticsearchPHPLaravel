@extends('layouts.master')

@section('content')
    <div id="search-wrapper">
        <form action="/" method="POST" class="form-inline">
            {{ csrf_field() }}

            <div class="form-group">
                <input type="text" name="query" class="form-control" placeholder="Enter your search query" />
                <button type="submit" name="search-button" class="form-control glyphicon glyphicon-search"></button>
            </div>
        </form>
    </div>
@endsection