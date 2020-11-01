<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class TelegramService
{

    private $token, $url;

    public function __construct($token)
    {
        $this->token = $token;
        $this->url = 'https://api.telegram.org/bot' . $this->token;
    }

    /**
     * Получение информации о боте
     * @return string
     */
    public function getMe()
    {
        $response = Http::get($this->url . '/getMe');
        return $response->body();
    }


    /**
     * Установление вебхука
     * @return \Illuminate\Http\Client\Response|void
     */
    public function setWebHook()
    {
        
        $url = 'http://kubsubot.ru/' . $this->token . '/webhook';
        dump($url);
        dump($this->url . '/setWebhook');
        $response = Http::get($this->url . '/setWebhook', [
            'url' => $url
        ]);
        dump($response->body());
        return $response->body();
    }


    /**
     * Отправление сообщений
     * @param $arr
     */
    public function sendMessage($arr)
    {
        $response = Http::post($this->url . '/sendMessage', $arr);
        return $response->body();
    }

    public function sendChatAction($arr)
    {
        $response = Http::post($this->url . '/sendChatAction', $arr);
        return json_decode($response->body(),true);
    }
}
