<?php

$U_PATH = "/var/www/i/";  //her kunne jeg også ha bare ./upload tror jeg
$U_PATH_LOG = "sys/"; // path til loggen
$U_PATH_ERROR = "sys/error/";
$U_WEB = "http://slt.pw/"; //siden
$U_WEB_SHORT = "u/";
$U_MAXSIZE = 40; //max size i mib
$U_MAXCALC = 1024 * 1024 * $U_MAXSIZE;
$U_MAXON = 0; // tar av cap pga var noen issues
$U_WEB_DEL = $U_WEB . "sys/takedown_3t.php?u=";
//print_r($_FILES);

uploadFile();

function uploadFile() {
    global $U_PATH, $U_WEB, $U_MAXCALC, $U_MAXON;
    $tmp_rstring = randomname();
    $path_info = pathinfo($_FILES["file"]["name"]);
    $tmp_name = $tmp_rstring . "." . $path_info["extension"];

    if ($_FILES["file"]["error"] > 0 || empty($_FILES["file"]["name"])) { //sjekke om det ikke er en error
        echo $U_WEB . $U_PATH_ERROR . $_FILES["file"]["error"];  // echo erroren
        exit();
    } elseif ($_FILES["file"]["size"] >= $U_MAXCALC && $U_MAXON === 1) { // om filen er større enn $U_MAXSIZE Mib
        echo $U_WEB . $U_PATH_ERROR . "1?Size=" . $U_MAXCALC; // gi max size error
        exit();
    } elseif (file_exists($U_PATH . $tmp_rstring) || file_exists($U_PATH . $tmp_name)) {
        uploadFile();
        exit();
    } else {
        if (!($_FILES["file"]["type"] == "text/html" || $_FILES["file"]["type"] == "application/octet-stream")) { //file type != php[application/octet-stream] eller html[text/html]
            if ($_FILES["file"]["type"] == "text/plain"){ // check if text = link
              if (u_link()){
                exit();
              }
            }
            move_uploaded_file($_FILES["file"]["tmp_name"], $U_PATH . $tmp_name); // flytt fra tmp til upload
            u_log($tmp_name);
            if(!(empty($_REQUEST["inbrowser"]))){
                header("Location: " . $U_WEB . $tmp_name); //funker ikke i shareX men åpner linken automatisk i inbrowser
            } else {
                echo $U_WEB . $tmp_name; // echo linken
            }
            exit();
        } else {
            echo $U_WEB . "slt.html"; // echo linken
            exit();
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
    global $U_PATH, $U_WEB, $U_WEB_DEL, $U_PATH_LOG;
    $fh = fopen($U_PATH .  $U_PATH_LOG . "log.html", "a"); // setter verdien " �pne fil m stream MED write only -> nederst
    $fh_log_html = "<p> " . $_SERVER["REMOTE_ADDR"] . ":" . $_SERVER["REMOTE_PORT"] . " " . date("d.m H:i:s") .
            " <a href=\"" . $U_WEB . $U_FILE . "\">" . $U_FILE . "</a> <a href=\"" . $U_WEB_DEL . $U_FILE . "\"> [Delete]</a>" . "\r\n";

    fwrite($fh, $fh_log_html); // skriv string til fil i $fh
    fclose($fh); // look streamen til $fh filen

    $fh = fopen($U_PATH . $U_PATH_LOG . "log.cfg", "a");
    fwrite($fh, $U_FILE . "=" . $_SERVER["REMOTE_ADDR"] . "\n");
    fclose($fh);
}

function u_link(){ // sjekk om filen er en url og WOW overkompiserte det, men shorta det ned
  $u_text_file = file_get_contents($_FILES["file"]["tmp_name"]);
  if (filter_var($u_text_file, FILTER_VALIDATE_URL)){
    shorturl($u_text_file);
    return true;
  }else{
    return false;
  }
}

function shorturl($str_url){ // laget for å kunne shorte urlr
  global $U_PATH, $U_WEB_SHORT, $U_WEB;
  $tmp_rstring = randomname();
  if (file_exists($U_PATH . $U_WEB_SHORT . $tmp_rstring)) {
    shorturl($str_url);
    exit();
  }
  $fh = fopen($U_PATH . $U_WEB_SHORT . $tmp_rstring, "a");
  $fh_msg = "<meta http-equiv=\"refresh\" content=\"0; url=" . $str_url .  "\">";
  fwrite($fh, $fh_msg);
  fclose($fh);
  echo $U_WEB . $U_WEB_SHORT . $tmp_rstring;

}

?>
