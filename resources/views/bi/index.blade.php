@extends('layouts.app')

@section('content')
    <style>
        .daterangepicker{
            right: auto!important;
        }
    </style>
    <div class="white-box">
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
                </tr>
                <tr>
                    @for($i = 0; $i <= $columns; $i++)
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
                        @for($i = 0; $i <= $columns; $i++)
                            <td>{{ $value->{'col'.$i} }}</td>
                        @endfor
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

