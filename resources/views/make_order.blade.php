<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <style>
            .no-padding {
                padding: 0;
            }
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
            select option {
                font-weight: bold;
            }
            .content {
                text-align: center;
                display: inline-block;
            }
            button {
                font-weight: bold;
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
                    <h2>Buying as {{$customer->name}}</h2>
                </div>
                <div class="col-sm-5 no-padding">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(Session::has('flash_success'))
                        <div class="alert alert-success">
                            <b>{{Session::get('flash_success')}}</b>
                        </div>
                    @endif
                    
                    @if(Session::has('flash_error'))
                        <div class="alert alert-danger text-left">
                            <b>{{Session::get('flash_error')}}</b>
                        </div>
                    @endif
                    <form method="POST" action="/{{Session::get('lang')}}/post/order">
                        {{ csrf_field() }}
                        <div class="col-sm-12">
                            <div class="col-xs-6 no-padding">
                                <h3>Product</h3>
                            </div>
                            <div class="col-xs-6 no-padding">
                                <h3>Quantity</h3>
                            </div>
                            <div class="col-xs-12 no-padding" id="prods-selection">
                                @foreach($products as $p)
                                    <div class="col-xs-6 no-padding">
                                        {{$p->description}} ({{$p->category->name}})
                                    </div>
                                    <div class="col-xs-6 no-padding">
                                        <input data-price="{{$p->price}}" class="input-qtd" steps="0" min="1" type="number" name="quantities[{{$p->id}}]">
                                    </div>
                                @endforeach
                            </div>
                            <br>
                            <input type="hidden" name="id_customer" value="{{$customer->id}}">
                            <br>
                            <div id="total">Total: <span>0</span> €</div>
                            <button type="submit" class="btn btn-default">Buy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script
  src="https://code.jquery.com/jquery-2.2.4.min.js"
  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
  crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script type="text/javascript">
            var price, qtd, total, v;
            $('.input-qtd').on('keyup', function() {
                total = 0;
                $('.input-qtd').each(function() {
                    qtd = 0;
                    v = $(this).val();
                    if(v != '' && !isNaN(v)) {
                        qtd = v;
                    }
                    price = $(this).data('price');
                    total += Math.round((price * qtd) * 100) / 100;
                });
                $('#total span').html(Math.round((total) * 100) / 100);
            });
        </script>
    </body>
</html>
