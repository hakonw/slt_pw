<?php

$U_PATH = "/var/www/i/";  //her kunne jeg også ha bare ./upload tror jeg
$U_WEB = "http://slt.pw/"; //siden
$U_MAXSIZE = 40; //max size i mib
$U_MAXCALC = 1024 * 1024 * $U_MAXSIZE;
$U_MAXON = 0; // tar av cap pga var noen issues
$U_WEB_DEL = $U_WEB . "sys/takedown_3t.php?u=";
//print_r($_FILES); //print all info  for debug ofc
uploadFile();


function uploadFile() {
    global $U_PATH, $U_WEB, $U_MAXCALC, $U_MAXON;
    $tmp_rstring = randomname();
    $path_info = pathinfo($_FILES["file"]["name"]);
    $tmp_name = $tmp_rstring . "." . $path_info["extension"];

    if ($_FILES["file"]["error"] > 0 || empty($_FILES["file"]["name"])) { //sjekke om det ikke er en error
        echo $U_WEB . "sys/error/" . $_FILES["file"]["error"];  // echo erroren
        exit();
    } elseif ($_FILES["file"]["size"] >= $U_MAXCALC && $U_MAXON === 1) { // om filen er større enn $U_MAXSIZE Mib
        echo $U_WEB . "sys/error/1?Size=" . $U_MAXCALC; // gi max size error
        exit();
    } elseif (file_exists($tmp_rstring)) {
        uploadFile();
        exit();
    } else {
        if(!($_FILES["file"]["type"] == "text/html" || $_FILES["file"]["type"] == "application/octet-stream")){
            move_uploaded_file($_FILES["file"]["tmp_name"], $U_PATH . $tmp_name); // flytt fra tmp til upload
            u_log($tmp_name);
            //echo $U_WEB . $tmp_name; // echo linken
            header('Location: ' . $U_WEB . $tmp_name);
            exit();
        }else {
            //echo $U_WEB . "slt.html";
            header('Location: ' . $U_WEB . $tmp_name);
        }
    }
}

function randomname($length = 3) {
    $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; // alle chars
    $randomstring = ""; //gjøre klar stringen
    for ($i = 0; $i < $length; $i++) { // looper length ganger og plusser på 1 char hver gang
        $randomstring .= $chars[rand(0, strlen($chars) - 1)]; // rand = random nummer fra 0 og lengden på chars -1  og  $string[] tar charsen ut av den
    }
    return $randomstring;
}

function u_log($U_FILE) { // logs what was uploaded from who
    global $U_PATH, $U_WEB, $U_WEB_DEL;
    $fh = fopen($U_PATH . "sys/log.html", "a"); // setter verdien " �pne fil m stream MED write only -> nederst
    $fh_log_html = "<p> " . $_SERVER["REMOTE_ADDR"] . ":" . $_SERVER["REMOTE_PORT"] . " " . date("d.m H:i:s") .
                    " <a href=\"" . $U_WEB . $U_FILE . "\">" . $U_FILE . "</a> <a href=\"" . $U_WEB_DEL . $U_FILE . "\"> [Delete]</a>" ."\r\n";

    fwrite($fh, $fh_log_html); // skriv string til fil i $fh
    fclose($fh); // look streamen til $fh filen

    $fh = fopen($U_PATH . "sys/log.cfg", "a");
    fwrite($fh, $U_FILE . "=" . $_SERVER["REMOTE_ADDR"] . "\n");
    fclose($fh);
}

?>
