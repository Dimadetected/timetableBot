@extends('layouts.app')

@section('h1')
    Шаблоны
@endsection
@section('actions')
    <a href="{{route($routes['form'])}}" class="btn btn-success">Добавить</a>
@endsection
@section('content')
    <div class="container">
        <table class="table ">
            <thead>
            <tr>
                <th>День недели</th>
                <th>Группа</th>
                <td></td>
                <td></td>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr class="text-center">
                    <td>{{$item->dayOfWeekText}}</td>
                    <td>{{$item->group->name}}
                    </td>
                    <td>
                        <table class="table table-bordered">
                            @foreach($item->timetableChisl as $i=>$arr)
                                <tr>
                                    <td>{{$arr['z']['time']??$arr['c']['time']}}</td>
                                    @if(isset($arr['z']['time']) and  isset($arr['c']['time']))
                                        @if($arr['z']['lecture'] == isset($arr['c']['lecture']))
                                            <td>{{$arr['z']['lecture']}}</td>
                                            <td>{{$arr['z']['teacher']}}</td>
                                        @else
                                            <td>
                                                {{$arr['c']['lecture']}}
                                                <hr>
                                                {{$arr['z']['lecture']}}
                                            </td>
                                            <td>
                                                {{$arr['z']['teacher']}}
                                                <hr>
                                                {{$arr['c']['teacher']}}
                                            </td>
                                        @endif
                                    @else
                                        @if(isset($arr['c']['lecture']))
                                            <td>
                                                {{$arr['c']['lecture']}}
                                                <hr>
                                                Пусто
                                            </td>
                                            <td>
                                                {{$arr['c']['teacher']}}
                                                <hr>
                                                Пусто
                                            </td>
                                        @elseif(isset($arr['z']['lecture']))
                                            <td>
                                                Пусто
                                                <hr>
                                                {{$arr['z']['lecture']}}
                                            </td>
                                            <td>
                                                Пусто
                                                <hr>
                                                {{$arr['z']['teacher']}}
                                            </td>
                                        @endif
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                    </td>
                    <td class="text-right"><a href="{{route($routes['form'],$item->id)}}" class="btn btn-primary">Редактировать</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    
    </div>
@endsection
