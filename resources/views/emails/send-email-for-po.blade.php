<!DOCTYPE html>
<html>
<head>
    <title>Digits Asset Management System</title>
</head>
<body>
    
<p>Hi<span style="font-weight: 700;">&nbsp;{{ $infos['assign_to'] }}</span></p>

<p>New Item Sourcing, Please see details below.</p>

    <div class="row" style="display:flex;">
        <div class="col1">
        <p><span style="font-weight: 700;">REF#:&nbsp;</span>
            <span style="text-align: center;">{{ $infos['reference_number'] }}</span>
            </p>
        </div>
        <div class="col1" style="padding-left: 450px">
        <p><span style="font-weight: 700;">Requested Date:&nbsp;</span>
            <span style="text-align: center;">{{ $infos['created_at'] }}</span>
            </p>
        </div>
    </div>

    <div class="row" style="display:flex;">
        <div class="col1">
        <p><span style="font-weight: 700;">Employee Name:&nbsp;</span>
            <span style="text-align: center;">{{ $infos['employee_name'] }}</span>
            </p>
        </div>
        <div class="col1" style="padding-left: 370px">
        <p><span style="font-weight: 700;">Company Name:&nbsp;</span>
            <span style="text-align: center;">{{ $infos['company_name'] }}</span>
            </p>
        </div>
    </div>

    <div class="row" style="display:flex;">
        <div class="col1">
        <p><span style="font-weight: 700;">Department:&nbsp;</span>
            <span style="text-align: center;">{{ $infos['department'] }}</span>
            </p>
        </div>
        <div class="col1" style="padding-left: 210px">
        <p><span style="font-weight: 700;">Position:&nbsp;</span>
            <span style="text-align: center;">{{ $infos['position'] }}</span>
            </p>
        </div>
    </div>

    <div class="row" style="display:flex;">
        <div class="col1">
        <p><span style="font-weight: 700;">Date Needed:&nbsp;</span>
            <span style="text-align: center;">{{ $infos['date_needed'] }}</span>
            </p>
        </div>
        <div class="col1" style="padding-left: 415px">
        <p><span style="font-weight: 700;">Status:&nbsp;</span>
            <span style="text-align: center;">{{ $infos['status'] }}</span>
            </p>
        </div>
    </div>

    <div class="row" style="display:flex;">
        <div class="col1">
        <p><span style="font-weight: 700;">Request Type:&nbsp;</span>
            <span style="text-align: center;">{{ $infos['request_type'] }}</span>
            </p>
        </div>
    </div>

    <table border="1" width="100%" style="text-align: center;">
        <tbody>
        <tr>
            <th class="control-label col-md-2">Digits Code</th>
            <th class="control-label col-md-2">Item Description</th>
            <th class="control-label col-md-2">Category</th>
            <th class="control-label col-md-2">Sub Category</th>
            <th class="control-label col-md-2">Class</th>
            <th class="control-label col-md-2">Sub Class</th>
            <th class="control-label col-md-2">Brand</th>
            <th class="control-label col-md-2">Model</th>
            <th class="control-label col-md-2">Size</th>
            <th class="control-label col-md-2">Actual Color</th>
            <th class="control-label col-md-2">Quantity</th>
            <th class="control-label col-md-2">Budget Range</th>
        </tr>
        <tr>
            <td class="col-md-4">{{$infos['digits_code']}}</td>   
            <td class="col-md-4">{{$infos['item_description']}}</td>     
            <td class="col-md-4">{{$infos['category']}}</td>  
            <td class="col-md-4">{{$infos['sub_category']}}</td>  
            <td class="col-md-4">{{$infos['class']}}</td>  
            <td class="col-md-4">{{$infos['sub_class']}}</td>  
            <td class="col-md-4">{{$infos['brand']}}</td>  
            <td class="col-md-4">{{$infos['model']}}</td>  
            <td class="col-md-4">{{$infos['size']}}</td>  
            <td class="col-md-4">{{$infos['actual_color']}}</td>  
            <td class="col-md-4">{{$infos['quantity']}}</td>  
            <td class="col-md-4">{{$infos['budget']}}</td>  
        </tr>
        </tbody>
    </table>

</body>
</html>