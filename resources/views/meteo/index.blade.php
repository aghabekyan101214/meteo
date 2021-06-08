@extends('layouts.app')

@section('content')
    <style>
        #myTable th{
            font-size: 13px;
        }
        input {
            border: 1px solid black!important;
        }
        input {
            color: black!important;
        }

        @media print {
            table, th, td
            {
                border-collapse:collapse;
                border: 1px solid black;
                width:100%;
                text-align:right;
            }
            .table-responsive{
                width: 100%;
                position: fixed;
                height: 100%;
                top: 0;
                left: 0;
                z-index: 999999999999999;
                background: white;
            }
            .input-tr{
                display: none;
            }
            .pagination{
                display: none;
            }
        }
    </style>
    <div class="white-box">
        <div class="row">
            <div class="col-md-12" style="text-align: right; margin-bottom: 20px;">
                <button class="btn btn-primary m-r-5" onclick="search()" data-toggle="tooltip" data-placement="top" title="Search"><i class="fa fa-search"></i></button>
                <a href="/" data-toggle="tooltip" data-placement="top" title="Refresh the page">
                    <button class="btn btn-success"><i class="fa fa-recycle"></i></button>
                </a>
                <button class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Print the table" onclick="printTable()">
                    <i class="fa fa-print"></i>
                </button>
                <form action="/export-to-excel" target="_blank" method="post" id="export-form" style="display: inline-block">
                    @csrf
                    <button class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Print the table">
                        Ներբեռնել Excel Տարբերակով
                    </button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table id="myTable" class="table table-striped table-bordered" border="1" cellpadding="3">
                <thead>
                <tr>
                    <th>Ամսաթիվ</th>
                    <th>Խոնավություն (տոկոս %)</th>
                    <th>Ջերմաստիճան (ցելսիուս)</th>
                    <th>Քամու արգ. 09 (մ/վրկ)</th>
                    <th>Քամու ուղղ. 09 (աստիճան)</th>
                    <th>Քամու արգ. 27 (մ/վրկ)</th>
                    <th>Քամու ուղղ. 27 (աստիճան)</th>
                    <th>Ճնշում (հպա)</th>
                    <th>Տեսանելիություն 09 (մետր)</th>
                    <th>Տեսանելիություն сер. (մետր)</th>
                    <th>Տեսանելիություն 27 (մետր)</th>
                    <th>Ցողի կետ (ջերմաստիճան)</th>
                    <th>Շփման գործակից</th>
                    <th>Ամպերի ներքին սահմանի բարձր. (մետր)</th>
                    <th>Ամպամածության տեսակը և քանակը</th>
                </tr>
                <tr>
                    <th>Date</th>
                    <th>Humidity (percent %)</th>
                    <th>Temperature (celsius)</th>
                    <th>Wind speed 09 (m/s)</th>
                    <th>Wind direction 09 (degree)</th>
                    <th>Wind speed 27 (m/s)</th>
                    <th>Wind direction 27 (degree)</th>
                    <th>Pressure (GPA)</th>
                    <th>Visibility 09 (metres)</th>
                    <th>Visibility mid. (metres)</th>
                    <th>Visibility 27 (metres)</th>
                    <th>Dew point (temperature)</th>
                    <th>Contact coefficient</th>
                    <th>Inner border of clouds (metres)</th>
                    <th>Cloudiness type and amount</th>
                </tr>
                <tr class="input-tr">
                    <th><input autocomplete="off" type="text" placeholder="Ամսաթիվ" class="form-control date" name="created_at" value="{{ !is_null($request->from) ? ($request->from . " - " . $request->to) : '' }}"></th>
                    <th><input type="text" name="wet" value="{{ $request->wet ?? '' }}" class="form-control search-inp" placeholder="Խոնավություն"></th>
                    <th><input type="text" name="temperature" value="{{ $request->temperature ?? '' }}" class="form-control search-inp" placeholder="Ջերմաստիճան"></th>
                    <th><input type="text" name="wind_speed_09" value="{{ $request->wind_speed_09 ?? '' }}" class="form-control search-inp" placeholder="Քամու արգ. 09"></th>
                    <th><input type="text" name="wind_direction_09" value="{{ $request->wind_direction_09 ?? '' }}" class="form-control search-inp" placeholder="Քամու ուղղ. 09"></th>
                    <th><input type="text" name="wind_speed_27" value="{{ $request->wind_speed_27 ?? '' }}" class="form-control search-inp" placeholder="Քամու արգ. 27"></th>
                    <th><input type="text" name="wind_direction_27" value="{{ $request->wind_direction_27 ?? '' }}" class="form-control search-inp" placeholder="Քամու ուղղ. 27"></th>
                    <th><input type="text" name="bar" value="{{ $request->bar ?? '' }}" class="form-control search-inp" placeholder="Ճնշում"></th>
                    <th><input type="text" name="visibility_09" value="{{ $request->visibility_09 ?? '' }}" class="form-control search-inp" placeholder="Տեսանելիություն 09"></th>
                    <th><input type="text" name="visibility_mid" value="{{ $request->visibility_mid ?? '' }}" class="form-control search-inp" placeholder="Տեսանելիություն сер."></th>
                    <th><input type="text" name="visibility_27" value="{{ $request->visibility_27 ?? '' }}" class="form-control search-inp" placeholder="Տեսանելիություն 27"></th>
                    <th><input type="text" name="start_point" value="{{ $request->start_point ?? '' }}" class="form-control search-inp" placeholder="Ցողի կետ"></th>
                    <th><input type="text" name="contact_coefficient" value="{{ $request->contact_coefficient ?? '' }}" class="form-control search-inp" placeholder="Շփման գործակից"></th>
                    <th><input type="text" name="cloud_height" value="{{ $request->cloud_height ?? '' }}" class="form-control search-inp" placeholder="Ամպերի բրձր."></th>
                    <th><input type="text" name="cloudy" value="{{ $request->cloudy ?? '' }}" class="form-control search-inp" placeholder="Ամպամածություն"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $d)
                    <tr>
                        <td>{{ $d->created_at }}</td>
                        <td>{{ is_numeric($d->wet) ? $d->wet + 0 : $d->wet }}</td>
                        <td>{{ is_numeric($d->temperature) ? $d->temperature + 0 : $d->temperature }}</td>
                        <td>{{ is_numeric($d->wind_speed_09) ? $d->wind_speed_09 + 0 : $d->wind_speed_09 }}</td>
                        <td>{{ is_numeric($d->wind_direction_09) ? $d->wind_direction_09 + 0 : $d->wind_direction_09 }}</td>
                        <td>{{ is_numeric($d->wind_speed_27) ? $d->wind_speed_27 + 0 : $d->wind_speed_27 }}</td>
                        <td>{{ is_numeric($d->wind_direction_27) ? $d->wind_direction_27 + 0 : $d->wind_direction_27 }}</td>
                        <td>{{ is_numeric($d->bar) ? ($d->bar + 0) : $d->bar }}</td>
                        <td>{{ is_numeric($d->visibility_09) ? $d->visibility_09 + 0 : $d->visibility_09 }}</td>
                        <td>{{ is_numeric($d->visibility_mid) ? $d->visibility_mid + 0 : $d->visibility_mid }}</td>
                        <td>{{ is_numeric($d->visibility_27) ? $d->visibility_27 + 0 : $d->visibility_27 }}</td>
                        <td>{{ $d->start_point ? (is_numeric($d->start_point) ? ($d->start_point + 0) : $d->start_point ) : '-' }}</td>
                        <td>{{ $d->contact_coefficient }}</td>
                        <td>{{ $d->cloud_height }}</td>
                        <td>{{ $d->cloudy }}</td>
                    </tr>

                @endforeach
                </tbody>
            </table>
            <span class="pagination">
            {{ $data->appends(request()->except('page'))->links() }}
            </span>
        </div>
    </div>
    <script>
        $(function() {
            $('input[name="created_at"]').daterangepicker({
                opens: 'right',
                timePicker: true,
                autoUpdateInput: false,
                locale: {
                    format: 'Y-MM-D H:m:s'
                }
            });

            $('input[name="created_at"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('Y-MM-D H:m:s') + ' - ' + picker.endDate.format('Y-MM-D H:m:s'));
            });

            $('input[name="created_at"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });

        printTable = () => {
            window.print();
        }

        function search() {
            let query = $(".searchInp").val();
            let search = $(".date").val().split(" - ");
            // if(!query) return;
            let url = location.href.split("?")[0];
            var urlParams = new URLSearchParams(window.location.search);
            // if(!urlParams.has("search")) {
            //     urlParams.append('search', query);
            // } else {
            //     urlParams.set('search', query);
            // }
            if(!urlParams.has("from")) {
                urlParams.append('from', search[0] || '');
                urlParams.append('to', search[1] || '');
            } else {
                urlParams.set('from', search[0] || '');
                urlParams.set('to', search[1] || '');
            }
            $(".search-inp").each(function () {
                let name = $(this).attr("name");
                let val = $(this).val();
                if(val && !$(this).hasClass("date")) {
                    if(!urlParams.has(name)) {
                        urlParams.append(name, val)
                    } else {
                        urlParams.set(name, val)
                    }
                } else {
                    urlParams.delete(name)
                }
            });
            urlParams.delete('page')
            let params = urlParams.toString();
            location.href = url + "?" + params;
        }

        $(document).ready(function () {
            let query = location.href.split('?')[1];
            if(query !== undefined && query != '') {
                $("#export-form").attr('action', $("#export-form").attr('action') + '?' + query);
            }
        });
    </script>

@endsection

