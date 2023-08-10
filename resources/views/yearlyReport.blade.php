<!DOCTYPE html>
<html>

<head>
    <title>Yearly fees report </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
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
                    <h2>Yearly Fees Report</h2>
                    <h4>{{ $mytime }}</h4>
                </div>
                <div class="pull-right">
                </div>
            </div>
        </div><br>

        <div>

            <div class='chart_img'>
                <h4>Study Fees</h4>
                <img style="
                    width: 500px;
                    height: auto;
                     "
                    src="charts/year_study.png" alt="Kk">
            </div>

        </div>
        <br>
        <div>
            <div class='chartimg'>
                <h4>Bus Fees</h4>
                <img style="
                    width: 500px;
                    height: auto;
                    "
                    src="charts/year_bus.png" alt="Kk">
            </div>
        </div>



    </div>

</body>

</html>
