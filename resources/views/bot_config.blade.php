@extends('layouts.app')
@section('content')
<div class="container">
  <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header">Telegram Bot Configurations</h1>

    <div class="row featurette">
        <div class="col-md-7 order-md-2">
          <br><br><br>
          <br><h2 class="featurette-heading">
            @if(!empty($telegram_username))
                Telegram UserID: {{$telegram_username}}
            @else
                {{ Form::open(array('action' => 'BotConfigController@setUserID')) }}
                {{ Form::label('telegram_username', 'Enter your Telegram UserID') }}
                {{ Form::text('telegram_username')}} 
                {{ Form::submit('Set Telegram UserID')}} 
                {{ Form::close() }}
            @endif
          </h2>
        </div>
        <div class="col-md-5 order-md-1">
          <img src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Content" class="featurette-image" width="350" height="350">
        </div>
      </div>
    <br>
    @include('bot_config_settings')
  </div>
</div>
</div>
@endsection