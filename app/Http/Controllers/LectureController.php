<?php

namespace App\Http\Controllers;

use App\Http\Requests\LectureFormRequest;
use App\Models\Group;
use App\Models\Lecture;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    private $views = [
        'index' => 'lectures.admin.index',
        'form' => 'lectures.admin.form',
    ];
    private $routes = [
        'index' => 'admin.lectures.index',
        'form' => 'admin.lectures.form',
        'store' => 'admin.lectures.store',
    ];

    public function index()
    {
        $items = Lecture::query()->get();

        $routes = $this->routes;
        return view($this->views['index'], compact('items', 'routes'));
    }

    public function form($id = FALSE)
    {
        $item = new Lecture();
        if ($id)
            $item = Lecture::query()->find($id);

        $groups = Group::query()->get();
        $routes = $this->routes;
        return view($this->views['form'], compact('item', 'routes'));
    }

    public function store(LectureFormRequest $request)
    {
        $id = $request->id;

        Lecture::query()->updateOrCreate([
            'id' => $id,
        ], [
            'name' => $request->name,
            'group_id' => $request->group_id
        ]);

        return redirect()->route($this->routes['index']);
    }
}
