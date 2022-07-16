<?php
require_once(__DIR__.'/../lib/Concit/MyErrorException.php');

function error2exception($code, $message, $file, $line) {
	return new MyErrorException($message, 0, $code, $file, $line);
}

function customException($e) {
	echo 'Ligne : ' , $e->getLine(), ' dans le fichier ', $e->getFile() . '<strong>Exception lancée</strong> : ' , $e->getMessage();
}

set_error_handler('error2exception');
set_exception_handler('customException');

const DEFAULT_APP = 'Frontend';

if(!isset($_GET['app']) || !file_exists(__DIR__.'/../App/'.$_GET['app'].'/'.$_GET['app'].'Application.php'))
	$_GET['app'] = DEFAUTL_APP;
	
// j'inclus le fichier permettant d'enregistrer les autoloads
require __DIR__.'/../lib/Concit/SplClassLoader.php';

// j'enregistre ensuite les autoloads correspondant à chaque vendor
$concitLoader = new SplClassLoader('Concit', __DIR__.'/../lib');
$concitLoader->register();

$entityLoader = new SplClassLoader('Entity', __DIR__.'/../lib/vendors');
$entityLoader->register();

$modelLoader = new SplClassLoader('Model', __DIR__.'/../lib/vendors');
$modelLoader->register();

$appLoader = new SplClassLoader('App', __DIR__.'/..');
$appLoader->register();

$formBuilderLoader = new SplClassLoader('FormBuilder', __DIR__.'/../lib/vendors');
$formBuilderLoader->register();

$validatorLoader = new SplClassLoader('Validator', __DIR__.'/../lib/vendors');
$validatorLoader->register();

$symfonyComponentLoader = new SplClassLoader('Yaml', __DIR__.'/../lib/vendors');
$symfonyComponentLoader->register();

$appClass = 'App\\'.$_GET['app'].'\\'.$_GET['app'].'Application';
$app = new $appClass();
$app->run();