<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    protected $telegram;
    protected $chat_id;
    protected $username;
    protected $text;
    protected $user;

    public function __construct()
    {
        $this->telegram = new Api(config('telegram.bots.mybot.token'));
    }

    public function getMe()
    {
        $response = $this->telegram->getMe();
        return $response;
    }

    public function setWebHook()
    {
        $url = 'https://kubsu.4wr.ru/' . config('telegram.bots.mybot.token') . '/webhook';
        $response = $this->telegram->setWebhook(['url' => $url]);

        return $response == TRUE ? $response : dd($response);
    }

    public function handleRequest(Request $request)
    {
        $this->chat_id = $request['message']['chat']['id'];
        $this->username = $request['message']['from']['username'];
        $this->text = $request['message']['text'];

        $user = \App\User::query()->firstOrCreate([
            'tg_id' => $this->chat_id,
        ], [
            'name' => 'Ждем имя',
            'email' => $this->chat_id . '@kubsuBot.ru',
            'password' => bcrypt(1),
        ]);

        $this->user = $user;

        if ($user->name == 'Ждем имя') {
            if (is_null($user->remember_token)) {
                $this->sendMessage('Введи ФИО');
                $user->update(['remember_token' => 1]);
            } else {
                $user->update(['name' => $this->text]);
            }
        } else {
            if (is_null($user->group_id)) {
                $this->newUser();
            } else {
                switch ($this->text) {
                    case '/today':
                        $this->timetableSend(1);
                        break;
                    case '/tomorrow':
                        $this->timetableSend(2);
                        break;
                    default:
                        $this->showMenu();
                }

            }
        }
        return 'ok';
    }

    public function showMenu($info = NULL)
    {
        $message = '';

        $message .= '/today' . chr(10);
        $message .= '/tomorrow' . chr(10);

        $this->sendMessage($message);
    }

    public function newUser()
    {
        $message = 'Привет. Скоро твой аккаунт подтвердят и ты будешь получать расписание!';

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

    public function timetableSend($flag = FALSE)
    {
        if ($flag == 1) {
            $startMessage = 'Расписание на сегодня:';
            $date = Carbon::parse(\request('date', now()));
        } else{
            $startMessage = 'Расписание на завтра:';
            $date = Carbon::parse(\request('date', now()->addDay()));
        }

        $timetable = Timetable::query()
            ->where('date', 'LIKE', '%' . $date->toDateString() . '%')
//            ->where('group_id', $this->user->group_id)
            ->where('group_id', 1)
            ->first();
        $message = '';
        $type = 'c';
        if ($date->weekOfYear % 2 == 0)
            $type = 'z';
        foreach ($timetable->timetable as $times => $arr)
            if (isset($arr[$type]))
                $message .= $arr[$type]['time'] . ' | ' . $arr[$type]['lecture'] . ' | ' . $arr[$type]['teacher'] . PHP_EOL;

        $this->sendMessage($startMessage . ' ' . PHP_EOL . $message);
    }
}
