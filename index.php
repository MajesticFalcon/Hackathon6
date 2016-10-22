<?php
  require_once 'vendor/autoload.php';

    $loader = new Twig_Loader_Filesystem('templates/');
    $twig = new Twig_Environment($loader);

	require_once($_SERVER['DOCUMENT_ROOT']."/common/include.php");

    // instantiate the App object
    $app = new \Slim\Slim();

    // Add route callbacks
    $app->get('/', function () use ($twig) {
        echo $twig->render('index.html.twig', array());
    });

    // Run application
    $app->run();


	// print_pre($_POST);

	if ($logged_in_user != ''){
		jump("org/search.php");
	}
	
	if ($_POST['Action'] == 'Login'){
		//Im going to let the designers worry about if the fields have been entered
		$crypted_pass = "";
		$crypted_pass = crypt(safe_value($_POST['password']),'$2a$09$WHYAMISTORINGTHISSALTPLAINLYINSOURCECODE?$');
		$result = select_q("SELECT * from hackathon.users WHERE `username` = '".safe_value($_POST['username'])."' and `password` = '".$crypted_pass."'");
		if (count($result) > 0){
			$logged_in_user = $_POST['username'];
			print_pre("Your logged in as ".$logged_in_user."");
			jump("/org/search.php");
		}
		// print_pre(safe_value('s'));
	}
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

