<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: bold;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }
        </style>
    </head>
    <body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <h3>Customers</h3>
                <ul>
                @foreach($customers as $c)
                    <li><a href="{{url(Session::get('lang'). '/make-order/' .$c->id)}}">Make an order as {{$c->name}}</a></li>
                @endforeach
                </ul>
            </div>
        </div>
    </div>
    </body>
</html>
