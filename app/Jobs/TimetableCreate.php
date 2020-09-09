<?php

namespace App\Jobs;

use App\Models\Template;
use App\Models\Timetable;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TimetableCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public static function handle()
    {
        $period = CarbonPeriod::create(now(), now()->addMonths(3));
        $templates = Template::query()->get();

        foreach ($templates as $template) {
            foreach ($period as $date) {
                if ($date->dayOfWeek == $template->dayOfWeek) {
                    $timetable = Timetable::query()->updateOrCreate([
                        'date' => $date->copy()->format('Y-m-d 00:00:00'),
                        'group_id' => $template->group_id,
                        'dayOfWeek' => $template->dayOfWeek
                    ], [
                        'type' => $template->type,
                    ]);
                }
            }
        }
    }
}
