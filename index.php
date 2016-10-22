<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/common/include.php");

	// print_pre($_POST);
	
	if ($_POST['Action'] == 'Login'){
		//Im going to let the designers worry about if the fields have been entered
		crypt(safe_value($_POST['password']),'$2a$09$anexamplestringforsalt$');
	}
		print_pre("DS");
		print_pre("DS2");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>St. Louis Service Share</title>

    <!-- Bootstrap core CSS
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template 
    <link href="dist/css/signin.css" rel="stylesheet">
	-->
  </head>

  <body>
  
  <nav class="navbar navbar-fixed-top navbar-dark bg-inverse">
  <div class="nav-wrap">
      <a class="navbar-brand" href="#"><img src="dist/images/service-share.jpg" width="80" height="80" alt=""/>Service Share</a>
      <ul class="nav navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#">Resource Map</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Update Resource Availability</a>
        </li>
      </ul></div>
    </nav>

    <div class="container">
      
   
      <div class="starter-template">
        <p class="lead">Search available resources for utility & rent assistance in the St. Louis region.</p>
      </div>
      
      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Log in for access.</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input name="username" type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <input type="submit" name="Action" value="Login" class="btn btn-lg btn-primary btn-block"></input>
        <br />
		<!-- This is beyond our current scope !-->
        <!-- <a href="forgot.html">Forgot Password?</a> !-->
      </form>

     
      
  </div> 
    <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
