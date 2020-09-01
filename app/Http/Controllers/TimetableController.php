<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimetableFormRequest;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Timetable;
use App\Models\UsersType;
use App\User;
use Carbon\Carbon;
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
        $items = Timetable::query()->get();
        
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
            1=>'Понедельник',
            2=>'Вторник',
            3=>'Среда',
            4=>'Четверг',
            5=>'Пятница',
            6=>'Суббота',
            0=>'Воскресенье',
        ];
        
        $teachers = User::query()->where('users_type_id',$usersType->id)->get();
        $groups = Group::query()->get();
        $faculties = Faculty::query()->get();
        $courses = Course::query()->get();
    
        Carbon::setLocale('ru');
        $routes = $this->routes;
        return view($this->views['form'], compact('item', 'routes','teachers','faculties','groups','courses','daysOfWeek'));
    }
    
    public function store(TimetableFormRequest $request)
    {
        $id = $request->id;
        
        Timetable::query()->updateOrCreate([
            'id' => $id,
        ], [
            'name' => $request->name,
        ]);
        
        return redirect()->route($this->routes['index']);
    }
}
