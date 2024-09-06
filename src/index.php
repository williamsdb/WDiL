<?php

/**
 * index.php
 *
 * When did I last?
 *
 * @author     Neil Thompson <neil@spokenlikeageek.com>
 * @copyright  2024 Neil Thompson
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License v3.0
 * @link       https://github.com/williamsdb/WDiL
 * @see        https://www.spokenlikeageek.com/2023/08/02/exporting-all-wordpress-posts-to-pdf/ Blog post
 * 
 * ARGUMENTS
 *
 */

// turn off reporting of notices
error_reporting(0);
ini_set('display_errors', 0);

// session start
session_start();

// Load Composer & parameters
require 'vendor/autoload.php';
try {
    require 'config.php';
} catch (\Throwable $th) {
    die('config.php file not found. Have you renamed from config_dummy.php?');
}
require 'functions.php';

// set up Smarty
use Smarty\Smarty;

$smarty = new Smarty();

$smarty->setTemplateDir('templates');
$smarty->setCompileDir('templates_c');
$smarty->setCacheDir('cache');
$smarty->setConfigDir('configs');
$smarty->registerPlugin("modifier", "date_format_tz", "smarty_modifier_date_format_tz");

// Get the current path from the requested URL
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove leading and trailing slashes
$trimmed_path = trim($current_path, '/');

// Split the path into segments
$path_segments = explode('/', $trimmed_path);

// Get the first segment, which is the command, followed by the rule id and then the action id
$cmd = $path_segments[0];
if (isset($path_segments[1])){
    $id = $path_segments[1];
}
if (isset($path_segments[2])){
    $act = $path_segments[2];
}

// are we logged in?
If (!isset($_SESSION['database']) && $cmd != 'login' &&  $cmd != 'loginUser' && $cmd != 'register' &&  $cmd != 'registerUser'){
    Header('Location: /login');
    die;
}

// any error or information messages
if (!empty($_SESSION['error'])) {
    $smarty->assign('error', $_SESSION['error']);
    unset($_SESSION['error']);
}

