@extends('crudbooster::admin_template')
@section('content')

<div id='box_main' class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Upload a File</h3>
        <div class="box-tools"></div>
    </div>

    <form method='post' id="form" enctype="multipart/form-data" action="{{ route('upload-status-update') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="box-body">

            <div class='callout callout-success'>
                <h4>Welcome to Data Importer Tool</h4>
                Before uploading a file, please read below instructions : <br/>
                * File format should be : CSV, XLSX file format<br/>
            </div>

            <label class='col-sm-1 control-label'>Import Template File: </label>
            <div class='col-sm-2'>
                <a href='{{ CRUDBooster::mainpath() }}/upload-user-account-template' class="btn btn-primary" role="button">Download Template</a>
            </div>

            <label class='col-sm-1 control-label'>Upload Type: </label>
            <div class='col-sm-3'>
                <select class="form-control select2" style="width: 100%;" required name="upload_type" id="upload_type">
                    <option value="">Select Upload Type</option>
                    <option value="cancel">Cancel</option>
                    <option value="move_order">Update to move order</option>
                </select>
            </div>

            <label for='import_file' class='col-sm-1 control-label'>File to Import: </label>
            <div class='col-sm-4'>
                <input type='file' name='import_file' class='form-control' required/>
                <div class='help-block'>File type supported only : CSV, XLSX</div>
            </div>

        </div><!-- /.box-body -->

        <div class="box-footer">
            <div class='pull-right'>
                <a href='{{ CRUDBooster::mainpath() }}' class='btn btn-default'>Cancel</a>
                <input type='submit' class='btn btn-primary' name='submit' value='Upload'/>
            </div>
        </div><!-- /.box-footer-->
    </form>
</div><!-- /.box -->

@endsection