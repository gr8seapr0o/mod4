<?php
/**
 * home file /
 * here we have only App::run /
 * define DS /
 * define ROOT /
 * define VIEW_PATH /
 */

/*ini_set('display_errors', 1);
error_reporting(E_ALL);*/

// set separator for directory separation
define('DS',DIRECTORY_SEPARATOR);

// ROOT указывает на 2 уровня высше чем index.php, то есть на 08_4_Modul, он нужен для включения файлов
define('ROOT',dirname(dirname(__FILE__)));

// constant for the path to the views
define('VIEWS_PATH',ROOT.DS.'views');

// connect init.php with __autoload() action
require_once (ROOT.DS.'lib'.DS.'init.php');


// start session
session_start();

App::run($_SERVER['REQUEST_URI']);