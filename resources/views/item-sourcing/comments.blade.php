<div class="col-md-6">
    <h3 class="text-center">Chat Box</h3>
    <div class="chat">     
            <div class="body-comment">                     
                @foreach($comments as $key => $comment)
                        @if(CRUDBooster::myId() == $comment->user_id)
                        <strong style="margin-left: 95%">Me</strong>
                            <span class="session-comment">
                                <p style="word-wrap: break-word; padding:5px;"><span class="comment">{{ $comment->comments }} </span> </p>
                                <p style="text-align:right; font-size:11px; font-style: italic; padding-right:5px;"> {{ $comment->created_at }} </p>      
                            </span>  
                        @else
                            <strong style="margin-left:10px">{{ $comment->name }}</strong>
                            <span class="text-comment">
                                <p style="word-wrap: break-word; padding:5px;"><span class="comment">{{ $comment->comments }} </span> </p>
                                <p style="text-align:right; font-size:11px; font-style: italic; padding-right:5px;"> {{ $comment->created_at }} </p>      
                            </span>   
                            
                        @endif           
                    <div class="new-body-comment">

                    </div>
                @endforeach       
            </div>                
    </div>
    <div class="send">
        <textarea oninput="validate(this)" class="form-control" placeholder="Message ..." name="message" id="message" style="width:100%"></textarea> <button type="button" class="btn btn-primary btnChat" id="btnChat"><i class="fa fa-send icon-send"></i></button> 
    </div> 
</div>