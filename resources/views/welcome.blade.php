<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Apriori Product Suggestions</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="{{ asset('build/assets/app-f470565a.css') }}">

</head>

<body>
    <br /><br />
    <div class="container">
        <h2>Apriori Product Recommendations</h2>
        <form name="suggestions" id="form1" method="get" action="{{ url('recommend') }}">
            <div class="form-group">

                <label for="product1">Choose three products:</label>

                <div class="form-group">
                    <label for="product1">Item 1:</label>
                    <select name="product1" id="product1" class="form-control">
                        <option selected>Choose...</option>
                        @foreach ($products as $product)
                            <option value="{{ $product }}">{{ $product }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="product2">Item 2:</label>
                    <select name="product2" id="product2" class="form-control">
                        <option selected>Choose...</option>
                        @foreach ($products as $product)
                            <option value="{{ $product }}">{{ $product }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="product3">Item 3:</label>
                    <select name="product3" id="product3" class="form-control">
                        <option selected>Choose...</option>
                        @foreach ($products as $product)
                            <option value="{{ $product }}">{{ $product }}</option>
                        @endforeach
                    </select>
                </div>
                <br />
                <input type="submit" class="btn btn-primary">

            </div>

        </form>
        <br />

        @if (isset($recommendation))
            <div class="alert alert-success" role="alert">
                <h5>We found recommendations based on other customers who purchased {{ $selected }}:</h5>

            </div>
            <div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Product</th>
                            <th scope="col">Support</th>
                            <th scope="col">Confidence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $counter = 1;
                        @endphp
                        @foreach ($result as $item => $s_c)
                            <tr>
                                <th scope="row">{{ $counter }}</th>
                                <td>{{ $item }}</td>
                                <td>{{ $s_c['support'] }}</td>
                                <td>{{ $s_c['confidence'] }}</td>
                            </tr>
                            @php
                                $counter = $counter + 1;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
            <br /><br />
            <div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
                <h2></h2>
                <canvas id="liftChart" style="width:100%;max-width:700px"></canvas>
                <script>
                    var xValues = JSON.parse({{ Js::from($items) }});
                    var yValues = JSON.parse({{ Js::from($lift) }});
                    var barColors = ["red", "green", "blue"];

                    new Chart("liftChart", {
                        type: "bar",
                        data: {
                            labels: xValues,
                            datasets: [{
                                backgroundColor: barColors,
                                data: yValues
                            }]
                        },
                        options: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: "Lift by Product"
                            }
                        }
                    });
                </script>
            </div>
        @endif

        @if (isset($error))
            <div class="alert alert-success" role="alert">
                <h5>The selection must include 3 different products!</h5>
                {{-- <p> {{$error}} </p> --}}
            </div>

            {{-- <canvas id="myChart" style="max-width: 500px;"></canvas> --}}
        @endif


</body>
<script src="{{ asset('build/assets/app-7e506d02.js') }}">
    < /html>
