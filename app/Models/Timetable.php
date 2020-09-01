<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'type' => 'array'
    ];
    private $times = [
        '08:00-09:30' => 1,
        '09:40-11:10' => 2,
        '11:30-13:00' => 3,
        '13:10-14:40' => 4,
        '15:00-16:30' => 5,
        '16:40-18:10' => 6,
        '18:20-19:50' => 7,
    ];
    
    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }
    
    public function getDayOfWeekTextAttribute()
    {
        $daysOfWeek = [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            0 => 'Воскресенье',
        ];
        return $daysOfWeek[$this->dayOfWeek];
    }
    
    
    public function getTimetableAttribute()
    {
        $lectures_ids_c = $this->type['c']['lectures'];
        $teachers_ids_c = $this->type['c']['teachers'];
        $times_c = $this->type['c']['times'];
        
        $lectures_c = Lecture::query()->whereIn('id', $lectures_ids_c)->pluck('name', 'id')->toArray();
        $teachers_c = User::query()->whereIn('id', $teachers_ids_c)->pluck('name', 'id')->toArray();
        
        $lectures_ids_z = $this->type['z']['lectures'];
        $teachers_ids_z = $this->type['z']['teachers'];
        $times_z = $this->type['z']['times'];
        
        $lectures_z = Lecture::query()->whereIn('id', $lectures_ids_z)->pluck('name', 'id')->toArray();
        $teachers_z = User::query()->whereIn('id', $teachers_ids_z)->pluck('name', 'id')->toArray();
        
        $arr = [];
        for ($i = 0; $i < 6; $i++) {
            if (isset($times_c[$i]) and isset($lectures_ids_c[$i]) and isset($lectures_c[$lectures_ids_c[$i]])) {
                $arr[$this->times[$times_c[$i]]]['c']['time'] = $times_c[$i];
                $arr[$this->times[$times_c[$i]]]['c']['teacher'] = $teachers_c[$teachers_ids_c[$i]];
                $arr[$this->times[$times_c[$i]]]['c']['lecture'] = $lectures_c[$lectures_ids_c[$i]];
            }
            
            if (isset($times_z[$i]) and isset($lectures_ids_z[$i]) and isset($lectures_z[$lectures_ids_z[$i]])) {
                $arr[$this->times[$times_z[$i]]]['z']['time'] = $times_z[$i];
                $arr[$this->times[$times_z[$i]]]['z']['lecture'] = $lectures_z[$lectures_ids_z[$i]];
                $arr[$this->times[$times_z[$i]]]['z']['teacher'] = $teachers_z[$teachers_ids_z[$i]];
            }
        }
        ksort($arr);
        return $arr;
    }
}
