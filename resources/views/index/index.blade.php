@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xs-6 col-xs-offset-3">
            <div id="search-wrapper">
                <form action="/" method="GET" class="form-inline">
                    <div class="form-group">
                        <input type="text" name="query" class="form-control" placeholder="Enter your search query" value="{{ $query or '' }}" />
                        <button type="submit" name="search-button" class="form-control glyphicon glyphicon-search"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (!empty($hits))
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2" id="results-text">
                Displaying results {{ ($from + 1) }} to {{ $to }} of {{ $total }}.
            </div>
        </div>

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

        <div class="row">
            <div class="pagination-wrapper col-xs-8 col-xs-offset-2">
                <nav>
                    <ul class="pagination">
                        <li>
                            <a href="?query={{ urlencode($query) }}&page={{ ($page - 1) }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        @for ($i = 1; $i <= 10; $i++)
                            <li {!! $i == $page ? 'class="active"' : '' !!}><a href="?query={{ urlencode($query) }}&page={{ $i }}">{{ $i }}</a></li>
                        @endfor

                        <li>
                            <a href="?query={{ urlencode($query) }}&page={{ ($page + 1) }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    @elseif (isset($hits))
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                <p>No results!</p>
            </div>
        </div>
    @endif
@endsection