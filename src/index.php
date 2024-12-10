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
 * @see        https://www.spokenlikeageek.com/tag/when-did-i-last/ Blog post
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
require __DIR__.'/vendor/autoload.php';
try {
    require __DIR__.'/config.php';
} catch (\Throwable $th) {
    die('config.php file not found. Have you renamed from config_dummy.php?');
}
require __DIR__.'/functions.php';

// set up namespaces
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

// Get the first segment, which is the command, followed by the activity id and then the action id
$cmd = $path_segments[0];
if (isset($path_segments[1])){
    $id = $path_segments[1];
}
if (isset($path_segments[2])){
    $act = $path_segments[2];
}

// are we logged in?
If (!isset($_SESSION['database']) && $cmd != 'login' &&  $cmd != 'loginUser' && $cmd != 'register' &&  $cmd != 'registerUser' &&  $cmd != 'forgot' &&  $cmd != 'forgotPass' &&  $cmd != 'resetPassword' &&  $cmd != 'changePassword'){
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
        $smarty->assign('header', 'Activities Database');
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

        // is this the first activity?
        if (!isset($_SESSION['activities']) || !is_array($_SESSION['activities'])) {
            $_SESSION['activities'] = array();
            $_SESSION['activities'][0]['activityName'] = $_REQUEST['activityName'];
            $_SESSION['activities'][0]['period'] = $_REQUEST['period'];
            $_SESSION['activities'][0]['activityColour'] = $_REQUEST['color'];
            $_SESSION['activities'][0]['triggers'] = array();
            if (isset($_REQUEST['notifications'])){
                $_SESSION['activities'][0]['notifications'] = TRUE; 
            }else{
                $_SESSION['activities'][0]['notifications'] = FALSE;
            }
            $_SESSION['activities'][0]['notificationTriggered'] = FALSE;

        }else{
            $i = count($_SESSION['activities']);
            $_SESSION['activities'][$i]['activityName'] = $_REQUEST['activityName'];
            $_SESSION['activities'][$i]['period'] = $_REQUEST['period'];
            $_SESSION['activities'][$i]['activityColour'] = $_REQUEST['color'];
            $_SESSION['activities'][$i]['triggers'] = array();
            if (isset($_REQUEST['notifications'])){
                $_SESSION['activities'][$i]['notifications'] = TRUE; 
            }else{
                $_SESSION['activities'][$i]['notifications'] = FALSE;
            }
            $_SESSION['activities'][$i]['notificationTriggered'] = FALSE;
        }

        // store the activities in the activities database file
        writeActivities($_SESSION['activities'], $_SESSION['database']);
        $i = count($_SESSION['activities'])-1;

        // Redirect to the relevant page
        $_SESSION['error'] = 'Activity created';
		Header('Location: /');

        break;

    case 'editActivity':

        if ($id > count($_SESSION['activities'])-1){
            Header('Location: /');
            die;
        }
        $smarty->assign('activityName', $_SESSION['activities'][$id]['activityName']);
        if (isset($_SESSION['activities'][$id]['activityColour'])){
            $smarty->assign('activityColour', $_SESSION['activities'][$id]['activityColour']);
        }
        if (isset($_SESSION['activities'][$id]['notifications']) && $_SESSION['activities'][$id]['notifications'] == TRUE){
            $smarty->assign('notifications', TRUE); 
        }else{
            $smarty->assign('notifications', FALSE); 
        }
        $smarty->assign('id', $id);
        $smarty->display('editActivity.tpl');
        break;

    case 'updateActivity':

        $_SESSION['activities'][$id]['activityName'] = $_REQUEST['activityName'];
        $_SESSION['activities'][$id]['activityColour'] = $_REQUEST['color'];
        if (isset($_REQUEST['notifications'])){
            $_SESSION['activities'][$id]['notifications'] = TRUE;
        }else{
            $_SESSION['activities'][$id]['notifications'] = FALSE;
        }

        // store the activities in the activities database file
        writeActivities($_SESSION['activities'], $_SESSION['database']);

        // Redirect to the relevant page
        $_SESSION['error'] = 'Activity updated';
		Header('Location: /');

        break;

    case 'triggerActivity':

        if (!isset($_REQUEST) || empty($_REQUEST)){
            Header('Location: /');
            die;
        }

        if ($_REQUEST['activityId'] > count($_SESSION['activities'])-1){
            Header('Location: /');
            die;
        }

        // convert time to GMT/UTC and add to array
        $i = count($_SESSION['activities'][$_REQUEST['activityId']]['triggers']);
        $timezoneOffset = timezone_offset_get(new DateTimeZone(TZ), new DateTime());
        $_SESSION['activities'][$_REQUEST['activityId']]['triggers'][$i]['timestamp'] = strtotime($_REQUEST['dateTime'])-$timezoneOffset;
        $_SESSION['activities'][$_REQUEST['activityId']]['triggers'][$i]['comment'] = $_REQUEST['comment'];
        // reset the notification
        $_SESSION['activities'][$_REQUEST['activityId']]['notificationTriggered'] = FALSE;

        // sort the array into time order
        usort($_SESSION['activities'][$_REQUEST['activityId']]['triggers'], function ($a, $b) {
            return $a['timestamp'] <=> $b['timestamp']; 
        });

        // store the activities in the activities database file
        writeActivities($_SESSION['activities'], $_SESSION['database']);

        // Redirect to the relevant page
        $_SESSION['error'] = 'Activity triggered';
        if ($_REQUEST['redirectTo']=='stats'){
            Header('Location: /statsActivity/'.$_REQUEST['activityId']);
        }else{
            Header('Location: /');
        }

        break;

    case 'archiveActivity':

        // does the id exist?
        if ($id>=count($_SESSION['activities'])){
            Header('Location: /');
        }

        if (!isset($_SESSION['activities'][$id]['archived']) || $_SESSION['activities'][$id]['archived']==0){
            $_SESSION['activities'][$id]['archived'] = 1;
        }else{
            $_SESSION['activities'][$id]['archived'] = 0;
        }

        // store the activities in the activities database file
        writeActivities($_SESSION['activities'], $_SESSION['database']);

        if ($_SESSION['activities'][$id]['archived'] == 1){
            $_SESSION['error'] = 'Activity archived';
        }else{
            $_SESSION['error'] = 'Activity restored';
        }
        Header('Location: /');

        break;

    case 'deleteActivity':

        // does the id exist?
        if ($id>=count($_SESSION['activities'])){
            Header('Location: /');
        }

        // delete the activity
        unset($_SESSION['activities'][$id]);
        $_SESSION['activities'] = array_values($_SESSION['activities']);

        // store the activities in the activities database file
        writeActivities($_SESSION['activities'], $_SESSION['database']);

        $_SESSION['error'] = 'Activity deleted';
        Header('Location: /');

        break;

    case 'deleteTrigger':

        // delete the trigger
        unset($_SESSION['activities'][$id]['triggers'][$act]);
        // reindex the array
        $_SESSION['activities'][$id]['triggers'] = array_values($_SESSION['activities'][$id]['triggers']);

        // store the activities in the activities database file
        writeActivities($_SESSION['activities'], $_SESSION['database']);

        $smarty->assign('error', 'Trigger deleted');
        Header('Location: /statsActivity/'.$id);

        break;

    case 'statsActivity':

        // does the id exist?
        if ($id>=count($_SESSION['activities'])){
            Header('Location: /');
        }

        $timezoneOffset = timezone_offset_get(new DateTimeZone(TZ), new DateTime());
        $localTimestamp = $_SESSION['activities'][$id]['triggers'][count($_SESSION['activities'][$id]['triggers']) - 1]['timestamp'];

        // Do we have enough data for some stats?
        if (count($_SESSION['activities'][$id]['triggers'])>1){
            // Calculate intervals between consecutive timestamps
            $intervals = [];
            $labels = [];
            $data = [];
            for ($i = 1; $i < count($_SESSION['activities'][$id]['triggers']); $i++) {
                $intervals[] = $_SESSION['activities'][$id]['triggers'][$i]['timestamp'] - $_SESSION['activities'][$id]['triggers'][$i - 1]['timestamp'];
                $labels[] = smarty_modifier_date_format_tz($_SESSION['activities'][$id]['triggers'][$i]['timestamp']);
                $data[] = formatTime($intervals[$i-1], 0, TRUE);
            }

            $smarty->assign('labels', json_encode($labels));
            $smarty->assign('data', json_encode($intervals));

            // Generate x values as indices for the intervals (1, 2, 3, ...)
            $x_values = range(1, count($intervals));
            
            // Perform linear regression on the intervals
            $result = linear_regression($x_values, $intervals);
            $slope = $result['slope'];
            
            // Determine the trend based on the slope
            if ($slope > 0) {
                $smarty->assign('trend', "The intervals are generally increasing");
            } elseif ($slope < 0) {
                $smarty->assign('trend', "The intervals are generally decreasing");
            } else {
                $smarty->assign('trend', "The intervals are roughly constant");
            }

            // Calculate the average interval
            $averageInterval = array_sum($intervals) / count($intervals);

            // Find the largest interval
            $largestInterval = max($intervals);

            // calculate the next trigger
            $lastTrigger = $_SESSION['activities'][$id]['triggers'][count($_SESSION['activities'][$id]['triggers'])-1]['timestamp'];
            $enp =  $lastTrigger + $averageInterval;
            $enpt = $enp - time();

            if ($enpt < 0){
                $enpx = '<span style="color: red">overdue by '.formatTime(time()- $enp, 0).'</span>';
                $smarty->assign('enp', smarty_modifier_date_format_tz($enp, "Y-m-d H:i:s", TZ).' '.$enpx);
                $smarty->assign('enpx', $enpx);
            }else{
                $enpx = ' in '.formatTime($enpt, 0);
                $smarty->assign('enp', smarty_modifier_date_format_tz($enp, "Y-m-d H:i:s", TZ).$enpx);
                $smarty->assign('enpx', 'due '.$enpx);
            }
    
            $smarty->assign('avg', formatTime($averageInterval, 0));
            $smarty->assign('lrg', formatTime($largestInterval, 0));
            $smarty->assign('intervals', array_reverse($data, FALSE));
        }else{
            $smarty->assign('avg', 'Not enough data');
            $smarty->assign('enp', 'Not enough data');
            $smarty->assign('lrg', 'Not enough data');
            $smarty->assign('trend', 'Not enough data');
            $smarty->assign('labels', '');
            $smarty->assign('data', '');
        }

        // What's the elapsed time?
        $smarty->assign('elp', formatTime(time() - $localTimestamp, 0));

        $smarty->assign('activityName', $_SESSION['activities'][$id]['activityName']);
        $smarty->assign('activities', $_SESSION['activities'][$id]);
        $triggersReversed = array_reverse($_SESSION['activities'][$id]['triggers'], true);
        $smarty->assign('triggersReversed', $triggersReversed);
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
        if (strpos($_REQUEST['username'],'@')){
            $id = searchEmail($users, strtolower($_REQUEST['username']));
        }else{
            $id = searchUsername($users, strtolower($_REQUEST['username']));
        }

        if ($id == -1){
            $smarty->assign('error', 'Account not found');
            $smarty->display('login.tpl');    
        }else{
            // account found so check password
            if (password_verify($_REQUEST['password'], $users[$id]['password'])) {
                $_SESSION['database'] = $users[$id]['username'].'.db';
                $_SESSION['username'] = $users[$id]['username'];
                if (isset($users[$id]['show'])){
                    $_SESSION['show'] = $users[$id]['show'];
                }else{
                    $_SESSION['show'] = 1;
                }

                // reset the guid
                $users[$id]['guid'] = '';
                writeUsers($users);

                Header('Location: /');   
                die;
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

        // does the registration code match?
        if ($_REQUEST['regcode'] != REGCODE){
            $smarty->assign('error', 'Your registration code is not correct');
            $smarty->display('login.tpl');
            break;
        }

        $users = readUsers();

        // does the username or password already exist
        $uid = searchUsername($users, $_REQUEST['username']);
        $eid = searchEmail($users, $_REQUEST['email']);

        if ($uid !== -1 || $eid !== -1){
            $smarty->assign('error', 'Username or email address already exists');
            $smarty->display('login.tpl');
            break;
        }

        // get the password hash
        $pwd = password_hash($_REQUEST['password'], PASSWORD_DEFAULT);

        if (!isset($users) || !is_array($users)) {
            $users = array();
            $users[0]['email'] = $_REQUEST['email'];
            $users[0]['username'] = $_REQUEST['username'];
            $users[0]['password'] = $pwd;
            $users[0]['show'] = 1;
            $users[0]['guid'] = '';
            file_put_contents('./databases/'.$_REQUEST['username'].'.db','');
        }else{
            $i = count($users);
            $users[$i]['email'] = $_REQUEST['email'];
            $users[$i]['username'] = $_REQUEST['username'];
            $users[$i]['password'] = $pwd;
            $users[$i]['show'] = 1;
            $users[$i]['guid'] = '';
            file_put_contents('./databases/'.$_REQUEST['username'].'.db','');
        }
        writeUsers($users);
        $smarty->assign('error', 'User created. Please login');
        $smarty->display('login.tpl');
        break;

    case 'forgot':

        $smarty->display('forgot.tpl');
        break;

    case 'forgotPass':

        // if user found set database
        $users = readUsers();
        $id = searchEmail($users, $_REQUEST['email']);

        if ($id != -1){
            $uuid = uniqid();
            $data['guid'] = protocol().$_SERVER['SERVER_NAME'].'/resetPassword/'.$id.'-'.$uuid;
            $body = $smarty->fetch( 'email-forgotten-password.tpl', $data );

            // store the id
            $users[$id]['guid'] = $uuid;
            writeUsers($users);

            // account found so send email
            sendMail($_REQUEST['email'], 'Reset your password', $body);
        }

        $smarty->assign('error', 'If an account has been found an email has been sent to you with further instructions');
        $smarty->display('login.tpl');
        break;

    case 'resetPassword':

        $users = readUsers();
        $code = explode('-',$id);

        if ($code[1] != $users[$code[0]]['guid']){
            Header('Location: /');   
            die;
        }else{
            $smarty->assign('id', $code[0]);
            $smarty->assign('code', $code[1]);
            $smarty->display('resetPassword.tpl');
        }
        break;

    case 'changePassword':

        $users = readUsers();

        if ($_REQUEST['code'] != $users[$_REQUEST['id']]['guid']){
            Header('Location: /');   
            die;
        }elseif ($_REQUEST['password'] != $_REQUEST['passwordConf']){
            $smarty->assign('error', 'Passwords do not match');
            $smarty->assign('id', $_REQUEST['id']);
            $smarty->assign('code', $_REQUEST['code']);
            $smarty->display('resetPassword.tpl');
            $smarty->display('resetPassword.tpl');
            break;
        }else{
            $pwd = password_hash($_REQUEST['password'], PASSWORD_DEFAULT);
            $users[$_REQUEST['id']]['password'] = $pwd;
            $users[$_REQUEST['id']]['guid'] = '';
            writeUsers($users);
            $smarty->assign('error', 'Your password has been changed');
            $smarty->display('login.tpl');
        }

        break;

    case 'logout':
        unset($_SESSION['database']);
        unset($_SESSION['username']);
        session_destroy();
        $smarty->display('login.tpl');
        break;

    case 'admin':

        // should we be here?
        if (ADMIN != $_SESSION['username']){
            Header('Location: /');   
            die;
        }

        $smarty->assign('users', readUsers());
        $smarty->display('admin.tpl');
        break;

    case 'account':

        $users = readUsers();
        $id = searchUsername($users, $_SESSION['username']);

        $smarty->assign('email', $users[$id]['email']);
        $smarty->assign('username', $users[$id]['username']);
        if (isset($users[$id]['pushoverToken'])){
            $smarty->assign('token', $users[$id]['pushoverToken']);
        }else{
            $smarty->assign('token', '');
        }
        if (isset($users[$id]['pushoverUser'])){
            $smarty->assign('user', $users[$id]['pushoverUser']);
        }else{
            $smarty->assign('user', '');
        }
        $smarty->display('account.tpl');
        break;

    case 'updateAccount':

        $users = readUsers();
        $id = searchUsername($users, $_SESSION['username']);

        $users[$id]['pushoverToken'] = $_REQUEST['token'];
        $users[$id]['pushoverUser'] = $_REQUEST['user'];
        writeUsers($users);

        $_SESSION['error'] = 'Your account has been updated';
		Header('Location: /');

        break;

    case 'stats':

        // is there anything to produce any stats?
        if (!empty($_SESSION['activities'])){
            $total = count($_SESSION['activities']);
        }else{
            $total = 0;
        }
        $i=0;
        $totTriggered=0;
        $maxInterval=0;
        $maxIntervalStr='';
        $minInterval=99999999;
        $minIntervalStr='';
        $maxTimestamp=0;
        $maxTimestampStr='';
        $minTimestamp=9999999999;
        $minTimestampStr='';
        $notTriggered=0;
        while ($i <= $total-1) {
            // not interested if archived so ignore
            if (!isset($_SESSION['activities'][$i]['archived']) || $_SESSION['activities'][$i]['archived']==0){
                $totTriggered=$totTriggered+count($_SESSION['activities'][$i]['triggers']);
                if (count($_SESSION['activities'][$i]['triggers'])>=1){
                    // Calculate intervals between consecutive timestamps
                    $intervals = calculateIntervals($_SESSION['activities'][$i]);
                    $totTriggers=count($_SESSION['activities'][$i]['triggers']);
    
                    // Find the largest and smallest timestamps
                    if ($totTriggers > 0){
                        if ($_SESSION['activities'][$i]['triggers'][$totTriggers-1]['timestamp'] > $maxTimestamp){
                            $maxTimestampStr = $_SESSION['activities'][$i]['activityName'].' at '. smarty_modifier_date_format_tz($_SESSION['activities'][$i]['triggers'][$totTriggers-1]['timestamp'], "Y-m-d H:i:s", TZ);
                            $maxTimestamp = $_SESSION['activities'][$i]['triggers'][$totTriggers-1]['timestamp'];    
                        }
                        if ($_SESSION['activities'][$i]['triggers'][$totTriggers-1]['timestamp'] < $minTimestamp){
                            $minTimestampStr = $_SESSION['activities'][$i]['activityName'].' at '. smarty_modifier_date_format_tz($_SESSION['activities'][$i]['triggers'][$totTriggers-1]['timestamp'], 'Y-m-d H:i:s', TZ);
                            $minTimestamp = $_SESSION['activities'][$i]['triggers'][$totTriggers-1]['timestamp'];    
                        }    
                    }
        
                    // Find the largest and smallest intervals
                    if(count($_SESSION['activities'][$i]['triggers'])>1){
                        $largestInterval = max($intervals);
                        if ($largestInterval > $maxInterval){
                            $maxIntervalStr = $_SESSION['activities'][$i]['activityName'].' - '. formatTime($largestInterval, 0);
                            $maxInterval = $largestInterval;
                        } 
                        $smallestInterval = min($intervals);
                        if ($smallestInterval < $minInterval){
                            $minIntervalStr = $_SESSION['activities'][$i]['activityName'].' - '. formatTime($smallestInterval, 0);
                            $minInterval = $smallestInterval;
                        }     
                    }
                }else{
                    $notTriggered++;
                }  
            }
            $i++;
        }

        $smarty->assign('total', $total);
        $smarty->assign('totTriggered', $totTriggered);
        $smarty->assign('maxInterval', $maxIntervalStr);
        $smarty->assign('minInterval', $minIntervalStr);
        $smarty->assign('maxTimestamp', $maxTimestampStr);
        $smarty->assign('minTimestamp', $minTimestampStr);
        $smarty->assign('notTriggered', $notTriggered);
        $smarty->display('stats.tpl');
        break;

    case 'notifications':

        // load users
        $users = readUsers();

        // initialise output
        $list = '';

        // cycle through all users
        $i = 0;
        while($i < count($users)){

            // load database
            $activities = readActivities($users[$i]['username'].'.db');

            // cycle through all activities
            if (!empty($activities)){
                $j = 0;
                while($j < count($activities)){

                    // does it have notifications set?
                    if (isset($activities[$j]['notifications']) && $activities[$j]['notifications'] && $activities[$j]['archived'] != 1){
                        if (count($activities[$j]['triggers'])>1){

                            // has this already been triggered?
                            $alreadyTriggered=FALSE;
                            if (isset($activities[$j]['notificationTriggered']) && $activities[$j]['notificationTriggered']) $alreadyTriggered=TRUE;

                            // Calculate intervals between consecutive timestamps
                            $intervals = calculateIntervals($activities[$j]);

                            // Calculate the average interval
                            $averageInterval = array_sum($intervals) / count($intervals);

                            // calculate the next trigger
                            $lastTrigger = $activities[$j]['triggers'][count($activities[$j]['triggers'])-1]['timestamp'];
                            $nextTrigger =  $lastTrigger + $averageInterval;
                            $timeToNextTrigger = $nextTrigger - time();
                            $perc = number_format((($averageInterval-$timeToNextTrigger)/$averageInterval)*100);

                            $list .= '<strong><u><a href="/statsActivity/'.$j.'">'.$activities[$j]['activityName'].'</a></u></strong><br>';
                            if ($perc > 100){
                                $list .= 'Overdue<br>&nbsp;<br>';                            
                            }elseif ($perc >= THRESHOLD && $alreadyTriggered){
                                $list .= 'Is '.$perc.'% through before next trigger is due (triggered)<br>&nbsp;<br>';                            
                            }else{
                                $list .= 'Is '.$perc.'% through before next trigger is due<br>&nbsp;<br>';                            
                            }
                            }
                    }
                    $j++;
                }    
            }
            $i++;
        }

        $smarty->assign('list', $list);
        $smarty->assign('header', 'Upcoming Notifications');
        $smarty->display('list.tpl');
        break;

    case '':

        $_SESSION['activities'] = readActivities($_SESSION['database']);
        $smarty->assign('activities', $_SESSION['activities']);
        if (isset($_REQUEST['state'])){
            $users = readUsers();
            $id = searchUsername($users, $_SESSION['username']);
            $users[$id]['show'] = $_REQUEST['state'];
            writeUsers($users);
            $_SESSION['show'] = $_REQUEST['state'];
        }
        if (isset($_REQUEST['colour'])){
            $smarty->assign('colour', $_REQUEST['colour']);
        }else{
            $smarty->assign('colour', 'default');
        }
        $smarty->assign('archived', $_SESSION['show']);
        $smarty->display('home.tpl');
        break;

    default:

        # command not recognised
        if (isset($_SESSION['database'])){
            $smarty->assign('error', 'Command not recognised');
            $smarty->assign('activities', $_SESSION['activities']);
            $smarty->display('home.tpl');
        }else{
            Header('Location: /login');
            die;
        }
        
        break;
}

?>