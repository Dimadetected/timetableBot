<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacultyFormRequest;
use App\Http\Requests\GroupFormRequest;
use App\Models\Faculty;
use App\Models\Group;
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
    
    public function index()
    {
        $items = Group::query()->get();
        
        $routes = $this->routes;
        return view($this->views['index'], compact('items', 'routes'));
    }
    
    public function form($id = FALSE)
    {
        $users = User::query()->get();
        $item = new Group();
        if ($id)
            $item = Group::query()->find($id);
        
        $routes = $this->routes;
        return view($this->views['form'], compact('item', 'routes','users'));
    }
    
    public function store(GroupFormRequest $request)
    {
        $id = $request->id;
        
        Group::query()->updateOrCreate([
            'id' => $id,
        ], [
            'name' => $request->name,
            'user_id' => $request->user_id
        ]);
        
        return redirect()->route($this->routes['index']);
    }
}
