@extends('layouts.app')

@section('h1')
    <a href="{{route($routes['index'])}}">Пользователи</a> \ Создание пользователя
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
                    <label for="name">ФИО:</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{$item->name}}" id="name" name="name">
                </div>
                <div class="form-group">
                    <label for="email">Email: </label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" value="{{$item->email??time().'@kubsuBot.ru'}}" id="email" name="email" placeholder="Введите email">
                </div>
                <div class="form-group">
                    <label for="birthday">Дата рождения:</label>
                    <input type="text" class="form-control @error('birthday') is-invalid @enderror" value="{{$item->birthday??now()->format('d.m.Y')}}" id="birthday" name="birthday" placeholder="Введите email">
                </div>
                <div class="form-group">
                    <label for="users_type_id">Тип пользователя</label>
                    <select name="users_type_id" id="users_type_id" class="form-control">
                        @foreach($usersTypes as $usersType)
                            <option @if($usersType->id == $item->users_type_id) selected @endif value="{{$usersType->id}}">{{$usersType->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="group_id">Группа: </label>
                    <select name="group_id" id="group_id" class="form-control">
                        <option value="null">Нет</option>
                        @foreach($groups as $group)
                            <option @if($group->id == $item->group_id) selected @endif value="{{$group->id}}">{{$group->name}}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-success btn-block">Сохранить</button>
            </form>
        </div>
    </div>
@endsection
