<?php

namespace App\Http\Controllers;

use App\Telegram;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BotConfigController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = array();

        $data['id'] = Auth::user()->id;

        $data['email'] = Auth::user()->email;

        $telegram = Telegram::where('user_id', $telId)->get();

        $data['telegram_user_id'] = Auth::user()->telegram_user_id;

        $telegram = $this->seed($data['telegram_user_id']);

        $data['telegram_db_data'] = $telegram;

        $data['msg_type'] = '';
        
        $data['msg_value'] = '';

        return view('bot_config')->with($data);
    }

    public function setUserID(Request $request)
    {
        $telegram_username =  $request->input('telegram_username');
        
        $data = array();
        $data['user_id'] = Auth::user()->id;
        $data['email'] = Auth::user()->email;

        User::where('email', $data['email'])->update(array('telegram_username' => $telegram_username));

        $telegram = $this->seed($data['telegram_username']);

        $data['telegram_db_data'] = $telegram;
        $data['msg_type'] = 'success';
        $data['msg_value'] = 'Your Telegram User ID is now linked to you PayBee Profile';
    

        return view('bot_config')->with($data);
    }

    public function saveBotConfig(Request $request)
    {
        $input =  $request->input(); unset($input['_token']);

        foreach ($input as $key => $value) {
            $id = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $field_name = str_replace('_'.$id,'',$key);
            if ($field_name == 'is_active') {
              switch ($value) {
                case 'Yes':
                  $value = true;
                  break;
                case 'No':
                  $value = false;
                  break;
              }
            }

            Telegram::where('id', $id)->update(array($field_name => $value));
        }

        $data = array();
        $data['user_id'] = Auth::user()->id;
        $data['email'] = Auth::user()->email;
        $data['telegram_user_id'] = Auth::user()->telegram_user_id;

        $telegram = $this->seed($data['telegram_user_id']);

        $data['telegram_db_data'] = $telegram;

        $data['msg_type'] = 'success';
        $data['msg_value'] = 'Your Bot Settings Have been configured';

        return view('bot_config')->with($data);
    }

    public function seed($telId){
      
      try {
            $telegram = Telegram::where('user_id', $telId)->get();
        } catch (Exception $exception) {
            $commands = ['/start', '/getUserID', '/getBTCEquivalent', '/getGlobal'];
            $seed_data = array(); $data = array();

            if ( isset($profile['username']) ) {
                $data['username'] = $profile['username'];
            }

            if (isset($profile['first_name'])) {
                $data['first_name'];
            }

            if (isset($profile['last_name'])) {
                $data['last_name'];
            }

            foreach ($commands as $command) {
                
                $data['command'] = $command;
                
                if ($command == '/getBTCEquivalent') {
                    $data['is_active'] = 1;
                }

                if ($command == '/getBTCEquivalent' || $command == '/getUserID') {
                    $data['default_setting'] = 'USD';
                }

                array_push($seed_data, $data);
                unset($data['command']); unset($data['default_setting']);
            }
            

            Telegram::insert($seed_data);
            $telegram = Telegram::where('username', $telUser)->get();
        }

      return $telegram;
    }
}
