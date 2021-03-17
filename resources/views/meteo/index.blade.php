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
                        <th>Խոնավություն</th>
                        <th>Ջերմաստիճան</th>
                        <th>Քամու արգ. 09</th>
                        <th>Քամու ուղղ. 09</th>
                        <th>Քամու արգ. 27</th>
                        <th>Քամու ուղղ. 27</th>
                        <th>Ճնշում</th>
                        <th>Տեսանելիություն 09</th>
                        <th>Տեսանելիություն сер.</th>
                        <th>Տեսանելիություն 27</th>
                        <th>Ցողի կետ</th>
                        <th>Շփման գործակից</th>
                        <th>Ամպերի բրձր.</th>
                        <th>Ամպամածություն</th>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <th>Humidity</th>
                        <th>Temperature</th>
                        <th>Wind speed 09</th>
                        <th>Wind direction 09</th>
                        <th>Wind speed 27</th>
                        <th>Wind direction 27</th>
                        <th>Pressure</th>
                        <th>Visibility 09</th>
                        <th>Visibility mid.</th>
                        <th>Visibility 27</th>
                        <th>Dew point</th>
                        <th>Contact coefficient</th>
                        <th>Cloud height</th>
                        <th>Cloudiness</th>
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
                            <td>{{ number_format($d->wet, 2) > 0 ? number_format($d->wet, 2) :  '-' }}</td>
                            <td>{{ number_format($d->temperature, 2) > 0 ? number_format($d->temperature, 2) : '-' }}</td>
                            <td>{{ number_format($d->wind_speed_09, 2) > 0 ? number_format($d->wind_speed_09, 2) : '-' }}</td>
                            <td>{{ number_format($d->wind_direction_09, 2) > 0 ? number_format($d->wind_direction_09, 2) : '-' }}</td>
                            <td>{{ number_format($d->wind_speed_27, 2) > 0 ? number_format($d->wind_speed_27, 2) : '-' }}</td>
                            <td>{{ number_format($d->wind_direction_27, 2) > 0 ? number_format($d->wind_direction_27, 2) : '-' }}</td>
                            <td>{{ number_format($d->bar, 2) > 0 ? number_format($d->bar, 2) : '-' }}</td>
                            <td>{{ number_format($d->visibility_09, 2) > 0 ? number_format($d->visibility_09, 2) : '-' }}</td>
                            <td>{{ number_format($d->visibility_mid, 2) > 0 ? number_format($d->visibility_mid, 2) : '-' }}</td>
                            <td>{{ number_format($d->visibility_27, 2) > 0 ? number_format($d->visibility_27, 2) : '-' }}</td>
                            <td>{{ $d->start_point ?? '-' }}</td>
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