// execute command
switch ($cmd) {

    case 'database':

        $smarty->assign('list', array_to_html($_SESSION['activities'], TRUE));
        $smarty->display('list.tpl');
        die;

        break;

    case 'clearcache':

        session_destroy();
		Header('Location: /');
        die;

        break;

    case 'log':

        $log = file_get_contents("./logs.db");
        $logarr = explode("\n", $log);
        $log = [];
        $i = count($logarr);
        $j = 0;
        while ($i >= 0 && $j <= 100){
            if (!empty($logarr[$i])){
                $t = explode(",", $logarr[$i]);
                $log[$j]['date'] = $t[0];
                $log[$j]['entry'] = trim($t[1], "\"");    
                $j++;
            }
            $i--;
        }

        $smarty->assign('log', $log);
        $smarty->display('log.tpl');
        die;

        break;

    case 'addActivity':

        $smarty->display('addActivity.tpl');
        break;

    case 'createActivity':

        if (!isset($_SESSION['activities']) || !is_array($_SESSION['activities'])) {
            $_SESSION['activities'] = array();
            $_SESSION['activities'][0]['activityName'] = $_REQUEST['activityName'];
            $_SESSION['activities'][0]['period'] = $_REQUEST['period'];
            $_SESSION['activities'][0]['triggers'] = array();

        }else{
            $i = count($_SESSION['activities']);
            $_SESSION['activities'][$i]['activityName'] = $_REQUEST['activityName'];
            $_SESSION['activities'][$i]['period'] = $_REQUEST['period'];
            $_SESSION['activities'][$i]['triggers'] = array();
        }

        // store the activities in the activities database file
        writeActivities($_SESSION['activities'], $_SESSION['database']);
        $i = count($_SESSION['activities'])-1;

        // Redirect to the relevant page
        $_SESSION['error'] = 'Activity created';
		Header('Location: /');

        break;

    case 'editActivity':

        $smarty->assign('activityName', $_SESSION['activities'][$id]['activityName']);
        $smarty->assign('id', $id);
        $smarty->display('editActivity.tpl');
        break;

    case 'updateActivity':

        $_SESSION['activities'][$id]['activityName'] = $_REQUEST['activityName'];

        // store the activities in the activities database file
        writeActivities($_SESSION['activities'], $_SESSION['database']);

        // Redirect to the relevant page
        $_SESSION['error'] = 'Activity updated';
		Header('Location: /');

        break;

    case 'triggerActivity':

        $i = count($_SESSION['activities'][$id]['triggers']);
        $_SESSION['activities'][$id]['triggers'][$i]['timestamp'] = time();

        // store the activities in the activities database file
        writeActivities($_SESSION['activities'], $_SESSION['database']);

        // Redirect to the relevant page
        $_SESSION['error'] = 'Activity triggered';
		Header('Location: /');

        break;

    case 'deleteActivity':

        // delete the rule
        unset($_SESSION['activities'][$id]);
        $_SESSION['activities'] = array_values($_SESSION['activities']);

        // store the activities in the activities database file
        writeActivities($_SESSION['activities'], $_SESSION['database']);

        $smarty->assign('error', 'Activity deleted');
        $smarty->assign('activities', $_SESSION['activities']);
        $smarty->display('home.tpl');

        break;

    case 'statsActivity':
        
        // Do we have enough data for some stats?
        if (count($_SESSION['activities'][$id]['triggers'])>1){
            // Calculate intervals between consecutive timestamps
            $intervals = [];
            for ($i = 1; $i < count($_SESSION['activities'][$id]['triggers']); $i++) {
                $intervals[] = $_SESSION['activities'][$id]['triggers'][$i]['timestamp'] - $_SESSION['activities'][$id]['triggers'][$i - 1]['timestamp'];
            }
            
            // Calculate the average interval
            $averageInterval = array_sum($intervals) / count($intervals);

            // Find the largest interval
            $largestInterval = max($intervals);

            $smarty->assign('avg', formatTime($averageInterval));
            $smarty->assign('lrg', formatTime($largestInterval));
        }else{
            $smarty->assign('avg', 'Not enough data');
            $smarty->assign('lrg', 'Not enough data');    
        }

        // What's the elapsed time?
        $smarty->assign('elp', formatTime(time() - $_SESSION['activities'][$id]['triggers'][count($_SESSION['activities'][$id]['triggers']) - 1]['timestamp']));

        $smarty->assign('activityName', $_SESSION['activities'][$id]['activityName']);
        $smarty->assign('activities', $_SESSION['activities'][$id]);
        $smarty->assign('triggers', $_SESSION['activities'][$id]['triggers']);
        $smarty->assign('id', $id);
        $smarty->display('statsActivity.tpl');
        break;

    case 'login':

        $smarty->display('login.tpl');
        break;

    case 'loginUser':

        // if user found set database
        $users = readUsers();
        $id = searchUsername($users, $_REQUEST['username']);

        if ($id == -1){
            $smarty->assign('error', 'Account not found');
            $smarty->display('login.tpl');    
        }else{
            // account found so check password
            if (password_verify($_REQUEST['password'], $users[$id]['password'])) {
                $_SESSION['database'] = $users[$id]['username'].'.db';
                $_SESSION['activities'] = readActivities($_SESSION['database']);
                Header('Location: /');   
            } else {
                $smarty->assign('error', 'Incorrect username or password');
                $smarty->display('login.tpl');    
            }
        }
        break;

    case 'register':

        $smarty->display('register.tpl');
        break;

    case 'registerUser':

        $users = readUsers();

        // does the username or password already exist
        $uid = searchUsername($users, $_REQUEST['username']);
        $eid = searchEmail($users, $_REQUEST['email']);

        if ($uid !== -1 || $eid !== -1){
            $smarty->assign('error', 'Username or email address already exists');
            $smarty->display('login.tpl');    
        }

        // get the password hash
        $pwd = password_hash($_REQUEST['password'], PASSWORD_DEFAULT);

        if (!isset($users) || !is_array($users)) {
            $users = array();
            $users[0]['email'] = $_REQUEST['email'];
            $users[0]['username'] = $_REQUEST['username'];
            $users[0]['password'] = $pwd;
            file_put_contents('./databases/'.$_REQUEST['username'].'.db','');
        }else{
            $i = count($users);
            $users[$i]['email'] = $_REQUEST['email'];
            $users[$i]['username'] = $_REQUEST['username'];
            $users[$i]['password'] = $pwd;
            file_put_contents('./databases/'.$_REQUEST['username'].'.db','');
        }
        writeUsers($users);
        $smarty->assign('error', 'User created. Please login');
        $smarty->display('login.tpl');
        break;

    case 'logout':
        session_destroy();
        $smarty->display('login.tpl');
        break;

    case '':
        $smarty->assign('activities', $_SESSION['activities']);
        $smarty->display('home.tpl');
        break;

    default:
        # command not recognised
        $smarty->assign('error', 'Command not recognised');
        $smarty->assign('activities', $_SESSION['activities']);
        $smarty->display('home.tpl');
        break;
}

function smarty_modifier_date_format_tz($input, $format = "Y-m-d H:i:s", $timezone = 'UTC') {
    try {
        if ($input instanceof DateTime) {
            // If $input is already a DateTime object, use it directly
            $dateTime = $input;
        } else {
            // Assume $input is a Unix timestamp
            $dateTime = new DateTime();
            $dateTime->setTimestamp((int)$input); // Cast to int to avoid errors
        }

        // Set the timezone
        $dateTime->setTimezone(new DateTimeZone($timezone));

        // Return the formatted date
        return $dateTime->format($format);
    } catch (Exception $e) {
        // Handle any exceptions, e.g., invalid timezone or timestamp
        return '';
    }
}

function formatTime($seconds) {
    $minutes = $seconds / 60;
    $hours = $minutes / 60;
    $days = $hours / 24;
    $months = $days / 30.44; // approximate months
    $years = $days / 365.25; // approximate years

    if ($years >= 1) {
        return round($years, 2) . ' years';
    } elseif ($months >= 1) {
        return round($months, 2) . ' months';
    } elseif ($days >= 1) {
        return round($days, 2) . ' days';
    } elseif ($hours >= 1) {
        return round($hours, 2) . ' hours';
    } else {
        return round($minutes, 2) . ' minutes';
    }
}


?>