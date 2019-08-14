@if(!empty($telegram_username))
  <h2 class="sub-header">Available commands to activate/deactivate on Telegram</h2><br>
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
        {{ Form::open(array('action' => 'BotConfigController@setUserID')) }}
        @foreach($telegram_db_data as $config_settings)
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
                {{ Form::checkbox('is_active_'.$config_settings->id,"value", $config_settings->is_active) }} 
                @if (isset($_POST['is_active_'.$config_settings->id]))
                  It works
                @endif
              
              </td>
            </tr>
        @endforeach
      </tbody>
      
    </table>
    {{ Form::submit('Save Settings') }} 
    {{ Form::close() }}
  </div>
@endif