<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        body{
          margin-top: 150px;
          background-color: #C4CCD9;
        }
        .error-main{
          background-color: #fff;
          box-shadow: 0px 10px 10px -10px #5D6572;
        }
        .error-main h1{
          font-weight: bold;
          color: #444444;
          font-size: 150px;
          text-shadow: 2px 4px 5px #6E6E6E;
        }
        .error-main h6{
          color: #42494F;
          font-size: 20px;
        }
        .error-main p{
          color: #050505;
          font-size: 15px; 
        }
    </style>
</head>
<body>
  
    <div class="container">
      <div class="row text-center">
        <div class="col-lg-6 offset-lg-3 col-sm-6 offset-sm-3 col-12 p-3 error-main">
          <div class="row">
            <div class="col-lg-8 col-12 col-sm-10 offset-lg-2 offset-sm-1">
              <img src="{{asset('img/stop-order.gif')}}" alt="Warning" class="text-center" width="200" height="200">
              <h4>This transaction currently used by <span class="text-info">{{$user->current_user}} </span></h4>
              <p><span>ERF Module - DAM</span></p>
            </div>
          </div>
        </div>
      </div>
    </div>
      
</body>
</html>