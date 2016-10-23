<?php
session_start();

require_once 'vendor/autoload.php';

$loader = new Twig_Loader_Filesystem('templates/');
$twig = new Twig_Environment($loader);
$twig->addGlobal('session', $_SESSION);

require_once($_SERVER['DOCUMENT_ROOT'] . "/common/include.php");

// instantiate the App object
$app = new \Slim\Slim();

/** Check if a user is logged in */
$app->get('/:method', function ($request_uri) use ($twig, $app) {
    if (preg_match("/register/", $request_uri)) {
        $app->pass();
    }
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        $alert = 'You must be logged in to access all system functionality.';
        echo $twig->render('index.html.twig', array('alert' => $alert));
    } else {
        $app->pass();
    }
})->conditions(array('method' => '.+'));

// Add route callbacks
$app->get('/', function () use ($twig, $profile) {
    echo $twig->render('index.html.twig', array());
});


/** REGISTER **/
$app->get('/register', function () use ($twig) {
    echo $twig->render('register.html.twig', array());
});
$app->post('/register', function () use ($twig) {
    $user = new Hackathon\User($_POST);
    if ($user->verifyUser($_POST)) {
        $_SESSION['logged_in'] = true;
        $p_id = $user->insertUser($user->getUser());
        jump("profile/" . $result['p_id']);
    } else {
        echo $twig->render('register.html.twig', array('user' => $_POST, 'errors' => $user->getErrors()));
    }
});

/** PROFILE */
$app->get('/profile', function () use ($twig) {
    echo $twig->render('ProfileEdit.html.twig');
});

$app->post('/profile', function () use ($twig) {
    $provider = new Hackathon\Provider();
    if ($provider->verifyProvider($_POST)) {
        $provider->insertProvider($_POST);
        jump('program');
    } else {
        echo $twig->render('ProfileEdit.html.twig', array('provider' => $_POST, 'errors' => $provider->getErrors()));
    }
});

$app->get('/profile/:id', function ($id) use ($twig) {
    $profile = get_profile($id);
    echo $twig->render('ProfileEdit.html.twig', array('profile' => $profile));
});

$app->post('/profile/:id', function ($id) use ($twig) {
    // TODO if logged in user owns provider
    $provider = new Hackathon\Provider();
    if (!isset($_POST['coc'])) {
        $_POST['coc'] = 1;
    }
    if (!$provider->verifyProvider($_POST)) {
        echo $twig->render('ProfileEdit.html.twig', array('profile' => $_POST, 'errors' => $provider->getErrors()));
    } else {
        $provider->updateProvider($id, $_POST);
        $alert = 'Your provider has been successfully updated.';
        echo $twig->render('ProfileEdit.html.twig', array('profile' => $_POST, 'alert' => $alert));
    }
});

/** PROGRAM **/
$app->get('/program', function () use ($twig) {
    $program = new Hackathon\Program();
    $user = new Hackathon\User();
    $userId = $_SESSION['user_id'];
    $userArr = $user->fetchUser($userId);
    $programs = $program->getProgramsForProvider($userArr['p_id']);
    echo $twig->render('program.html', array('user_programs' => $programs) );
});

$app->get('/program/:id', function ($id) use ($twig) {
    $program = new Hackathon\Program();
    $record = $program->getProgramRecord($id);
    $requirements = array();
    foreach ($record['requirements'] as $requirement) {
        $requirements[$requirement] = 1;
    }
    $record['requirements'] = $requirements;
    $twig->render('program.html', array('program' => $record));
});

$app->post('/program', function () use ($twig) {
    $program = new Hackathon\Program($_POST);
    // Joe use this $program
    $programArray = $program->getProgram();
    $programRequirements = $program->getProgramRequirements();
    $programZips = $program->getZipcodes();

    $result = array_merge($programArray, $programRequirements, $programZips);
    $program->insertProgram($result);
    exit;
});

$app->get('/program/update/:id', function ($id) use ($twig) {
    echo $twig->render('program.html');
});

/** SEARCH **/
$app->get('/search', function () use ($twig) {
    //select col gets a reg array of unamed values
    $zip_codes = select_col("select zip from zip_code", "zip");
    foreach ($zip_codes as $zip_code) {
        // print_pre($zip_code);
    }
    echo $twig->render('search.html.twig', array('zip_codes' => $zip_codes));
});

$app->post('/search', function () use ($twig) {
    // print_pre($_POST);
    $requires = array();
	// print_pre($_POST);
	// die();
    //This is gross O(n) queries You should be ashamed!
    foreach ($_POST['requirements'] as $requirement) {
        // print_pre("SD");
        array_push($requires, id_q("SELECT uuid FROM hackathon.service_requirement WHERE `ack` = '" . safe_value($requirement) . "'"));
    }
	$sql = "";
	if (count($requires) >= 12){
		print_pre("ALL");
		$sql = ("SELECT * FROM program join program_link_service_requirement on (program.uuid = program_link_service_requirement.program_uuid) join service_requirement on (service_requirement_uuid = program_link_service_requirement.service_requirement_uuid) WHERE `budget` <= " . safe_value($_POST['budget']) . "  ");
	}else{
		print_pre("some");
		$sql = ("SELECT * FROM program join program_link_service_requirement on (program.uuid = program_link_service_requirement.program_uuid) join service_requirement on (service_requirement_uuid = program_link_service_requirement.service_requirement_uuid) WHERE `service_requirement_uuid` = '" . safe_value($requirement) . "' AND `budget` <= " . safe_value($_POST['budget']) . "  ");
	}
    // print_pre($requires);
    // die();
	print_pre($sql);
	$a = select_q($sql);
	
    echo $twig->render('results.html.twig', array('agencies' => $a));

    // die();
    // select_q("SELECT uuid FROM hackathon.service_requirement WHERE `name")
    // echo $twig->render('search.html.twig');
});

$app->get('/logout', function () use ($twig) {
    $_SESSION['logged_in'] = false;
    jump('');
});

$app->post('/', function () use ($twig) {
    if ($_POST['Action'] == 'Register') {
        jump("register");
    }
    //Im going to let the designers worry about if the fields have been entered
    $crypted_pass = "";
    $crypted_pass = crypt(safe_value($_POST['password']), '$2a$09$WHYAMISTORINGTHISSALTPLAINLYINSOURCECODE?$');
    $result = id_q("SELECT * from hackathon.users WHERE `username` = '" . safe_value($_POST['username']) . "' and `password` = '" . $crypted_pass . "'");
    if (count($result) > 0) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $result['id'];
        $logged_in_user = $_POST['username'];
        print_pre("Your logged in as " . $logged_in_user . "");
        if ($result['registered'] == 1) {
            jump("search");
        } else {
            jump("profile/".$result['p_id']);
        }
    } else {
        $error = 'Your username or password does not match the information we have on file.';
        echo $twig->render('index.html.twig', array('error' => $error));
    }
});

$app->get('/search', function () use ($twig) {
    echo $twig->render('search.html.twig', array());
});

$app->post('/', function () use ($twig) {
    // jump("/");
});

// Run application
$app->run();
?>


