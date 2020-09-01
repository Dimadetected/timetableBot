<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersTypeFormRequest;
use App\Models\UsersType;

class UsersTypeController extends Controller
{
    private $views = [
        'index' => 'usersType.admin.index',
        'form' => 'usersType.admin.form',
    ];
    private $routes = [
        'index' => 'admin.usersType.index',
        'form' => 'admin.usersType.form',
        'store' => 'admin.usersType.store',
    ];
    
    public function index()
    {
        $items = UsersType::query()->get();
        
        $routes = $this->routes;
        return view($this->views['index'], compact('items', 'routes'));
    }
    
    public function form($id = FALSE)
    {
        $item = new UsersType();
        if ($id)
            $item = UsersType::query()->find($id);
        
        $routes = $this->routes;
        return view($this->views['form'], compact('item', 'routes'));
    }
    
    public function store(UsersTypeFormRequest $request)
    {
        $id = $request->id;
    
        UsersType::query()->updateOrCreate([
            'id' => $id,
        ], [
            'name' => $request->name,
        ]);
        
        return redirect()->route($this->routes['index']);
    }
}
