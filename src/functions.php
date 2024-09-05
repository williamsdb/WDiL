<?php

    function pushover($message, $token, $user) {
        // Send to PushOver
        curl_setopt_array($ch = curl_init(), array(
            CURLOPT_URL => "https://api.pushover.net/1/messages.json",
            CURLOPT_POSTFIELDS => array(
            "token" => $token,
            "user" => $user,
            "message" => $message,
            ),
        ));
        curl_exec($ch);
        curl_close($ch);

        return;
    }

    function readActivities() {
        // Read the activities database
        try {
            $activities = file_get_contents('./activities.db');
        } catch (\Throwable $th) {
            die('activities.db file not found. Have you created it?');
        }
        return unserialize($activities);
    }

    function writeActivities($activities) {
        // write the activities to the database file
        try {
            file_put_contents('./activities.db',serialize($activities));
        } catch (\Throwable $th) {
            die('activities.db file not found. Have you created it?');
        }
    }

    // log calls
    function debug($string){

        if (!DEBUG) return;

        // write the rules to the database file
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