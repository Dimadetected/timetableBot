<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacultyFormRequest;
use App\Http\Requests\GroupFormRequest;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\UsersType;
use App\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    private $views = [
        'index' => 'groups.admin.index',
        'form' => 'groups.admin.form',
    ];
    private $routes = [
        'index' => 'admin.groups.index',
        'form' => 'admin.groups.form',
        'store' => 'admin.groups.store',
    ];
    private $usesTypeId = 1;
    public function index()
    {
        abort_if(auth()->user()->id != 23,401);
        $items = Group::query()->get();

        $routes = $this->routes;
        return view($this->views['index'], compact('items', 'routes'));
    }

    public function form($id = FALSE)
    {
        $usersType = UsersType::query()->find($this->usesTypeId);
        $users = User::query()->where('users_type_id',$usersType->id)->get();

        $item = new Group();
        if ($id)
            $item = Group::query()->find($id);

        $courses = Course::query()->get();
        $faculties = Faculty::query()->get();

        $routes = $this->routes;
        return view($this->views['form'], compact('item', 'routes','users','courses','faculties'));
    }

    public function store(GroupFormRequest $request)
    {
        $id = $request->id;

        Group::query()->updateOrCreate([
            'id' => $id,
        ], [
            'name' => $request->name,
            'user_id' => $request->user_id,
            'course_id' => $request->course_id,
            'faculty_id' => $request->faculty_id,
        ]);

        return redirect()->route($this->routes['index']);
    }
}
