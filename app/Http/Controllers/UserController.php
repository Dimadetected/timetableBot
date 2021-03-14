<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Group;
use App\Models\UsersType;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{

    private $views = [
        'index' => 'users.admin.index',
        'form' => 'users.admin.form',
    ];
    private $routes = [
        'index' => 'admin.users.index',
        'form' => 'admin.users.form',
        'store' => 'admin.users.store',
    ];

    public function index()
    {
        abort_if(auth()->user()->users_type_id != 1, 401);
        $items = User::query()->with(['users_type', 'group'])->get();
        $routes = $this->routes;
        return view($this->views['index'], compact('items', 'routes'));
    }

    public function form($id = FALSE)
    {
        $item = new User();
        if ($id)
            $item = User::query()->find($id);
        $usersTypes = UsersType::query()->get();
        $groups = Group::query()->get();
        $routes = $this->routes;
        return view($this->views['form'], compact('item', 'routes', 'usersTypes', 'groups'));
    }

    public function store(UserFormRequest $request)
    {
        $id = $request->id;
        User::query()->updateOrCreate([
            'id' => $id,
        ], [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt(123),
            'birthday' => Carbon::parse($request->birthday)->format('Y-m-d'),
            'users_type_id' => $request->users_type_id,
            'group_id' => ($request->group_id != 'null' ? $request->group_id : NULL),
        ]);

        return redirect()->route($this->routes['index']);
    }
}
