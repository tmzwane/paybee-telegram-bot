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
        $this->chat_id = $request['message']['chat']['id'];
        $this->username = $request['message']['from']['username'];
        $this->text = $request['message']['text'];

        try {
            $telegram = Telegram::where('username', $this->username)->get();
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
            $telegram = Telegram::where(array('username' => $this->username, 'is_active' => 1 ))->get();
        }
 
        switch (true) {
            case strpos($this->text, '/start'):
            	$this->sendMessage('All good things start like this :-)');
            	break;
            case strpos($this->text, '/menu') && in_array('/menu', $telegram):
                $this->showMenu();
                break;
            case strpos($this->text, '/getUserID') && in_array('/getUserID', $telegram):
                $this->getUserID();
                break;
            case strpos($this->text, '/getBTCEquivalent') && in_array('/getBTCEquivalent', $telegram):
                $this->getBTCEquivalent();
                break;
            case strpos($this->text, '/getGlobal') && in_array('/getGlobal', $telegram):
                $this->getGlobal();
                break;
            default:
                $this->sendMessage('Not allowed, configure settings on your profile at '.env('APP_URL'));
        }
    }
 
    public function showMenu($info = null)
    {
        $message = '';
        if ($info) { $message .= $info . chr(10); }
        
        switch (true) {
            case in_array('/menu', $telegram):
                $message .= '/menu'.chr(10);
                break;
            case in_array('/getUserID', $telegram):
                $message .= '/getUserID'.chr(10);
                break;
            case in_array('/getBTCEquivalent', $telegram):
                $message .= '/getBTCEquivalent [amount] [currency]'.chr(10);
                break;
            case in_array('/getUserID', $telegram):
                $message .= '/getGlobal'.chr(10);
                break;
        }
        
        $this->sendMessage($message);
    }
 
    public function getGlobal()
    {
        $client = new Client();
		$res = $client->get('https://api.coindesk.com/v1/bpi/currentprice.json');
		$data = json_decode($res->getBody());
        $this->sendMessage(json_encode($data), true);
    }

 
    public function getBTCEquivalent()
    {
        $client = new Client();
		$res = $client->get('https://api.coindesk.com/v1/bpi/currentprice.json');
		$data = json_decode($res->getBody(), TRUE);

		if (isset($this->text) && !empty($this->text)) {
			$pieces = explode(" ", $this->text);
			if (count($pieces) > 2) {
				$currency = $data['bpi'][$pieces[2]];
				$quantity = $pieces[1];
				$rate = floatval($data['bpi'][$pieces[2]]['rate_float']);
				$total_rate = $quantity / $rate ;
			} else {
				$currency = 'USD';
				$quantity = 1;
				$rate = floatval($data['bpi'][$currency]['rate_float']);
				$total_rate = $quantity / $rate ;
			}
		} else {
			$currency = 'USD';
			$quantity = 1;
			$rate = floatval($data['bpi'][$currency]['rate_float']);
			$total_rate = $quantity / $rate ;
		}
		$total_rate = number_format((float)$total_rate, 7, '.', '');
        $this->sendMessage($quantity." ".$currency." is ".$total_rate." BTC (".$rate." ".$currency." - 1 BTC)", true);
    }
 
    public function checkDatabase()
    {
        try {
            $telegram = Telegram::where('username', $this->username)->latest()->firstOrFail();
 
            if ($telegram->command == 'getUserID') {
 				$this->sendMessage($this->username, true);
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
}
