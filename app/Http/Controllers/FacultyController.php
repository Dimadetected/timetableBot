<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacultyFormRequest;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    private $views = [
        'index' => 'faculties.admin.index',
        'form' => 'faculties.admin.form',
    ];
    private $routes = [
        'index' => 'admin.faculties.index',
        'form' => 'admin.faculties.form',
        'store' => 'admin.faculties.store',
    ];
    
    public function index()
    {
        $items = Faculty::query()->get();
        
        $routes = $this->routes;
        return view($this->views['index'], compact('items', 'routes'));
    }
    
    public function form($id = FALSE)
    {
        $item = new Faculty();
        if ($id)
            $item = Faculty::query()->find($id);
        
        $routes = $this->routes;
        return view($this->views['form'], compact('item', 'routes'));
    }
    
    public function store(FacultyFormRequest $request)
    {
        $id = $request->id;
        
        Faculty::query()->updateOrCreate([
            'id' => $id,
        ], [
            'name' => $request->name,
        ]);
        
        return redirect()->route($this->routes['index']);
    }
}
