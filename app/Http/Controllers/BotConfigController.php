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

        return view('bot_config')->with($data);
    }

    public function saveBotConfig(Request $request)
    {
        echo "Well done";
    }
}
