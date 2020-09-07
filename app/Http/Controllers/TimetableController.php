<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateFormRequest;
use App\Http\Requests\TimetableFormRequest;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Lecture;
use App\Models\Template;
use App\Models\Timetable;
use App\Models\UsersType;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    private $views = [
        'index' => 'timetable.admin.index',
        'form' => 'timetable.admin.form',
    ];
    private $routes = [
        'index' => 'admin.timetable.index',
        'form' => 'admin.timetable.form',
        'store' => 'admin.timetable.store',
    ];

    private $teacherTypeId = 2;

    public function index()
    {
        abort_if(!auth()->check(),403,'Доступ запрещен');
        $user= auth()->user();
        $items = Timetable::query()->where('group_id',$user->group_id)->where('date','>=',now()->format('Y-m-d 00:00:00'))->orderBy('date','asc')->get();
        $routes = $this->routes;
        return view($this->views['index'], compact('items', 'routes'));
    }

    public function form($id = FALSE)
    {
        $item = new Timetable();
        if ($id)
            $item = Timetable::query()->find($id);

        $usersType = UsersType::query()->find($this->teacherTypeId);

        $daysOfWeek = [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            0 => 'Воскресенье',
        ];

        $timesLectures = [
            1 => [
                'start' => '08:00',
                'end' => '09:30',
            ],
            2 => [
                'start' => '09:40',
                'end' => '11:10',
            ],
            3 => [
                'start' => '11:30',
                'end' => '13:00',
            ],
            4 => [
                'start' => '13:10',
                'end' => '14:40',
            ],
            5 => [
                'start' => '15:00',
                'end' => '16:30',
            ],
            6 => [
                'start' => '16:40',
                'end' => '18:10',
            ],
            7 => [
                'start' => '18:20',
                'end' => '19:50',
            ],
        ];

        $teachers = User::query()->where('users_type_id', $usersType->id)->get();
        $groups = Group::query()->get();
        $faculties = Faculty::query()->get();
        $courses = Course::query()->get();
        $lectures = Lecture::query()->get();

        Carbon::setLocale('ru');
        $routes = $this->routes;
        return view($this->views['form'], compact('item', 'routes', 'teachers', 'faculties', 'groups', 'courses', 'daysOfWeek', 'lectures', 'timesLectures'));
    }

    public function store(TimetableFormRequest $request)
    {
        $id = $request->id;

        $type = [
            'c' => [
                'times' => [],
                'teachers' => [],
                'lectures' => [],
            ],
            'z' => [
                'times' => [],
                'teachers' => [],
                'lectures' => [],
            ],
        ];
        for ($i = 0; $i < 5; $i++) {
            if ($request->times_c[$i] != 'null')
                $type['c']['times'][] = $request->times_c[$i];
            if ($request->teacher_id_c[$i] != 'null')
                $type['c']['teachers'][] = $request->teacher_id_c[$i];
            if ($request->lecture_id_c[$i] != 'null')
                $type['c']['lectures'][] = $request->lecture_id_c[$i];

            if ($request->times_z[$i] != 'null')
                $type['z']['times'][] = $request->times_z[$i];
            if ($request->teacher_id_z[$i] != 'null')
                $type['z']['teachers'][] = $request->teacher_id_z[$i];
            if ($request->lecture_id_z[$i] != 'null')
                $type['z']['lectures'][] = $request->lecture_id_z[$i];
        }
        Timetable::query()->updateOrCreate([
            'id' => $id,
        ], [
            'group_id' => $request->group_id,
            'dayOfWeek' => $request->dayOfWeek,
            'type' => $type,
            'online' => ($request->online !='null'?$request->online:1)
        ]);

        return redirect()->route($this->routes['index']);
    }



}
