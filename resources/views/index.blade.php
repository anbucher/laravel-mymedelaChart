@extends('layouts.app')
@section('scripts')
<script>
    $(document).ready(function(){
        $("#message-alert").fadeTo(2000, 500).slideUp(500, function(){
            $("#message-alert").slideUp(500);
        });
    });
</script>
@endsection

@section('content')
@if(session()->get('message'))
    <div id="message-alert" class="alert alert-{{ session()->get('alerttype') }}">
       {{ session()->get('message') }}
       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if(!session()->get('stat.data'))
<div class="w-100 p-3">
    <div class="row justify-content-center">
        <div class="w-100 p-3">
            <div class="card">
                <div class="card-header">File Upload</div>
                <div class="card-body">
                <form method="POST" action="{{ route('file.upload') }}" aria-label="{{ __('Upload') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label for="excel_file" class="col-md-4 col-form-label text-md-right">{{ __('MyMedela Excel') }}</label>
                            <div class="col-md-6">
                                <input id="excel_file" type="file" class="form-control{{ $errors->has('excel_file') ? ' is-invalid' : '' }}" name="excel_file" required>
                                @if ($errors->has('excel_file'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('excel_file') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Upload') }}
                                </button>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
     
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@if(session()->get('stat.data'))
<div class="w-100 p-3 h-100">
    <div class="card h-100">
        <div class="card-header"></div>
        <div class="card-body h-75">
            <div id="chart"></div>
            <script>
                Highcharts.chart('chart', {

                    chart: {
                        type: 'heatmap',
                        margin: [80, 10, 80, 100]
                    },
                    tooltip: {
                        formatter: function() {

                            var hour = Math.floor(this.point.x);
                            hour = (hour < 10 ? '0' : '') + hour;
                            var min = Math.round((this.point.x-Math.floor(this.point.x))*60);
                            min = (min < 10 ? '0' : '') + min;

                            return new Date(this.point.y).toLocaleDateString() + ' ' + hour + ':' + min + ' <b>' + this.point.value + 'min </b>';  //{this.y:%e %b}' + Math.floor(this.x)/60 + '<b>this.value min</b>'
                        }
                    },
                    boost: {
                        useGPUTranslations: true
                    },

                    title: {
                        text: 'Feeding Statistic',
                        align: 'left',
                        x: 40
                    },
                    xAxis: {
                        type: 'datetime',
                        crosshair: true,
                        title: {
                            text: null
                        },
                        labels: {
                            format: '{value}:00'
                        },
                        minPadding: 0,
                        maxPadding: 0,
                        startOnTick: false,
                        endOnTick: false,
                        tickPositions: [0, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24],
                        tickWidth: 1,
                        min: 0,
                        max: 23
                    },
                    yAxis: {
                        type: 'datetime',
                        title: {
                            text: null
                        },
                        crosshair: true,
                        tickLength: 1,
                        tickInterval: 1000*60*60*24,
                        min: {!!session()->get('stat.min_date')!!},
                        max: {!!session()->get('stat.max_date')!!},
                        reversed: true,
                        labels: {
                            step: 10
                        }
                    },
                    colorAxis: {
                        stops: [
                            [0, '#3060cf'],
                            [0.2, '#fffbbc'],
                            [0.8, '#c4463a'],
                            [1, '#c4463a']
                        ],
                        min: 5,
                        max: 20,
                        startOnTick: false,
                        endOnTick: false,
                        labels: {
                            format: '{value} min'
                        },
                    },

                    series: [{
                        boostThreshold: 100,
                        borderWidth: 0,
                        nullColor: '#EFEFEF',
                        colsize: 1 / 12, // one day
                        rowsize: 24 * 36e5,
                        tooltip: {
                            headerFormat: 'Dauer<br/>'
                        },
                        turboThreshold: Number.MAX_VALUE, // #3404, remove after 4.0.5 release
                        data: {!!session()->get('stat.data')!!}
                    }],
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 500
                            },
                            chartOptions: {
                                legend: {
                                    align: 'center',
                                    verticalAlign: 'bottom',
                                    layout: 'horizontal'
                                },
                                yAxis: {
                                    labels: {
                                        align: 'left',
                                        x: 0,
                                        y: -5
                                    },
                                    title: {
                                        text: null
                                    }
                                },
                                subtitle: {
                                    text: null
                                },
                                credits: {
                                    enabled: false
                                }
                            }
                        }]
                    }
                });
            </script>
        </div>
    </div>
</div>
@endif
@endsection