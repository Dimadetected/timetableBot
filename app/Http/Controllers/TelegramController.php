<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram\Bot\Api;

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
        $url = 'https://kubsu.4wr.ru/' . env('TELEGRAM_BOT_TOKEN') . '/webhook';
        $response = $this->telegram->setWebhook(['url' => $url]);
        
        return $response == TRUE ? $response : dd($response);
    }
    
    public function handleRequest(Request $request)
    {
        $this->chat_id = $request['message']['chat']['id'];
        $this->username = $request['message']['from']['username'];
        $this->text = $request['message']['text'];
        
        switch ($this->text) {
            case '/start':
            case '/menu':
            default:
                $this->showMenu();
        }
    }
    
    public function showMenu($info = NULL)
    {
        $message = '';
        if ($info) {
            $message .= $info . chr(10);
        }
        $message .= '/menu' . chr(10);
        $message .= '/getGlobal' . chr(10);
        $message .= '/getTicker' . chr(10);
        $message .= '/getCurrencyTicker' . chr(10);
        
        $this->sendMessage($message);
    }
    
    protected function sendMessage($message, $parse_html = FALSE)
    {
        $data = [
            'chat_id' => $this->chat_id,
            'text' => $message,
        ];
        
        if ($parse_html) $data['parse_mode'] = 'HTML';
        
        $this->telegram->sendMessage($data);
    }
}
