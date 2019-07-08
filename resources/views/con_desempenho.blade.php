<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
         <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Styles -->
        <link href="{{ asset('css/normalize.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">
        <link href="{{ asset('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/sweetalert.css') }}" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-duallistbox.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datetimepicker.css')}}"> 
    </head>
    <body>
        <!-- header de la pagina -->
        @component('components.header')
        @endcomponent
        <div class="container mt-5">
           <div class="jumbotron">
            <div class="row">
                <div class="col-md-2 mt-2">
                    <b>Período</b>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="date-range input-group input-group-md">
                        <span class="input-group-addon" id="sizing-addon1"><i class="fa fa-calendar-minus-o" aria-hidden="true"></i></span>
                        <input type='text' placeholder="Deste" class="form-control" aria-describedby="sizing-addon1" id="startDate" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="date-range input-group input-group-md">
                        <span class="input-group-addon" id="sizing-addon1"><i class="fa fa-calendar-minus-o" aria-hidden="true"></i></span>
                        <input type='text' placeholder="Até" class="form-control" aria-describedby="sizing-addon1" id="endDate" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-2 mt-5">
                    <b>Consultores</b>
                </div>
                <div class="col-md-8">
                    <select id="selectConsultores" name="selectConsultores" multiple="multiple"></select>
                </div>
                <div class="col-md-2 mt-3 list-group">
                    <button type="button" id="btnRelatorio" class="btn btn"><i class="fa fa-list-alt"></i> Relatório</button>
                    <button type="button" id="btnGrafico" class="btn btn mt-2"><i class="fa fa-bar-chart"></i> Gráfico</button>
                    <button type="button" id="btnPizza" class="btn btn mt-2"><i class="fa fa-pie-chart"></i> Pizza</button>
                </div>
            </div>
        </div>
            <div class="col-md-12 col-sm-5 mt-5 text-center" id="table">
            </div>
            <div class="col-md-12 mt-5 text-center" id="showGrafico">
                <canvas id="graficoChart" width="400" height="150"></canvas>
            </div>
            <div class="col-md-12 mt-5 text-center" id="showPizza">
                <canvas id="pizzaChart" width="400" height="150"></canvas>
            </div>
        </div>

        {{$beforeScripts or null}} 

        <script src="{{asset('js/library/chart.bundle.js')}}"></script>
        <script src="{{asset('js/library/chart.js')}}"></script> 
        <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
        <script src="{{ asset('js/jquery-ui.js') }}"></script>
        <script src="{{ asset('js/bootstrap/popper.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/jquery.bootstrap-duallistbox.min.js') }}"></script>
        <script src="{{ asset('js/sweetalert.min.js') }}"></script>
        <script src="{{ asset('js/moment.js') }}"></script>
        <script src="{{ asset('js/pt-br.js') }}"></script>
        <script src="{{ asset('js/datetimepicker.min.js') }}"></script>

        <script type="text/javascript">
            var urlUsersConsultores = "{{route('getConsutores')}}";
            var urlRelatorio = "{{route('relatorio')}}";
            var urlGrafico = "{{route('grafico')}}";
            var urlPizza = "{{route('percentageReceitaLiquida')}}";
            var token=$('meta[name="csrf-token"]').attr('content');//envio de token
        </script>
        <script src="{{ asset('js/view/con_desempenho.js') }}"></script>
    {{$afterScripts or null}}
</body>
</html>

