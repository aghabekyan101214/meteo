@extends('layouts.app')

@section('content')

    <div class="white-box">
        <div class="table-responsive">
            <table id="myTable" class="table table-striped">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Время</th>
                    <th>Направление/Скорось Ветра</th>
                    <th>Давление в гПа</th>
                    <th>Цифра кода общего количества облаков / отню влажность</th>
                    <th>Температура/Цифра кода количества облаков нижнего яруса</th>
                    <th>Макс. порыв ветра/грозы</th>
                    <th>Номер явления/шторм/рабочий курс</th>
                    <th>Высота облаков</th>
                    <th>Давление в мм.рт.ст</th>
                    <th>Дальность вид. на ВПП по рабочему курсу</th>
                    <th>Дальность вид. на ВПП середины</th>
                    <th>Дальность вид. на нерабочему курсу</th>
                    <th>Мин. метеорологическая дальность видимости</th>
                    <th>Перпендикулярная составляющая скорости ветра к ВПП/гололед</th>
                    <th>Время суток</th>
                    <th>Ступень огней высокой интенсивности</th>
                    <th>Значение МОД, округление до 10 м <sub>2</sub></th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $value)
                    <tr>
                        <td>{{ $value->col0 }}</td>
                        <td>{{ $value->col1 }}</td>
                        <td>{{ $value->col2 }}</td>
                        <td>{{ $value->col3 }}</td>
                        <td>{{ $value->col4 }}</td>
                        <td>{{ $value->col5 }}</td>
                        <td>{{ $value->col6 }}</td>
                        <td>{{ $value->col7 }}</td>
                        <td>{{ $value->col8 }}</td>
                        <td>{{ $value->col9 }}</td>
                        <td>{{ $value->col10 }}</td>
                        <td>{{ $value->col11 }}</td>
                        <td>{{ $value->col12 }}</td>
                        <td>{{ $value->col13 }}</td>
                        <td>{{ $value->col14 }}</td>
                        <td>{{ $value->col15 }}</td>
                        <td>{{ $value->col16 }}</td>
                        <td>{{ $value->col17 }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $data->links() }}
        </div>
    </div>
@endsection

