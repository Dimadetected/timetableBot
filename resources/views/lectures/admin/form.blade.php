@extends('layouts.app')

@section('h1')
    <a href="{{route($routes['index'])}}">Предметы</a> \ Создание предмета
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
                <select name="group_id" id="">
                    @foreach($groups as $group)
                        <option @if(isset($item->group->id) and $group->id == $item->group->id) selected
                                @elseif(auth()->user()->group_id == $group->id) selected
                                @endif value="{{$group->id}}">{{$group->name}}</option>
                    @endforeach
                </select>
                <div class="form-group">
                    <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{$item->name}}"
                           id="name" name="name" placeholder="Введите название предмета">
                </div>
                <button class="btn btn-success btn-block">Сохранить</button>
            </form>
        </div>
    </div>
@endsection
