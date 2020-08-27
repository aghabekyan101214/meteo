@extends('layouts.app')

@section('content')

    <div class="white-box">
        <div class="row">
            <div class="col-md-12" style="text-align: right">
                <a href="/metar">
                    <button class="btn btn-success"><i class="fa fa-recycle"></i></button>
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table id="myTable" class="table table-striped">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>значение</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <input autocomplete="off" type="text" name="datefilter" class="form-control date" value="{{ !is_null($request->from) ? ($request->from . " - " . $request->to) : '' }}"/>
                    </td>
                    <td>
                        <div class="input-group">
                            <div style="display: flex">
                                <input type="text" class="form-control searchInp" value="{{ $request->search }}">
                                <button class="btn btn-deafult" onclick="search()" style="margin-left: 10px;"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </td>
                </tr>
                @foreach($data as $key => $value)
                    <tr>
                        <td>{{ $value->date }}</td>
                        <td>{{ $value->value }}</td>
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
            let query = $(".searchInp").val();
            let search = $(".date").val().split(" - ");
            // if(!query) return;
            let url = location.href.split("?")[0];
            var urlParams = new URLSearchParams(window.location.search);
            if(!urlParams.has("search")) {
                urlParams.append('search', query);
            } else {
                urlParams.set('search', query);
            }
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

