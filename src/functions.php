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