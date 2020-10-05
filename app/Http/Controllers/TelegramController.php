<?php

namespace App\Http\Controllers;

use App\Jobs\TimetableCreate;
use App\Jobs\TimetableNoticeEnd;
use App\Jobs\TimetableNoticeStart;
use App\Models\RedBtnQuestion;
use App\Models\RedBtnUser;
use App\Models\Timetable;
use App\Telegram;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;

class TelegramController extends Controller
{
    protected $telegram,$redBtnBot;
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
        $this->redBtnBot = new Api(config('telegram.bots.redBtn.token'));
    }
    public function redBtnBot(Request $request)
    {
        $this->chat_id = $request['message']['chat']['id'] ?? $request['callback_query']['from']['id'];
        $this->text = $request['message']['text'] ?? $request['callback_query']['data'];
        logger($request['message']);
        $user = RedBtnUser::query()->firstOrCreate(['tg_id' => $this->chat_id], ['step' => 0]);
        $question = RedBtnQuestion::query()->where('step', $user->step)->first();

        logger($user);
        logger($question);
        if (isset($request['callback_query']))
            logger($request['callback_query']);

        $inline_keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(["text" => "Кнопка Красная", 'callback_data' => 'Кнопка'])
            );

        $this->redBtnBot->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $question->text,
            'reply_markup' => $inline_keyboard
        ]);
        $user->step = $question->step + 1;
        $user->msg_id = $request['message']['message_id'] ?? $request['callback_query']['message']['message_id'];
        $user->save();

        return 200;
    }
    public function sendAllUsers()
    {
        $users = User::query()->whereNotNull('tg_id')->get();
        foreach ($users as $user)
            $this->telegram->sendMessage([
                'chat_id' => $user->tg_id,
                'text' => 'Всем привет! У нас вышло новое обновление, не теряй возможности его увидеть! Список нововведений:' . PHP_EOL .
                    '1) Обновлены все кнопки на более удобные.' . PHP_EOL .
                    '2) Добавлен календарь с выбором любой даты текущего семестра.',
            ]);
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

                if (stristr($this->text, '.')) {
                    if (Carbon::parse($this->text . '.2020'))
                        $this->timetableSend($this->text . '.2020');
                }
                if (stristr($this->text, ':')) {
                    $date = explode(':', $this->text);
                    $carb = Carbon::parse('2020-01-01');
                    if (isset($date[1])) {
                        for ($i = 0; $i < 12; $i++) {
                            if ($carb->copy()->addMonths($i)->monthName == $date[1]) {
                                $this->calendarMonth($carb->copy()->addMonths($i)->format('m'));
                                break;
                            }
                        }
                        return 200;
                    }
                }
                switch ($this->text) {
                    case 'Сегодня':
                        $this->timetableSend(1);
                        break;
                    case 'Завтра':
                        $this->timetableSend(2);
                        break;
                    case  'Назад':
                    case  'Календарь':
                        $this->calendar();
                        return 200;
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
            ->pluck('date')->toArray();

        $arr = [['Назад']];
        for ($i = 0; $i < 31; $i = $i + 3) {
            if (isset($timetables[$i]) and isset($timetables[$i + 1]) and isset($timetables[$i + 2])) {
                $arr[] = [
                    Carbon::parse($timetables[$i])->format('d.m'),
                    Carbon::parse($timetables[$i + 1])->format('d.m'),
                    Carbon::parse($timetables[$i + 2])->format('d.m'),
                ];
            } elseif (isset($timetables[$i]) and isset($timetables[$i + 1])) {
                $arr[] = [
                    Carbon::parse($timetables[$i])->format('d.m'),
                    Carbon::parse($timetables[$i + 1])->format('d.m'),
                ];
            } elseif (isset($timetables[$i])) {
                $arr[] = [Carbon::parse($timetables[$i])->format('d.m')];
            }
        }

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => 'Выберите день',
            'reply_markup' => $this->markup($arr),
        ]);
    }

    private function markup($buttons)
    {
        return json_encode(
            ['keyboard' =>
                $buttons,
                "one_time_keyboard" => TRUE,
                "resize_keyboard" => TRUE,
            ]
        );
    }

    public function showMenu($info = NULL)
    {
        $arr = [['Сегодня', 'Завтра'], ['Календарь']];

        $message = 'Необходимо выбрать день';
//        $message .= 'Также можно выбрать необходимую вам дату при помощи /date и через пробел дату: ' . PHP_EOL . '/date ' . now()->format('d.m.Y') . chr(10);

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $message,
            'reply_markup' => $this->markup($arr),
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
        $arr = [];

        foreach ($months as $month)
            $arr[] = ['Месяц:' . $month];

        $this->telegram->sendMessage([
            'chat_id' => $this->chat_id,
            'text' => 'Выберите месяц',
            'reply_markup' => $this->markup($arr),
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
