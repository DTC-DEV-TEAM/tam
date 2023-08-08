<div class="col-md-6">
    <h3 class="text-center">Other Details</h3>
    <table style="width:100%" id="other-detail">
        <tbody>
        @if($Header->approvedby != null)
            @if($Header->rejected_at == null)
            <tr>
                <th class="control-label col-md-2">{{ trans('message.form-label.approved_by') }}:</th>
                <td class="col-md-4">{{$Header->approvedby}} / {{$Header->approved_at}}</td>   
            </tr>
            @else
            <tr>
                <th class="control-label col-md-2">Rejected By:</th>
                <td class="col-md-4">{{$Header->approvedby}} / {{$Header->rejected_at}}</td>   
            </tr>
            @endif
        @endif
            @if($Header->approver_comments != null)
                <tr>
                    <th class="control-label col-md-2">{{ trans('message.table.approver_comments') }}:</th>
                    <td class="col-md-4">{{$Header->approver_comments}}</td>
                </tr>
            @endif
           
            <tr>
                <th class="control-label col-md-2">Status:</th>
                <td class="col-md-4">{{$Header->status_description}}</td>
            </tr>
         
            @if( $Header->processedby != null )
                <tr>
                    <th class="control-label col-md-2">{{ trans('message.form-label.processed_by') }}:</th>
                    <td class="col-md-4">{{$Header->processedby}} / {{$Header->processed_at}}</td>
                </tr>
            @endif
            @if( $Header->closedby != null )
                <tr>
                    <th class="control-label col-md-2">{{ trans('message.form-label.closed_by') }}:</th>
                    <td class="col-md-4">{{$Header->closedby}} / {{$Header->closed_at}}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>