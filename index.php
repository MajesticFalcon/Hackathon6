<?php
  require_once 'vendor/autoload.php';

    $loader = new Twig_Loader_Filesystem('templates/');
    $twig = new Twig_Environment($loader);

	require_once($_SERVER['DOCUMENT_ROOT']."/common/include.php");

    // instantiate the App object
    $app = new \Slim\Slim();
	
	
    // Add route callbacks
    $app->get('/', function () use ($twig, $profile) {
		echo $twig->render('index.html.twig', array());
    });
	$app->get('/profile/:id', function ($id) use ($twig) {
		$profile = get_profile($id);
        echo $twig->render('ProfileEdit.html.twig', array('profile' => $profile));
    });
    $app->get('/program', function () use ($twig) {
        echo $twig->render('program.html');
    });
	$app->post('/profile/:id', function ($id) use ($twig) {
		print_pre("Information Entered Completely");
        echo $twig->render('ProfileEdit.html.twig', array());
    });
	$app->get('/search', function () use ($twig) {
        echo $twig->render('search.html.twig', array());
    });
	$app->post('/', function () use ($twig) {
       // jump("/");
    });

    // Run application
    $app->run();


	// print_pre($_POST);


	if ($_POST['Action'] == 'Login'){
		//Im going to let the designers worry about if the fields have been entered
		$crypted_pass = "";
		$crypted_pass = crypt(safe_value($_POST['password']),'$2a$09$WHYAMISTORINGTHISSALTPLAINLYINSOURCECODE?$');
		$result = id_q("SELECT * from hackathon.users WHERE `username` = '".safe_value($_POST['username'])."' and `password` = '".$crypted_pass."'");
		if (count($result) > 0){
			$logged_in_user = $_POST['username'];
			print_pre("Your logged in as ".$logged_in_user."");
			if ($result['registered'] == 1){
				jump("org/search.php");
			}else{
				jump("profile/".$result['id']);
			}
		}else{
			print_pre("Incorrect Login");
			echo $twig->render('index.html.twig', array());
		}
		// print_pre(safe_value('s'));
	}
?>


