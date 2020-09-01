@extends('layouts.app')

@section('h1')
    <a href="{{route($routes['index'])}}">Группы</a> \ Создание группы
@endsection
@section('actions')
    <a href="{{route($routes['form'])}}" class="btn btn-success">Добавить</a>
@endsection
@section('content')
    <div class="container">
        <div class="col-md-6 offset-md-3 card card-body">
            <form action="{{route($routes['store'])}}" method="post">
                @csrf
                <input type="text" hidden name="id" value="{{$item->id}}">
                <div class="form-group">
                    <label for="name">Название группы: </label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{$item->name}}" id="name" name="name">
                </div>
                <div class="form-group">
                    <label for="course_id">Номер курса: </label>
                    <select name="course_id" id="course_id" class="form-control">
                        @foreach($courses as $course)
                            <option value="{{$course->id}}" @if($course->id == $item->course_id) selected @endif >{{$course->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="faculty_id">Факультет: </label>
                    <select name="faculty_id" id="faculty_id" class="form-control">
                        @foreach($faculties as $faculty)
                            <option value="{{$faculty->id}}" @if($faculty->id == $item->faculty_id) selected @endif >{{$faculty->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="user_id">Староста: </label>
                    <select name="user_id" id="user_id" class="form-control">
                        @foreach($users as $user)
                            <option value="{{$user->id}}" @if($user->id == $item->user_id) selected @endif >{{$user->name}}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-success btn-block">Сохранить</button>
            </form>
        </div>
    </div>
@endsection
