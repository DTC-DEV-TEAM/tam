@extends('crudbooster::admin_template')
    @push('head')
    
        <style type="text/css">   
            table.dataTable td.dataTables_empty {
                text-align: center;    
            }
            .active{
                font-weight: bold;
                font-size: 13px;
                color:#3c8dbc
            }
            .modal-content  {
                -webkit-border-radius: 3px !important;
                -moz-border-radius: 3px !important;
                border-radius: 3px !important; 
            }

            #table_dashboard td.hover:hover {
                background-color:#3c8dbc;
                color: #fff !important;
                font-weight: bold;
                font-size: 14px;
                
            }

            a{
                color: #000;
            }

        </style>
    @endpush
@section('content')

<div class='panel panel-default'>
   

        <div class='panel-body'>
        <div class="row" style="margin:5px">   
       
        <button type="button" id="btn-export" class="btn btn-primary btn-sm btn-export" style="margin-bottom:10px"><i class="fa fa-download"></i>
            <span>Export Data</span>
        </button>
        <form   method='post' target='_blank'>
            <table class='table table-hover table-striped table-bordered' id="table_dashboard">
                <thead>
                    <tr class="active">
                        <th width="auto" style="text-align:center">Erf Number</th>
                        <th width="auto" style="text-align:center">Jo Done</th>
                        <th width="auto" style="text-align:center">First Interview</th>
                        <th width="auto" style="text-align:center">Final Interview</th>
                        <th width="auto" style="text-align:center">For Comparison</th>
                        <th width="auto" style="text-align:center">Cancelled</th>
                        <th width="auto" style="text-align:center">Rejected</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($getData as $val)
                <tr>
                <audio id="audio">
                 <source src="{{asset('audio/hover-click.mp3')}}"/>
                </audio>
        
                    <td style="text-align:center"> {{$val->erf_number}}</td>
                    <td class="hover" id="hover" style="text-align:center"><a href='{{CRUDBooster::mainpath("summary-report/".$val->erf_number."-"."31")}}'></a>{{$val->jo_done}}</td>  
                    <td class="hover" id="hover" style="text-align:center"><a href='{{CRUDBooster::mainpath("summary-report/".$val->erf_number."-"."34")}}'></a>{{$val->first_interview}}</td>
                    <td class="hover" id="hover" style="text-align:center"><a href='{{CRUDBooster::mainpath("summary-report/".$val->erf_number."-"."35")}}'></a>{{$val->final_interview}}</td> 
                    <td class="hover" id="hover" style="text-align:center"><a href='{{CRUDBooster::mainpath("summary-report/".$val->erf_number."-"."42")}}'></a>{{$val->for_comparison}}</td>    
                    <td class="hover" id="hover" style="text-align:center"><a href='{{CRUDBooster::mainpath("summary-report/".$val->erf_number."-"."8")}}'></a>{{$val->cancelled}}</td>
                    <td class="hover" id="hover" style="text-align:center"><a href='{{CRUDBooster::mainpath("summary-report/".$val->erf_number."-"."5")}}'></a>{{$val->rejected}}</td>     
                </tr>
                    
                @endforeach
                </tbody>
            </table>
        </form>
        </div>   
        </div>
</div>

@endsection

@push('bottom')
<script src=
"https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js" >
    </script>
    <script src=
"https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" >
    </script>
        <script src=
"https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js" >
    </script>
    <script type="text/javascript">
       var table;
       $(document).ready(function() {
            table = $("#table_dashboard").DataTable({

                ordering:false,
                pageLength:25,
                language: {
                    searchPlaceholder: "Search"
                },
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"],
                    ],
                buttons: [
                    {
                        extend: "excel",
                        title: "Summary Report",
                        exportOptions: {
                            modifier: {
                            page: "current",
                        }
                        },
                    },
                ],
            });
            $("#btn-export").on("click", function () {
                table.button(".buttons-excel").trigger();
            });
            $('#erf_number,#status').select2({})
           
            $('#table_dashboard td.hover').hover(function() {
                $(this).addClass('hover');
               
                var audio = $("#audio")[0];
                $("#table_dashboard td.hover").mouseenter(function() {  
                    audio.play();
                    //audio.loop = true;
                });
                $("#table_dashboard td.hover").mouseleave(function() {
                    audio.pause();
                });
            }, function() {
                $(this).removeClass('hover');
            });

            $(document).on('click', '#hover', function() {
                var href = $('a', this).attr('href');
                window.location.href = href;
            });


    });
    </script>
@endpush