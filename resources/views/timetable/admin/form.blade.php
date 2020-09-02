@extends('layouts.app')

@section('h1')
    <a href="{{route($routes['index'])}}">Расписание</a> \ Создание расписания
@endsection
@section('actions')
    <a href="{{route($routes['form'])}}" class="btn btn-success">Добавить</a>
@endsection
@section('content')
    <div class="col-12">
        <div class="col-md-6 offset-md-3 ">
            <form action="{{route($routes['store'])}}" method="post">
                <div class="card card-body">
                    <div class="form-group my-3">
                        <label for="group_id">Номер группы:</label>
                        <select name="group_id" id="group_id" class="form-control">
                            @foreach($groups as $group)
                                <option @if($group->id == $item->group_id) selected @endif value="{{$group->id}}">{{$group->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group my-3">
                        <label for="dayOfWeek">День недели: </label>
                        <select name="dayOfWeek" id="dayOfWeek" class="form-control">
                            @foreach($daysOfWeek as $dayOfWeek =>$nameDay)
                                <option @if($dayOfWeek === $item->dayOfWeek) selected @endif value="{{$dayOfWeek}}">{{$nameDay}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group my-3">
                        <label for="online">Тип: </label>
                        <select name="online" id="online" class="form-control">
                            <option value="null">Очно</option>
                            <option  @if($item->online == 1) selected @endif value="1">Дистанционно</option>
                        </select>
                    </div>
                </div>
                @csrf
                <div class="row mt-4 ">
                    <div class="btn-group col-12 " role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-primary allBtn" id="chisl">Числитель</button>
                        <button type="button" class="btn btn-secondary allBtn" id="znam">Знаменатель</button>
                    </div>
                </div>
                <div class="card card-body chisl allCZ">
                    <div class="from-group">
                        @for($i=0;$i<6;$i++)
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="times_c[]">Время:</label>
                                    <select name="times_c[]" id="times_c[]" class="form-control">
                                        <option value="null">Пусто</option>
                                        @foreach($timesLectures as $timeLectures =>$times)
                                            <option @if(isset($item->type['c']['times'][$i]) and $times['start'].'-' . $times['end']  == $item->type['c']['times'][$i]) selected @endif value="{{$times['start'].'-' . $times['end']}}">{{$times['start']}} - {{$times['end']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="teacher_id_c[]">Преподаватель:</label>
                                    <select name="teacher_id_c[]" id="teacher_id_c[]" class="form-control">
                                        <option value="null">Пусто</option>
                                        @foreach($teachers as $teacher)
                                            <option @if(isset($item->type['c']['teachers'][$i]) and $teacher->id == $item->type['c']['teachers'][$i]) selected @endif value="{{$teacher->id}}">{{$teacher->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="lecture_id_c[]">Пары:</label>
                                    <select name="lecture_id_c[]" id="lecture_id_c[]" class="form-control ">
                                        <option value="null">Пусто</option>
                                        @foreach($lectures as $lecture)
                                            <option @if(isset($item->type['c']['lectures'][$i]) and $lecture->id == $item->type['c']['lectures'][$i]) selected @endif value="{{$lecture->id}}">{{$lecture->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                        @endfor
                    </div>
                
                </div>
                
                
                <div class="card card-body znam d-none allCZ">
                    <div class="form-group">
                        @for($i=0;$i<6;$i++)
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="times_z[]">Время:</label>
                                    <select name="times_z[]" id="times_z[]" class="form-control">
                                        <option value="null">Пусто</option>
                                        @foreach($timesLectures as $timeLectures =>$times)
                                            <option @if(isset($item->type['z']['times'][$i]) and $times['start'].'-' . $times['end']  == $item->type['z']['times'][$i]) selected @endif value="{{$times['start'].'-' . $times['end']}}">{{$times['start']}} - {{$times['end']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="teacher_id_z[]">Преподаватель:</label>
                                    <select name="teacher_id_z[]" id="teacher_id_z[]" class="form-control">
                                        <option value="null">Пусто</option>
                                        @foreach($teachers as $teacher)
                                            <option @if(isset($item->type['z']['teachers'][$i]) and $teacher->id == $item->type['z']['teachers'][$i]) selected @endif value="{{$teacher->id}}">{{$teacher->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="lecture_id_z[]">Пары:</label>
                                    <select name="lecture_id_z[]" id="lecture_id_z[]" class="form-control ">
                                        <option value="null">Пусто</option>
                                        @foreach($lectures as $lecture)
                                            <option @if(isset($item->type['z']['lectures'][$i]) and $lecture->id == $item->type['z']['lectures'][$i]) selected @endif value="{{$lecture->id}}">{{$lecture->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                        @endfor
                    </div>
                </div>
                <input type="text" hidden name="id" value="{{$item->id}}">
                <button class="btn btn-success btn-block mt-3">Сохранить</button>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
		$(document).on('click', '.allBtn', function () {
			console.log($(this).prop('id'));
			$('.allBtn').removeClass('btn-primary');
			$('.allBtn').removeClass('btn-secondary');
			$('.allBtn').addClass('btn-secondary');
			$('.allCZ').removeClass('d-none');
			$('.allCZ').addClass('d-none');
			$('.' + $(this).prop('id')).removeClass('d-none');
			$(this).removeClass('btn-secondary');
			$(this).addClass('btn-primary');
		})
    </script>
@endsection
