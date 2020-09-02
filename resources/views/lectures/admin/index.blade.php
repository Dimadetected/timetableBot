@extends('layouts.app')

@section('h1')
    Предметы
@endsection
@section('actions')
    <a href="{{route($routes['form'])}}" class="btn btn-success">Добавить</a>
@endsection
@section('content')
    <div class="container">
        <table class="table">
            <thead>
            <tr>
                <th>Название</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td class="text-right"><a href="{{route($routes['form'],$item->id)}}" class="btn btn-primary">Редактировать</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    
    </div>
@endsection
