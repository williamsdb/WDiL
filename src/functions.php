<?php

    function readActivities($database) {
        // Read the activities database
        try {
            $activities = file_get_contents('./databases/'.$database);
        } catch (\Throwable $th) {
            die('Database file not found. Have you created it?');
        }
        return unserialize($activities);
    }

    function writeActivities($activities, $database) {
        // write the activities to the database file
        try {
            file_put_contents('./databases/'.$database,serialize($activities));
        } catch (\Throwable $th) {
            die('Database file not found. Have you created it?');
        }
    }

    function readUsers() {
        // Read the users database
        try {
            $users = file_get_contents('./users.db');
        } catch (\Throwable $th) {
            die('Database file not found. Have you created it?');
        }
        return unserialize($users);
    }

    function writeUsers($users) {
        // write the users to the database file
        try {
            file_put_contents('./users.db',serialize($users));
        } catch (\Throwable $th) {
            die('Database file not found. Have you created it?');
        }
    }

    function searchUsername($users, $username) {
        foreach ($users as $key => $user) {
            if ($user['username'] === $username) {
                return $key;  // Return the id (index) of the matching entry
            }
        }
        // Username is not found
        return -1;
    }

    function searchEmail($users, $email) {
        foreach ($users as $key => $user) {
            if ($user['email'] === $email) {
                return $key;  // Return the id (index) of the matching entry
            }
        }
        // Username is not found
        return -1;
    }

    // log calls
    function debug($string){

        if (!DEBUG) return;

        // write a line to the log file
        try {
            file_put_contents('./logs.db', date("Y-m-d H:i:s").','.'"'.$string.'"'.PHP_EOL, FILE_APPEND);
        } catch (\Throwable $th) {
            die('logs.db file not found. Have you created it?');
        }

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
    
    function formatTime($seconds, $round=2) {
        $minutes = $seconds / 60;
        $hours = $minutes / 60;
        $days = $hours / 24;
        $months = $days / 30.44; // approximate months
        $years = $days / 365.25; // approximate years

        if ($years == 1) {
            return round($years, $round) . ' year';
        } elseif ($years > 1) {
            return round($years, $round) . ' years';
        } elseif ($months == 1) {
            return round($months, $round) . ' month';
        } elseif ($months > 1) {
            return round($months, $round) . ' months';
        } elseif ($days == 1) {
            return round($days, $round) . ' day';
        } elseif ($days > 1) {
            return round($days, $round) . ' days';
        } elseif ($hours == 1) {
            return round($hours, $round) . ' hour';
        } elseif ($hours > 1) {
            return round($hours, $round) . ' hours';
        } elseif ($minutes == 1) {
            return round($minutes, $round) . ' minute';
        } elseif ($minutes > 1) {
            return round($minutes, $round) . ' minutes';
        } elseif ($seconds == 1) {
            return round($seconds, $round) . ' second';
        } else {
            return round($seconds, $round) . ' seconds';
        }
    }

    function sendMail($to, $subject, $body){
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = SMTP_PORT;
        
            //Recipients
            $mail->setFrom('from@example.com', 'Mailer');
            $mail->addAddress('joe@example.net', 'Joe User');
            $mail->addReplyTo('info@example.com', 'Information');
                
            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
        
            $mail->send();
            return '';
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    
    function array_to_html($val, $var=FALSE) {
        $do_nothing = true;
        $indent_size = 20;
        $out = '';
        $colors = array(
            "Teal",
            "YellowGreen",
            "Tomato",
            "Navy",
            "MidnightBlue",
            "FireBrick",
            "DarkGreen"
            );
      
          // Get string structure
          ob_start();
          print_r($val);
          $val = ob_get_contents();
          ob_end_clean();
      
          // Color counter
          $current = 0;
      
          // Split the string into character array
          $array = preg_split('//', $val, -1, PREG_SPLIT_NO_EMPTY);
          foreach($array as $char) {
              if($char == "[")
                  if(!$do_nothing)
                      if ($var) { $out .= "</div>"; }else{ echo "</div>"; }
                  else $do_nothing = false;
              if($char == "[")
                  if ($var) { $out .= "<div>"; }else{ echo "<div>"; }
              if($char == ")") {
                  if ($var) { $out .= "</div></div>"; }else{ echo "</div></div>"; }
                  $current--;
              }
      
              if ($var) { $out .= $char; }else{ echo $char; }
      
              if($char == "(") {
                  if ($var){
                    $out .= "<div class='indent' style='padding-left: {$indent_size}px; color: ".($colors[$current % count($colors)]).";'>";
                  }else{
                    echo "<div class='indent' style='padding-left: {$indent_size}px; color: ".($colors[$current % count($colors)]).";'>";
                  }
                  $do_nothing = true;
                  $current++;
              }
          }

          return $out;
    }
      
?>