<?php

namespace App\Http\Controllers;

use App\Jobs\TimetableCreate;
use App\Jobs\TimetableNoticeEnd;
use App\Jobs\TimetableNoticeStart;
use App\Models\RedBtnQuestion;
use App\Models\RedBtnUser;
use App\Models\Timetable;
use App\Telegram;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;

class TelegramController extends Controller
{
    protected $telegram;
    protected $chat_id;
    protected $text;
    protected $user;
    protected $weekDay = [
        '1' => 'понедельник',
        '2' => 'вторник',
        '3' => 'среда',
        '4' => 'четверг',
        '5' => 'пятница',
        '6' => 'суббота',
        '0' => 'воскресенье',
    ];
    
    public function __construct()
    {
        $this->telegram = new Api(config('telegram.bots.mybot.token'));
    }
    
    public function handleRequest(Request $request)
    {
        
        if (isset($request['callback_query']))
            logger($request['callback_query']);
        
        logger($request['message']);
        $this->chat_id = $request['message']['chat']['id'] ?? $request['callback_query']['from']['id'];
        
        if (!isset($request['message']['text']) and !isset($request['callback_query']['data']))
            return 200;
        $this->text = $request['message']['text'] ?? $request['callback_query']['data'];
        
        $user = \App\User::query()->firstOrCreate([
            'tg_id' => $this->chat_id,
        ], [
            'name' => 'Ждем имя',
            'email' => $this->chat_id . '@kubsuBot.ru',
            'password' => bcrypt(1),
        ]);
        file_put_contents(public_path('request.json'), json_encode($request['message']));
        
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
                if (stristr(strtolower($this->text), 'асибо')) {
                    $this->sendMessage('Рад помочь!');
                }
                
                if (stristr($this->text, '/date')) {
                    $date = explode(' ', $this->text);
                    if (isset($date[1])) {
                        $this->timetableSend($date[1]);
                    }
                }
                if (stristr($this->text, '-')) {
                    $date = explode('-', $this->text);
                    $carb = Carbon::parse('2020-01-01');
                    if (isset($date[1])) {
                        for ($i = 0; $i < 12; $i++) {
                            if ($carb->copy()->monthName == $date[1]) {
                                $this->calendarMonth($carb->copy()->format('m'));
                                break;
                            }
                            $carb->addMonths($i);
                        }
                    }
                }
                switch ($this->text) {
                    case '/today':
                        $this->timetableSend(1);
                        break;
                    case '/tomorrow':
                        $this->timetableSend(2);
                        break;
                    case  '/calendar':
                        $this->calendar();
                        break;
                }
                $this->showMenu();
                
            }
        }
        return 200;
    }
    
    private function calendarMonth($month)
    {
        $timetables = Timetable::query()
            ->where('date', 'LIKE', '%-' . $month . '-%')
            ->where('group_id', $this->user->group_id)
            ->orderBy('date', 'asc')
            ->pluck('date');
        
        $response = $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => 'smth',
            'reply_markup' => json_encode(
                ['keyboard' =>
                    [[array("A", "B")]]]
            ),
        ]);
        
        $inline_keyboard = Keyboard::make()->inline();
        for ($i = 0; $i < 31; $i = $i + 3) {
            if (isset($timetables[$i]) and isset($timetables[$i + 1]) and isset($timetables[$i + 2])) {
                $inline_keyboard->row(
                    Keyboard::button(["text" => Carbon::parse($timetables[$i])->format('d'), 'callback_data' => '/date ' . Carbon::parse($timetables[$i])->format('Y-m-d')]),
                    Keyboard::button(["text" => Carbon::parse($timetables[$i + 1])->format('d'), 'callback_data' => '/date ' . Carbon::parse($timetables[$i + 1])->format('Y-m-d')]),
                    Keyboard::button(["text" => Carbon::parse($timetables[$i + 2])->format('d'), 'callback_data' => '/date ' . Carbon::parse($timetables[$i + 2])->format('Y-m-d')])
                );
            } elseif (isset($timetables[$i]) and isset($timetables[$i + 1])) {
                $inline_keyboard->row(
                    Keyboard::button(["text" => Carbon::parse($timetables[$i])->format('d'), 'callback_data' => '/date ' . Carbon::parse($timetables[$i])->format('Y-m-d')]),
                    Keyboard::button(["text" => Carbon::parse($timetables[$i + 1])->format('d'), 'callback_data' => '/date ' . Carbon::parse($timetables[$i + 1])->format('Y-m-d')])
                );
            } elseif (isset($timetables[$i])) {
                $inline_keyboard->row(
                    Keyboard::button(["text" => Carbon::parse($timetables[$i])->format('d'), 'callback_data' => '/date ' . Carbon::parse($timetables[$i])->format('Y-m-d')])
                );
            }
        }
        
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => 'Выберите день',
            'reply_markup' => 'ReplyKeyboardMarkup',
        ]);
    }
    
    public function showMenu($info = NULL)
    {
        
        $inline_keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(["text" => "Сегодня", 'callback_data' => '/today']),
                Keyboard::inlineButton(["text" => "Завтра", 'callback_data' => '/tomorrow'])
            );
        if ($this->chat_id == 541726137)
            $inline_keyboard->row(
                Keyboard::inlineButton(["text" => "Календарь", 'callback_data' => '/calendar'])
            );
        
        $message = 'Необходимо выбрать день';
