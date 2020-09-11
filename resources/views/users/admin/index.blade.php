@extends('layouts.app')

@section('h1')
    Пользователи
@endsection
@section('actions')
    <a href="{{route($routes['form'])}}" class="btn btn-success">Добавить</a>
@endsection
@section('content')
    <div class="container">
        <table class="table">
            <thead>
            <tr>
                <th>Имя</th>
                <th>Тип</th>
                <th>Факультет</th>
                <th>Группа</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td>{{$item->users_type->name??''}}</td>

                    <td>{{$item->group->faculty->name??''}}</td>
                    <td>{{$item->group->name??''}}</td>
                    <td class="text-right"><a href="{{route($routes['form'],$item->id)}}" class="btn btn-primary">Редактировать</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endsection
