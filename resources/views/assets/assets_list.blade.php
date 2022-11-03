<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
<!-- Your custom  HTML goes here -->
<table class='table table-hover table-striped table-bordered' id="table_dashboard">
  <thead>
      <tr class="active">
        <th width="auto">Name</th>
        <th width="auto">Description</th>
        <th width="auto">Price</th>
        <th width="auto">Action</th>
       </tr>
  </thead>
  <tbody>
    @foreach($result as $row)
      <tr>
        <td>{{$row->asset_code}}</td>
        <td>{{$row->asset_code}}</td>
        <!-- <td>{{$row->description}}</td>
        <td>{{$row->price}}</td> -->
        <td>
          <!-- To make sure we have read access, wee need to validate the privilege -->
          @if(CRUDBooster::isUpdate() && $button_edit)
          <a class='btn btn-success btn-sm' href='{{CRUDBooster::mainpath("edit/$row->id")}}'>Edit</a>
          @endif
          
          @if(CRUDBooster::isDelete() && $button_edit)
          <a class='btn btn-success btn-sm' href='{{CRUDBooster::mainpath("delete/$row->id")}}'>Delete</a>
          @endif
        </td>
       </tr>
    @endforeach
  </tbody>
</table>

<!-- ADD A PAGINATION -->
<p>{!! urldecode(str_replace("/?","?",$result->appends(Request::all())->render())) !!}</p>
@endsection

@push('bottom')
<script type="text/javascript">
$("#table_dashboard").DataTable({
    pageLength:10
});

	</script>
    @endpush