<?php

$U_PATH = "/var/www/i/";  //her kunne jeg også ha bare ./upload tror jeg
$U_WEB = "http://slt.pw/"; //siden

if(empty($_GET["u"])){
    echo "No argument";
}elseif (!file_exists($U_PATH . $_GET["u"])) {
    echo "There isnt a file named that";
}else{
    checkip($_GET["u"]);
}

function checkip($file_name){
    global $U_WEB;
    $u_cfg_line = file_get_contents($U_WEB . "sys/log.cfg");
    $file = $file_name . "=" . $_SERVER["REMOTE_ADDR"];
    $htmlwrite = "<p> " . $_SERVER["REMOTE_ADDR"] . ":" . $_SERVER["REMOTE_PORT"] . " " . date("d.m H:i:s") . " deleted=" . $file_name . "</p>";
    if (strpos($u_cfg_line, $file) !== false){
        rmfile($file_name);
        cleanup($file, "log.cfg");
        cleanup($file_name, "log.html", true);
        filewrite($htmlwrite, "log.html");
    }else{
        echo "The file does not belong to you! \r\n";
    }
}

function cleanup($delete, $log_file = "log.cfg", $searchfor = false){
    global $U_PATH;
    $data = file($U_PATH . "sys/" . $log_file);
    $out[] = array();

    if(!$searchfor){
        foreach($data as $line){ //  http://php.net/manual/en/control-structures.foreach.php
            if(trim($line) != $delete){ // alt som er ikke det man skal slette
               $out[] = $line; // f� til bake til et arary som skal bli filen
            }
        }
    }else{
        foreach($data as $line){
            if(!strpos($line, $delete)){
                $out[] = $line;
            }
        }
    }

    $fp = fopen($U_PATH . "sys/" . $log_file, "w+"); // w+ sletter alt i filen, og setter pointeren til 0 ( starten )
    //flock($fp, LOCK_EX); // l�ser s� ingen andre kan skrive noe fil filen
    foreach($out as $line) {
         fwrite($fp, $line);
    }
    //flock($fp, LOCK_UN);
    fclose($fp);
}

function rmfile($filename) {
    global $U_PATH;
    unlink($U_PATH . $filename); // slett filen
    echo $filename . " was removed.";
}

function filewrite($write, $file){
    global $U_PATH;
    $fp = fopen($U_PATH . "sys/" . $file, "a");
    fwrite($fp, $write);
    fclose($fp);
}

?>
