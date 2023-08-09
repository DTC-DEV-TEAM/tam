<!DOCTYPE html>
<html>
<head>
    <title>Digits Asset Management System</title>
</head>
<body>
    
<p>Hi<span style="font-weight: 700;">&nbsp;{{ $infos->bill_to }}</span></p>

<p>Kindly check the details of your IT Assets such as Asset Code, Digits Code and serial number deployed to you</p><br>   
<p>Please reply to this email if the items deployed are not the same and make sure to CC bpg@digits.ph</p><br>

    <table border="1" width="100%" style="text-align: center;">
        <tbody>
        <tr>
            <th class="control-label col-md-2">Status</th>
            <th class="control-label col-md-2">Digits Code</th>
            <th class="control-label col-md-2">Item Description</th>
            <th class="control-label col-md-2">Asset Code</th>
            <th class="control-label col-md-2">Serial No</th>
        </tr>
        @foreach($infos->assets as $asset)
        <tr>
            <td class="col-md-4">{{$asset->status_description}}</td>   
            <td class="col-md-4">{{$asset->digits_code}}</td>   
            <td class="col-md-4">{{$asset->item_description}}</td>     
            <td class="col-md-4">{{$asset->asset_code}}</td>  
            <td class="col-md-4">{{$asset->serial_no}}</td>  
        </tr>
        @endforeach
        </tbody>
    </table>

</body>
</html>