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
          <img src="{{ asset('tel-bot.png') }}" alt="Content" class="featurette-image" width="350" height="350">
        </div>
      </div>
    <br>
    @if(!empty($telegram_username))
      {{ Form::open(array('action' => 'BotConfigController@saveBotConfig')) }}
      <h2 class="sub-header">Available commands to activate/deactivate on Telegram</h2><br>
      @if(!empty($value))
      <div class="alert alert-{{ $type }} alert-dismissible fade show">
          <strong>Success</strong> {{ $value }}
          <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
      @endif
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th><h2>Command</h2></th>
              <th><h2>Default Setting</h2></th>
              <th><h2>Active</h2></th>
            </tr>
          </thead>
          <tbody>
            @foreach($telegram_db_data as $config_settings)
              <?php
                if( $config_settings->is_active == 0) { $options = array('No' => 'No','Yes' => 'Yes'); }
                else { $options = array('Yes' => 'Yes','No' => 'No'); }
              ?>
              <tr>
                <td>{{ $config_settings->id }}</td>
                <td>{{ $config_settings->command }}</td>
                <td>
                  {{Form::text("default_setting_".$config_settings->id, 
                               old( $config_settings->default_setting ) ? old( $config_settings->default_setting ) : (!empty( $config_settings->default_setting ) ? $config_settings->default_setting : null),
                               [
                                  "class" => "form-group",
                                  "placeholder" => $config_settings->default_setting,
                               ])
                  }}
                </td>
                <td>
                  {{ Form::select('is_active_'.$config_settings->id,  $options, null, ['class' => 'form-control']) }}
                </td>
              </tr>
            @endforeach
          </tbody>
          
        </table>
      </div>
      {{ Form::submit('Save Settings', array('id'=> 'button')) }} 
      {{ Form::close() }}
    @endif
  </div>
</div>
</div>
@endsection