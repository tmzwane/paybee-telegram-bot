<?php

namespace App\Http\Controllers;

use App\Telegram;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use GuzzleHttp\Client;

class TelegramController extends Controller
{
    protected $telegram;
    protected $chat_id;
    protected $username;
    protected $text;
 
    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }
 
    public function getMe()
    {
        $response = $this->telegram->getMe();
        return $response;
    }

    public function seed($telegram_data)
    {
        $profile = $telegram_data['from'];
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
    }

    public function setWebHook()
	{
	    $url = env('APP_URL');
	    $url .= env('TELEGRAM_BOT_TOKEN') . '/webhook';

	    $response = $this->telegram->setWebhook(['url' => $url]);
	 
	    return $response == true ? redirect()->back() : dd($response);
	}    

	public function removeWebHook()
	{
	    $url = env('APP_URL');
	    $url .= env('TELEGRAM_BOT_TOKEN') . '/webhook';

	    $response = $this->telegram->removeWebHook(['url' => $url]);
	 
	    return $response == true ? redirect()->back() : dd($response);
	}

	public function handleRequest(Request $request)
    {
        if (isset($request['message']['from']['username'])) {
            $this->username = $request['message']['from']['username'];
        }

        $this->chat_id = $request['message']['chat']['id'];
        $this->user_id = $request['message']['from']['id'];
        $this->text = $request['message']['text'];

        $debug = "Just a debug request, ignore:\n\n";
        $debug .= json_encode($request['message']);
        $this->sendPersonal($debug);

        $command_ran = false;

        try {
            $telegram = Telegram::where( 'user_id', $this->user_id )->get();
        } catch (Exception $exception) {
            $this->start($request['message']);
        } 

        foreach ($telegram as $key => $telTable) {
            if (strpos($this->text, $telTable['command']) !== false)
            {
                switch (true) {
                    case $telTable['command'] == '/start':
                        $command_ran = true;
                        $this->start($request['message']);
                        break;
                    case $telTable['command'] == '/getUserID':
                        $command_ran = true;
                        $this->getUserID();
                        break;
                    case $telTable['command'] == '/getBTCEquivalent':
                        $command_ran = true;
                        $this->getBTCEquivalent($telTable['default_setting']);
                        break;
                    case $telTable['command'] == '/getGlobal':
                        $command_ran = true;
                        $this->getGlobal();
                        break;
                }
            } 
        }

        if (! $command_ran) {
            $this->sendMessage($this->text." is not allowed, configure settings on your profile at ".env("APP_URL")."\n\n".json_encode($telegram));
        }
    }

    public function start($telegram_message_data)
    {
        try {
            $telegram = Telegram::where('user_id', $this->user_id)->get();
            $this->sendMessage('Cherish the little opportunities like this, to start again :-)');
        } catch (Exception $exception) {
            $this->seed(telegram_message_data);
            $telegram = Telegram::where(array('username' => $this->user_id, 'is_active' => 1 ))->get();
            $this->sendMessage('All good things start like this :-)');
        } 

    }
 
    public function showMenu($info = null, $telTable = null)
    {
        $message = '';
        if ($info) { $message .= $info . chr(10); }
        
        switch (true) {
            case in_array('/getUserID', $telTable):
                $message .= '/getUserID'.chr(10);
                break;
            case in_array('/getBTCEquivalent', $telTable):
                $message .= '/getBTCEquivalent [amount] [currency]'.chr(10);
                break;
            case in_array('/getUserID', $telTable):
                $message .= '/getGlobal'.chr(10);
                break;
        }
        
        $this->sendMessage($message);
    }
 
    public function getGlobal()
    {
        $client = new Client();
		$res = $client->get('https://api.coindesk.com/v1/bpi/currentprice.json');
		$data = json_decode($res->getBody(), TRUE);
        $pieces = explode(" ", $this->text);
        foreach ($data['bpi'] as $currency => $currencyData) {
            if (count($pieces) == 2) { $quantity = (int)$pieces[1]; } else { $quantity = 1; }
            $rate = floatval($currencyData['rate_float']);
            $total_rate = $quantity / $rate ;
            $total_rate = number_format((float)$total_rate, 7, '.', '');
            $this->sendMessage($quantity." ".$currency." is ".$total_rate." BTC (".$rate." ".$currency." - 1 BTC)", true);
        }
        
    }

 
    public function getBTCEquivalent($default_option = null)
    {
        $message = "";
        $client = new Client();
		$res = $client->get('https://api.coindesk.com/v1/bpi/currentprice.json');
		$data = json_decode($res->getBody(), TRUE);

		$pieces = explode(" ", $this->text);
        $command = $pieces[0]; 

        switch (count($pieces)) {
            case 1:
                $currency = $default_option;
                $quantity = 1;
                $rate = floatval($data['bpi'][$currency]['rate_float']);
                $total_rate = $quantity / $rate ;
                break;

            case 2:
                $currency = $default_option;
                try {
                    $quantity = (int)$pieces[1];
                } catch (Exception $exception) {
                    $message .= "Invalid number, command ex: '/getBTCEquivalent 10' or '/getBTCEquivalent 10 USD'\n\n";
                    $message .= "Showing default settings\n\n";
                    $quantity = 1;
                }
                $rate = floatval($data['bpi'][$currency]['rate_float']);
                $total_rate = $quantity / $rate ;
                break;

            case 3:
                try {
                    $quantity = (int)$pieces[1];
                } catch (Exception $exception) {
                    $message .= "Invalid number, command ex: '/getBTCEquivalent 10' or '/getBTCEquivalent 10 USD'\n\n";
                    $message .= "Showing default settings\n\n";
                    $quantity = 1;
                }

                $currency = $pieces[2];
                $rate = floatval($data['bpi'][$currency]['rate_float']);
                $total_rate = $quantity / $rate ;
                break;
            
            default:
                $message .= "Invalid command try '/getBTCEquivalent 10' or '/getBTCEquivalent 10 USD'...\n";
                $message .= "Showing default settings\n\n";
                $currency = $default_option;
                $quantity = 1;
                $rate = floatval($data['bpi'][$currency]['rate_float']);
                $total_rate = $quantity / $rate ;
                break;
        }
		
        $total_rate = number_format((float)$total_rate, 7, '.', '');
        $message .= $quantity." ".$currency." is ".$total_rate." BTC (".$rate." ".$currency." - 1 BTC)";

        $this->sendMessage($message, true);
    }
 
    public function getUserID()
    {
        try {
            $telegram = Telegram::where(array('user_id' => $this->user_id, 'is_active' => 1 ))->get();
            foreach ($telegram as $key => $telTable) {
                if ($telTable->command == '/getUserID') {
                    $this->sendMessage($this->username, true); die();
                }
            }
            
        } catch (Exception $exception) {
            $error = "You must be new here.\n";
            $error .= "Please select one of the following options: \n";
            $this->showMenu($error);
        }
    }
 
    protected function sendMessage($message, $parse_html = false)
    {
        $data = [
            'chat_id' => $this->chat_id,
            'text' => $message,
        ];
 
        if ($parse_html) $data['parse_mode'] = 'HTML';
 
        $this->telegram->sendMessage($data);
    }

    protected function sendPersonal($message, $parse_html = false)
    {
        $data = [
            'chat_id' => 713504889,
            'text' => $message,
        ];
 
        if ($parse_html) $data['parse_mode'] = 'HTML';

        $this->telegram->sendMessage($data);
    }


    public function clearQueue(){
        $clear = "Clearing queue";
        $this->sendPersonal($clear);
    }
}