//        $message .= 'Также можно выбрать необходимую вам дату при помощи /date и через пробел дату: ' . PHP_EOL . '/date ' . now()->format('d.m.Y') . chr(10);
        
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $message,
            'reply_markup' => $inline_keyboard,
        ]);
    }
    
    public function newUser()
    {
        $message = 'Привет. Скоро твой аккаунт подтвердят и ты будешь получать расписание!';
        
        $this->sendMessage($message);
    }
    
    protected function sendMessage($message, $parse_html = FALSE)
    {
        try {
            $data = [
                'chat_id' => $this->chat_id,
                'text' => $message,
            ];
            
            if ($parse_html) $data['parse_mode'] = 'HTML';
            
            $this->telegram->sendMessage($data);
        } catch (\Exception $e) {
            $this->telegram->sendMessage([
                'chat_id' => '541726137',
                'text' => $e->getMessage(),
            ]);
            
        }
    }
    
    public function timetableSend($flag)
    {
        if ($flag === 1) {
            $startMessage = 'Расписание на сегодня:';
            $date = Carbon::parse(\request('date', now()));
        } elseif ($flag === 2) {
            $startMessage = 'Расписание на завтра:';
            $date = Carbon::parse(\request('date', now()->addDay()));
        } else {
            $date = Carbon::parse($flag);
            $startMessage = 'Расписание на '
                . $this->weekDay[$date->dayOfWeek] . ' '
                . $date->copy()->format('d.m.Y');
        }
        $timetable = Timetable::query()
            ->where('date', 'LIKE', '%' . $date->toDateString() . '%')
            ->where('group_id', $this->user->group_id)
            ->first();
        if (isset($timetable->id)) {
            $message = PHP_EOL . '';
            $type = 'c';
            if ($date->weekOfYear % 2 == 0)
                $type = 'z';
            foreach ($timetable->timetable as $times => $arr)
                if (isset($arr[$type]))
                    $message .= $arr[$type]['time'] . PHP_EOL . '   -' . $arr[$type]['lecture'] . PHP_EOL . '     -' . $arr[$type]['teacher'] . PHP_EOL;
        } else {
            $message = 'Выходной';
        }
        
        $this->telegram->sendMessage([
                'chat_id' => $tg_id ?? $this->chat_id,
                'text' => $startMessage . ' ' . $message,
            ]
        );
    }
    
    private function calendar()
    {
        $months = [];
        $timetables = Timetable::query()
            ->where('group_id', $this->user->group_id)
            ->get();
        foreach ($timetables as $timetable) {
            if (!in_array(Carbon::parse($timetable->date)->monthName, $months))
                $months[] = Carbon::parse($timetable->date)->monthName;
        }
        logger($months);
        $inline_keyboard = Keyboard::make()
            ->inline();
        foreach ($months as $month)
            $inline_keyboard->row(Keyboard::inlineButton(["text" => $month, 'callback_data' => '/month-' . $month]));
        
        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => 'Выберите месяц',
            'reply_markup' => $inline_keyboard,
        ]);
    }
    
    public function sendAll()
    {
        ;
        $users = \App\User::query()->whereNotNull('group_id')->whereNotNull('tg_id')->get();
        foreach ($users as $user) {
            $this->chat_id = $user->tg_id;
            $this->timetableSend(\request('flag', 1));
        }
    }
    
    public function start()
    {
        TimetableNoticeStart::handle();
    }
    
    public function end()
    {
        TimetableNoticeEnd::handle();
    }
    
    public function create()
    {
        TimetableCreate::handle();
    }
    
}
