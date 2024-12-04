<?php

/**
 * notifications.php
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

// load users
$users = readUsers();

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
            if (isset($activities[$j]['notifications']) && $activities[$j]['notifications'] && (isset($activities[$j]['archived']) && $activities[$j]['archived'] != 1) || !isset($activities[$j]['archived'])){
                if (count($activities[$j]['triggers'])>1){

                    // has this already been triggered?
                    if (isset($activities[$j]['notificationTriggered']) && $activities[$j]['notificationTriggered']==1) break;

                    // Calculate intervals between consecutive timestamps
                    $intervals = calculateIntervals($activities[$j]);

                    // Calculate the average interval
                    $averageInterval = array_sum($intervals) / count($intervals);

                    // calculate the next trigger
                    $lastTrigger = $activities[$j]['triggers'][count($activities[$j]['triggers'])-1]['timestamp'];
                    $nextTrigger =  $lastTrigger + $averageInterval;
                    $timeToNextTrigger = $nextTrigger - time();
                    $perc = number_format((($averageInterval-$timeToNextTrigger)/$averageInterval)*100, 0, '.', '');

                    // is the next trigger in the future and > 90% of the average interval? (or whatever the THRESHOLD is set to in config)

                    if ($timeToNextTrigger > 0 && $perc >= THRESHOLD){

                        //send an email
                        if (!empty(SMTP_HOST)){
                            $data['activityName'] = $activities[$j]['activityName'];
                            $data['dueIn'] = formatTime($timeToNextTrigger, 0);
                            $body = $smarty->fetch(__DIR__.'/templates/email-notification.tpl', $data);
                
                            // send email
                            sendMail($users[$i]['email'], 'WDiL Notification', $body);
                        }

                        // send a pushover
                        if (!empty($users[$i]['pushoverToken'])){
                            pushover('Your activity '.$activities[$j]['activityName']. ' is due to be triggered in '.formatTime($timeToNextTrigger, 0), $users[$i]['pushoverToken'], $users[$i]['pushoverUser']);  
                        }

                        // set the notification trigger so it doesn't happen again this trigger
                        $activities[$j]['notificationTriggered'] = 1;
                        writeActivities($activities, $users[$i]['username'].'.db');

                    }
                    
                }
            }
            $j++;
        }    
    }
    $i++;
}

?>