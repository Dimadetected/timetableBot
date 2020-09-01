<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseFormRequest;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    
    private $views = [
        'index' => 'courses.admin.index',
        'form' => 'courses.admin.form',
    ];
    private $routes = [
        'index' => 'admin.courses.index',
        'form' => 'admin.courses.form',
        'store' => 'admin.courses.store',
    ];
    
    public function index()
    {
        $items = Course::query()->get();
        
        $routes = $this->routes;
        return view($this->views['index'], compact('items', 'routes'));
    }
    
    public function form($id = FALSE)
    {
        $item = new Course();
        if ($id)
            $item = Course::query()->find($id);
        
        $routes = $this->routes;
        return view($this->views['form'], compact('item', 'routes'));
    }
    
    public function store(CourseFormRequest $request)
    {
        $id = $request->id;
        
        Course::query()->updateOrCreate([
            'id' => $id,
        ], [
            'name' => $request->name,
        ]);
        
        return redirect()->route($this->routes['index']);
    }
}
