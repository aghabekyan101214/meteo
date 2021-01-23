@extends('layouts.app')

@section('content')
    <style>
        .daterangepicker{
            right: auto!important;
        }
    </style>
    <div class="white-box ">
        <div class="row">
            <div class="col-md-12" style="text-align: right">
                <a href="/bi">
                    <button class="btn btn-success"><i class="fa fa-recycle"></i></button>
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table id="myTable" class="table table-striped">
                <thead>
                <tr>
                    <th style="width: 300px">Дата</th>
                    <th>Направление/Скорось Ветра</th>
                    <th>Давление в гПа</th>
                    <th>Ampamacutyun</th>
                    <th>отню влажность</th>
                    <th>Температура/Цифра кода количества облаков нижнего яруса</th>
                    <th>Макс. порыв ветра/грозы</th>
                    <th>Օդերևութաբանական երևոյթներ</th>
                    <th>Номер явления/шторм/рабочий курс</th>
                    <th>Высота облаков</th>
                    <th>Давление в мм.рт.ст</th>
                    <th>Дальность вид. на ВПП по рабочему курсу</th>
                    <th>Дальность вид. на ВПП середины</th>
                    <th>Дальность вид. на нерабочему курсу</th>
                    <th>Мин. метеорологическая дальность видимости</th>
                    <th>Перпендикулярная составляющая скорости ветра к ВПП/гололед</th>
                    <th>Значения Давления</th>
                    <th>Погода</th>
                </tr>
                <tr>
                    @for($i = 0; $i <= $columns + 2; $i++)
                        @if($i == 0)
                            <td>
                                <input type="text" autocomplete="off" name="datefilter" class="form-control date" value="{{ !is_null($request->from) ? ($request->from . " - " . $request->to) : '' }}"/>
                            </td>
                        @else
                            <td>
                                @if($i == $columns)
                                    <div class="input-group">
                                        <div style="display: flex">
                                            <input type="number" class="form-control q" name="{{ "col$i" }}" value="{{ $request->{"col$i"} }}">
                                            <button class="btn btn-deafult" onclick="search()" style="margin-left: 10px;"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                @else
                                    <input type="number" class="form-control q" name="{{ "col$i" }}" value="{{ $request->{"col$i"} }}">
                                @endif
                            </td>
                        @endif
                    @endfor
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key => $value)
                    <tr>
                        <td>{{ $value->col0 }}</td>
                        <td>
                            @php
                                $splCol1 = str_split($value->col1);
                                echo ($splCol1[0] != 0 ? $splCol1[0] : '') . ($splCol1[1] != 0 ? $splCol1[1] : '') .  $splCol1[2] . '°' . ' / ' . $splCol1[3] . 'мс'
                            @endphp
                        </td>
                        <td>{{ $value->col2 }}</td>
                        <td>
                            @php
                                $splCol3 = str_split($value->col3);
                                echo $splCol3[0];
                            @endphp
                        </td>
                        <td>{{ $splCol3[2] . $splCol3[3] . '%' }}</td>
                        <td>
                            @php
                                $splCol4 = str_split($value->col4);
                                echo ($splCol4[0] != 0 ? $splCol4[0] : '')  . ($splCol4[0] == 0 ? ($splCol4[1] == 0 ? '' : $splCol4[1]) : $splCol4[1] ) . $splCol3[2] . '°C' . ' / ' . $splCol4[3] ;
                            @endphp
                        </td>
                        <td>
                            @php
                                $splCol5 = str_split($value->col5);
                                echo ($splCol5[0] != 0 ? $splCol5[0] : '') . $splCol5[1] . 'мс' . ' / ' . $splCol5[2];
                            @endphp
                        </td>
                        <td>
                            @php
                                $splCol6 = str_split($value->col6);
                                echo $splCol6[0];
                            @endphp
                        </td>
                        <td>{{ $splCol6[1] . ' / ' . $splCol6[2] }}</td>
                        <td>
                            @if($value->col7 != '---')
                                @php
                                    $splCol7 = str_split($value->col7);
                                    echo $value->col7 * 10 . 'м';
                                @endphp
                            @else
                                {{ $value->col7 }}
                            @endif
                        </td>
                        <td>{{ $value->col8 }}</td>
                        <td>{{ $value->col9 * 10 . 'м' }}</td>
                        <td>{{ $value->col10 * 10 . 'м' }}</td>
                        <td>{{ $value->col11 . 0 }}</td>
                        <td>{{ $value->col12 * 10 . 'м' }}</td>
                        <td>
                            @php
                                $splCol13 = str_split($value->col13);
                                echo ($splCol13[0] . $splCol13[1]) * 1 . 'мс' . ' / ' . $splCol13[2];
                            @endphp
                        </td>
                        <td>{{ $value->col14 }}</td>
                        <td>{{ $value->col15 }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $data->appends(request()->except('page'))->links() }}
        </div>
    </div>
    <script>
        $(function() {
            $('input[name="datefilter"]').daterangepicker({
                opens: 'left',
                timePicker: true,
                autoUpdateInput: false,

                locale: {
                    format: 'Y-MM-D H:m:s'
                }
            });

            $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('Y-MM-D H:m:s') + ' - ' + picker.endDate.format('Y-MM-D H:m:s'));
            });

            $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });

        function search() {
            let query = "";
            let search = $(".date").val().split(" - ");
            let url = location.href.split("?")[0];
            var urlParams = new URLSearchParams(window.location.search);
            $(".q").each(function(e, i){
                // if(!e) query += "?";
                // query += $(i).val() ? ($(i).attr("name") + "=" + $(i).val()) : '';
                if(!$(i).val()) {
                    urlParams.delete($(i).attr("name"));
                    return;
                }
                if(!urlParams.has($(i).attr("name"))) {
                    urlParams.append($(i).attr("name"), $(i).val());
                } else {
                    urlParams.set($(i).attr("name"), $(i).val());
                }
            });
            if(!urlParams.has("from")) {
                urlParams.append('from', search[0] || '');
                urlParams.append('to', search[1] || '');
            } else {
                urlParams.set('from', search[0] || '');
                urlParams.set('to', search[1] || '');
            }
            urlParams.delete('page')
            let params = urlParams.toString();
            location.href = url + "?" + params;
        }

    </script>
@endsection

