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
        $data['user_id'] = Auth::user()->id;
        $data['email'] = Auth::user()->email;
        $data['telegram_username'] = Auth::user()->telegram_username;

        $telegram = $this->seed($data['telegram_username']);

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
        $data['telegram_username'] = Auth::user()->telegram_username;

        $telegram = $this->seed($data['telegram_username']);

        $data['telegram_db_data'] = $telegram;

        $data['msg_type'] = 'success';
        $data['msg_value'] = 'Your Bot Settings Have been configured';

        return view('bot_config')->with($data);
    }

    public function seed($telUser){
      try {
            $telegram = Telegram::where('username', $telUser)->get();
        } catch (Exception $exception) {
            $seed = array(
                array('username' => $telUser, 
                      'command' => '/start', 
                      'default_setting' => '', 
                      'is_active' => 0),
                array('username' => $telUser, 
                      'command' => '/getUserID', 
                      'default_setting' => '', 
                      'is_active' => 1),
                array('username' => $telUser, 
                      'command' => '/getBTCEquivalent', 
                      'default_setting' => 'USD', 
                      'is_active' => 1),
                array('username' => $telUser, 
                      'command' => '/getGlobal', 
                      'default_setting' => '', 
                      'is_active' => 0)
            );

            Telegram::insert($seed);
            $telegram = Telegram::where('username', $telUser)->get();
        }

      return $telegram;
    }
}
