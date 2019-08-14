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

        try {
            $telegram = Telegram::where('username', $data['telegram_username'])->get();
        } catch (Exception $exception) {
            $seed = array(
                array('username' => $data['telegram_username'], 
                      'command' => '/start', 
                      'default_setting' => '', 
                      'is_active' => 0),
                array('username' => $data['telegram_username'], 
                      'command' => '/getUserID', 
                      'default_setting' => '', 
                      'is_active' => 1),
                array('username' => $data['telegram_username'], 
                      'command' => '/getBTCEquivalent', 
                      'default_setting' => 'USD', 
                      'is_active' => 1),
                array('username' => $data['telegram_username'], 
                      'command' => '/getGlobal', 
                      'default_setting' => '', 
                      'is_active' => 0)
            );

            Telegram::insert($seed);
            $telegram = Telegram::where('username', $data['telegram_username'])->get();
        }

        $data['telegram_db_data'] = $telegram;

        return view('bot_config')->with($data);
    }

    public function setUserID(Request $request)
    {
        $telegram_username =  $request->input('telegram_username');
        
        $data = array();
        $data['user_id'] = Auth::user()->id;
        $data['email'] = Auth::user()->email;

        User::where('email', $data['email'])->update(array('telegram_username' => $telegram_username));

        $seed = array(
            array('username' => $telegram_username, 
                  'command' => '/start', 
                  'default_setting' => '', 
                  'is_active' => 0),
            array('username' => $telegram_username, 
                  'command' => '/getUserID', 
                  'default_setting' => '', 
                  'is_active' => 1),
            array('username' => $telegram_username, 
                  'command' => '/getBTCEquivalent', 
                  'default_setting' => 'USD', 
                  'is_active' => 1),
            array('username' => $telegram_username, 
                  'command' => '/getGlobal', 
                  'default_setting' => '', 
                  'is_active' => 0)
        );

        Telegram::insert($seed);
        
        $telegram = Telegram::where('username', $telegram_username)->get();
        
        $data['telegram_db_data'] = $telegram;

        $msg = [
            'type' => 'success',
            'value' => 'Your Telegram User ID is now linked to you PayBee Profile',
        ];

        return redirect()->back()->with($msg);
    }

    public function saveBotConfig(Request $request)
    {
        $input =  $request->input(); unset($input['_token']);

        foreach ($input as $key => $value) {
            $id = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $field_name = str_replace('_'.$id,'',$key);
            Telegram::where('id', $id)->update(array($field_name => $value));
        }
        
        $msg = array();

        $msg['type'] = 'success';
        $msg['value'] = 'Your Bot Settings Have been configured';

        return redirect()->back()->with($msg);
    }
}
