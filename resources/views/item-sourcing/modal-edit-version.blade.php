<!-- Modal Versions Details -->
@push('head')
<style>
        #modal-version th,td{
            border: 1px solid rgba(000, 0, 0, .5);
            text-align: center;
        }
   
</style>
@endpush
<div class="modal fade modal" id="versionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title text-center" id="event-title">Edit Logs</h4>
        </div>
        <div class="modal-body modalBody">
            <div class="row" style="padding:30px; padding-top:0;">             
                <table class="table" id="modal-version" style="overflow-x: scroll;">
                    <tbody id="appendVersions">
 
                    </tbody>
                </table>                           
            </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>