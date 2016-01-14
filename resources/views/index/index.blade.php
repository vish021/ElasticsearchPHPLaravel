@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xs-6 col-xs-offset-3">
            <div id="search-wrapper">
                <form action="/" method="POST" class="form-inline">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <input type="text" name="query" class="form-control" placeholder="Enter your search query" value="{{ $query or '' }}" />
                        <button type="submit" name="search-button" class="form-control glyphicon glyphicon-search"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (!empty($hits))
        @foreach ($hits as $hit)
            <div class="row">
                <div class="col-xs-8 col-xs-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">{{ $hit['_source']['name'] }}</div>
                        <div class="panel-body">{{ $hit['_source']['description'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    @elseif (isset($hits))
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                <p>No results!</p>
            </div>
        </div>
    @endif
@endsection