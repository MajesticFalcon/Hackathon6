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

	if ($_POST['Action'] == 'Login'){
		//Im going to let the designers worry about if the fields have been entered
		$crypted_pass = "";
		$crypted_pass = crypt(safe_value($_POST['password']),'$2a$09$WHYAMISTORINGTHISSALTPLAINLYINSOURCECODE?$');
		$result = select_q("SELECT * from hackathon.users WHERE `username` = '".safe_value($_POST['username'])."' and `password` = '".$crypted_pass."'");
		print_pre("SELECT * from hackathon.users WHERE `username` = '".safe_value($_POST['username'])."' and `password` = '".$crypted_pass."'");
		// print_pre(safe_value('s'));
	}
?>

