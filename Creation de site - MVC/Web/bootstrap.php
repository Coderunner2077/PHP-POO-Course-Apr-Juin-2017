<?php 
require_once(__DIR__.'/../lib/OCFram/MyErrorException.php');

function error2exception($code, $message, $file, $line) {
	throw new MyErrorException($message, 0, $code, $file, $line);
}

function customException($e) {
	echo 'Ligne : ' , $e->getLine() , ' dans le fichier ', $e->getFile().'<strong>Exception lancée</strong> : ' , $e->getMessage();
}

set_error_handler('error2exception');
set_exception_handler('customException');

const DEFAULT_APP = 'Frontend';

// Si le nom de l'application n'est pas définie, alors on charge l'application par défaut 
if(!isset($_GET['app']) || !file_exists(__DIR__.'/../App/'.$_GET['app']))
	$_GET['app'] = DEFAUTL_APP;

// On inclut le fichier permettant d'enregistrer les autoload 
require __DIR__.'/../lib/OCFram/SplClassLoader.php';

// On va ensuite enregistrer les autoload corrspondant à chaque vendor
$OCFramLoader = new SplClassLoader('OCFram', __DIR__.'/../lib');
$OCFramLoader->register();

$AppLoader = new SplClassLoader('App', __DIR__.'/..');
$AppLoader->register();

$modelLoader = new SplClassLoader('Model', __DIR__.'/../lib/vendors');
$modelLoader->register();

$entityLoader = new SplClassLoader('Entity', __DIR__.'/../lib/vendors');
$entityLoader->register();

// Il ne reste plus qu'à déduire le nom de la classe et de l'instancier
$appClass = 'App\\'.$_GET['app'].'\\'.$_GET['app'].'Application';
$app = new $appClass;
$app->run();

?>