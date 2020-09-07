@extends('layouts.app')

@section('h1')
    Расписание
@endsection
@section('actions')
    <a href="{{route($routes['form'])}}" class="btn btn-success">Добавить</a>
@endsection
@section('content')
    <style>
        td {
            padding: 0 !important;
        }
    </style>
    <div class="col-12">
        <div class="row">
            @foreach($items as $item)
                <div class="col-md-4 offset-md-4">
                    <div class="h4 text-center">
                        {{\Carbon\Carbon::parse($item->date)->format('d.m.Y')}}
                    </div>
                    <div class="text-center d-md-none">
                        <hr>
                        @php $printArr = $item->timetable ; $j = 0 @endphp
                        @foreach( $printArr as $i=>$arr)
                            @php $j++ @endphp
                            <div class="  ">
                                <div class="h3 text-success">{{$arr['z']['time']??$arr['c']['time']}}</div>
                                @if(isset($arr['z']['time']) and  isset($arr['c']['time']))
                                    @if($arr['z']['lecture'] == ($arr['c']['lecture']))
                                        <span>{{$arr['z']['lecture']}}</span>
                                        <span>{{$arr['z']['teacher']}}</span><br>
                                    @else
                                        <span>
                                                {{$arr['c']['lecture']}}
                                            {{$arr['c']['teacher']}}<br>
                                               <br>
                                            </span>
                                        <span>
                                                {{$arr['z']['teacher']}}
                                            {{$arr['z']['lecture']}}
                                            
                                            </span>
                                    @endif
                                @else
                                    @if(isset($arr['c']['lecture']))
                                        <span style="border-bottom: 1px solid gray">
                                                {{$arr['c']['lecture']}} {{$arr['c']['teacher']}} <br>
                                            </span>
                                        <span >
                                                Chill Time
                                            </span>
                                    @elseif(isset($arr['z']['lecture']))
                                        <span style="border-bottom: 1px solid gray">
                                                Chill Time<br>
                                                
                                            </span>
                                        <span>
                                                {{$arr['z']['lecture']}}
                                            {{$arr['z']['teacher']}}
                                            </span>
                                    @endif
                                @endif
                            </div>
                            <hr>
                        @endforeach
                        <br>
                    </div>
                    <table class="table table-bordered d-none d-md-table">
                        @php $printArr = $item->timetable ; $j = 0 @endphp
                        @foreach( $printArr as $i=>$arr)
                            @php $j++ @endphp
                            <tr class="  text-center @if($j % 2 != 1) table-primary @endif ">
                                <td class="">{{$arr['z']['time']??$arr['c']['time']}}</td>
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
                </div>
            @endforeach
        </div>
    </div>
@endsection
