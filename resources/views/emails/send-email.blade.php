<!DOCTYPE html>
<html>
<head>
    <title>Digits Asset Management System</title>
</head>
<body>
    
<p>Hi<span style="font-weight: 700;">&nbsp;{{ $infos['assign_to'] }}</span></p>

<p>You have changes in the Item Details, Please see details below.</p>

    <div class="row" style="display:flex;">
        <div class="col1" style="margin-right: 600px">
        <p><span style="font-weight: 700;">REF#:&nbsp;</span>
            <span style="text-align: center;">{{ $infos['reference_number'] }}</span>
            </p>
        </div>
    </div>

    <table border="1" width="100%" style="text-align: center;">
        <tbody>
        @if($infos['request_type_id'] == 6)
               
                <tr>
                    @foreach($infos['item_description'] as $item)
                        <th class="control-label col-md-2">Item Description:</th>
                        <td class="col-md-4">{{$item}}</td>   
                    @endforeach  
                </tr>

                <tr>
                    @foreach($infos['brand']  as $brand)
                        <th class="control-label col-md-2">Brand:</th>
                        <td class="col-md-4">{{$brand}}</td>
                    @endforeach 
                </tr>
                <tr>
                    @foreach($infos['model']  as $model)
                        <th class="control-label col-md-2">Model:</th>
                        <td class="col-md-4">{{$model}}</td>
                    @endforeach 
                </tr>
                <tr>
                    @foreach($infos['size'] as $size)
                        <th class="control-label col-md-2">Size:</th>
                        <td class="col-md-4">{{$size}}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($infos['actual_color'] as $actual_color)
                        <th class="control-label col-md-2">Actual Color:</th>
                        <td class="col-md-4">{{$actual_color}}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($infos['material'] as $material)
                        <th class="control-label col-md-2">Material:</th>
                        <td class="col-md-4">{{$material}}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($infos['thickness'] as $thickness)
                        <th class="control-label col-md-2">Thickness:</th>
                        <td class="col-md-4">{{$thickness}}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($infos['lamination'] as $lamination)
                        <th class="control-label col-md-2">Lamination:</th>
                        <td class="col-md-4">{{$lamination}}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($infos['add_ons'] as $add_ons)
                        <th class="control-label col-md-2">Add Ons:</th>
                        <td class="col-md-4">{{$add_ons}}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($infos['installation'] as $installation)
                        <th class="control-label col-md-2">Installation:</th>
                        <td class="col-md-4">{{$installation}}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($infos['dismantling'] as $dismantling)
                        <th class="control-label col-md-2">Dismantling:</th>
                        <td class="col-md-4">{{$dismantling}}</td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($infos['quantity'] as $quantity)
                        <th class="control-label col-md-2">Quantity:</th>
                        <td class="col-md-4">{{$quantity}}</td>
                    @endforeach
                </tr>
                
         
        @else
            <tr>
                <th class="control-label col-md-2">Item Description:</th>
                <td class="col-md-4">{{$infos['item_description']}}</td>     
            </tr>

            <tr>
                <th class="control-label col-md-2">Brand:</th>
                <td class="col-md-4">{{$infos['brand']}}</td>
            </tr>
            <tr>
                <th class="control-label col-md-2">Model:</th>
                <td class="col-md-4">{{$infos['model']}}</td>
            </tr>
            <tr>
                <th class="control-label col-md-2">Size:</th>
                <td class="col-md-4">{{$infos['size']}}</td>
            </tr>
            <tr>
                <th class="control-label col-md-2">Actual Color:</th>
                <td class="col-md-4">{{$infos['actual_color']}}</td>
            </tr>
            <tr>
                <th class="control-label col-md-2">Quantity:</th>
                <td class="col-md-4">{{$infos['quantity']}}</td>
            </tr>
        @endif
        </tbody>
    </table>

</body>
</html>