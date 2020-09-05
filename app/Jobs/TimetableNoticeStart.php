<?php

namespace App\Jobs;

use App\Models\Timetable;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\Api;

class TimetableNoticeStart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected static $telegram;
    protected $chat_id;
    protected $username;
    protected $text;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public static function handle()
    {
        $startMessage = 'Расписание на сегодня:';
        $date = Carbon::parse(\request('date', now()));
        $users = User::query()->whereNotNull('tg_id')->whereNotNull('group_id')->get();
        foreach ($users as $user) {

            $timetable = Timetable::query()
                ->where('date', 'LIKE', '%' . $date->toDateString() . '%')
                ->where('group_id', $user->group_id)
                ->first();
            if (isset($timetable->id)) {
                $message = PHP_EOL . '';
                $type = 'c';
                if ($date->weekOfYear % 2 == 0)
                    $type = 'z';
                foreach ($timetable->timetable as $times => $arr)
                    if (isset($arr[$type]))
                        $message .= $arr[$type]['time'] . ' | ' . $arr[$type]['lecture'] . ' | ' . $arr[$type]['teacher'] . PHP_EOL;
            } else {
                $message = 'Выходной';
            }
            $telegram = new Api(config('telegram.bots.mybot.token'));
            $telegram->sendMessage([
                        'chat_id' => $user->tg_id,
                        'text' => $startMessage . ' ' . $message,
                    ]);
        }

    }
}
