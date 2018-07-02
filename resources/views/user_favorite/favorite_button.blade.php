    @if (Auth::user()->is_favorite($fid))
        {!! Form::open(['route' => ['user.unfavorite', $fid], 'method' => 'delete']) !!}
            {!! Form::submit('UnFavorte', ['class' => "btn btn-success btn-xs"]) !!}
        {!! Form::close() !!}
    @else
        {!! Form::open(['route' => ['user.favorite', $fid]]) !!}
            {!! Form::submit('Favorite', ['class' => "btn btn-default btn-xs"]) !!}
        {!! Form::close() !!}
    @endif
