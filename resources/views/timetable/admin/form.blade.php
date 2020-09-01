@extends('layouts.app')

@section('h1')
    <a href="{{route($routes['index'])}}">Расписание</a> \ Создание расписания
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
                <button class="btn btn-success btn-block">Сохранить</button>
            </form>
        </div>
    </div>
@endsection
