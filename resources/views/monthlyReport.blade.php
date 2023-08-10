<!DOCTYPE html>
<html>

<head>
    <title>Monthly fees report </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    {{-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script> --}}
    {{-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
      google.charts.load("current", {packages:['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
          ['paided', {{ $paided }}] ,
          [ 'unpaided',{{ $unpaided }}]
        ]);

        var options = {
          title: 'current amount',
          pieHole: 0.5,
        };

        var chart_div = document.getElementById('chart_div');
      var chart = new google.visualization.PieChart(document.getElementById('chart_div'));

      // Wait for the chart to finish drawing before calling the getImageURI() method.
      google.visualization.events.addListener(chart, 'ready', function () {
        chart_div.innerHTML = '<img src="' + chart.getImageURI() + '">';
        console.log(chart_div.innerHTML);
      });

      chart.draw(data, options);
        // chart.draw(data, options);
      }

    </script> --}}
</head>

<body>
    @php
        $i = 0;
        $mytime = Carbon\Carbon::now();
        $mytime->toDateTimeString();
    @endphp
    <div class="container">
        <div class="row">
            <div class="col-lg-12" style="margin-top: 15px ">
                <div class="pull-left">
                    <h2>Monthly Fees Report</h2>
                    <h4>{{ $mytime }}</h4>
                </div>
                <div class="pull-right">
                    {{-- <a class="btn btn-primary" href="{{route('users.index',['download'=>'pdf'])}}">Download PDF</a> --}}
                </div>
            </div>
        </div><br>
        <table class="table table-striped table-bordered ">
            <thead class="thead-dark">

                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Fees</th>
                    <th>paid</th>
                    <th>remind</th>
                    <th>status</th>
                </tr>

                @foreach ($data['data'] as $d)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $d['name'] }}</td>
                        <td>{{ $d['fees'] }}</td>
                        <td>{{ $d['current_amount'] }}</td>
                        <td>{{ $d['remind'] }}</td>
                        <td>{{ $d['status'] }}</td>
                    </tr>
                @endforeach
        </table>
        <div>
            <p>Bus Fees : {{ $data['data'][0]['bus_fees'] }}</p>
            <p>paided Fees : {{ $paided }}</p>
            <p>Full fees : {{ $full }}</p>
            <div class='chart_img'>
                <img style="
                    width: 300px;
                    height: auto;
                    float: top-left; " src="charts/d.png" alt="Kk">
            </div>
        </div>
        {{-- <div class="chart-container">
            {{-- <canvas  id="myChart"  width="200" height="200"></canvas> --}}
        {{-- <div id="donutchart" style="width: 900px; height: 500px;"></div> --}}

        {{-- </div> - --}}


    </div>

</body>

</html>
<style>
    .chart-container {
    padding-top: 20px;
    display: grid;
    place-items: center;
    /* width: 500px;
    height: 500px; */
    height: 50vh;
}
.chart_img {
    margin-left :200px;
    margin-top: -300px;
}
</style>
{{-- <script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['paided', 'unpaided'],
            datasets: [{
                data: [{{ $paided }}, {{ $unpaided }}],
                backgroundColor: [

                    'rgb(196, 229, 56)'   ,
                    'rgb(27, 156, 252)'             ],
                borderColor: [
                    'rgb(196, 229, 56)'   ,
                    'rgb(27, 156, 252)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            options: {
        plugins: {
            annotation: {
                annotations: [{
                    type: 'text',
                    x: '50%',
                    y: '50%',
                    text: 'Center Text',
                    fontColor: 'black',
                    fontSize: 18,
                    fontStyle: 'bold'
                }]
            }
        }
    }
        }
    });
</script>
<script>
    var canvas = document.getElementById('myChart');
var ctx = canvas.getContext('2d');
ctx.font = '18px Arial';
ctx.fillStyle = 'black';
ctx.fillText('Sample Text', 200, 200);
</script> --}}
