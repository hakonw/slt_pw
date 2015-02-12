<?php
$U_PATH = "/var/www/main/"; //her kunne jeg også ha bare ./upload tror jeg
$U_PATH_LOG = "/var/www/sys/"; // path til loggen
$U_PATH_ERROR = "error/";
$U_WEB = "http://slt.pw/"; //siden
$U_WEB_SYS = "http://s.slt.pw/";
$U_MAXSIZE = 40; //max size i mib
$U_MAXCALC = 1024 * 1024 * $U_MAXSIZE;
$U_MAXON = 0; // tar av cap pga var noen issues
$sql_cfg = array(
    "host" => "localhost",
    "username" => "yukiyuki",
    "password" => "yuki69",
    "dbname" => "db_slt",
    "table" => "loggy"
); //sql settings
// print_r($_FILES);

uploadFile();

function uploadFile(){
    global $U_PATH, $U_WEB, $U_MAXCALC, $U_MAXON, $U_PATH_ERROR, $U_WEB_SYS;
    $tmp_rstring = randomname();
    $path_info = pathinfo($_FILES["file"]["name"]);
    $tmp_name = $tmp_rstring . "." . $path_info["extension"];
    if ($_FILES["file"]["error"] > 0) { //sjekke om det ikke er en error
        echo $U_WEB_SYS . $U_PATH_ERROR . $_FILES["file"]["error"]; // echo erroren
    }
    elseif (empty($_FILES["file"]["name"])) { //mer for debug
        echo $U_WEB_SYS . $U_PATH_ERROR . "EMPTY_FILE_NAME";
    }
    elseif (($_FILES["file"]["size"] >= $U_MAXCALC) && $U_MAXON === 1) { // om filen er større enn $U_MAXSIZE Mib
        echo $U_WEB_SYS . $U_PATH_ERROR . "1?Size=" . $U_MAXCALC; // gi max size error
    }
    elseif (file_exists($U_PATH . $tmp_name) || file_exists($U_PATH . $tmp_rstring)) { /* The last check here is not needed */
        uploadFile();
    }
    else {
        if (sqlconnect()) {
            move_uploaded_file($_FILES["file"]["tmp_name"], $U_PATH . $tmp_name); // flytt fra tmp til upload
            u_sql_log($tmp_name);
        }
        else {
            exit();
        }
        if (!(empty($_REQUEST["inbrowser"]))) {
            header("Location: " . $U_WEB . $tmp_name); //funker ikke i shareX men åpner linken automatisk i inbrowser
	    echo "<script> window.alert(" . $U_WEB . $tmp_name . "); </script>";
        }
        else {
            echo $U_WEB . $tmp_name; // echo linken
        }
    }
    exit();
}

function randomname($length = 3){
    $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; // alle chars
    $randomstring = ""; //gjøre klar stringen
    for ($i = 0; $i < $length; $i++) { // looper length ganger og plusser på 1 char hver gang
        $randomstring.= $chars[rand(0, strlen($chars) - 1) ]; // rand = random nummer fra 0 og lengden på chars -1  og  $string[] tar charsen ut av den
    }

    return $randomstring;
}

function u_sql_log($u_file){
    global $cone, $sql_cfg;
    $tmp_date = date("m.d.y");
    $tmp_time = date("H:i:s");
    $tmp_ip = $_SERVER["REMOTE_ADDR"];
    $filesize = round($_FILES["file"]["size"]/1024, 1);

    // need to beautify this
    mysqli_query($cone, "INSERT INTO " . $sql_cfg["table"] . " (ip, date, time, file, size)
    VALUES (\"" . $tmp_ip . "\",
    \"" . $tmp_date . "\",
    \"" . $tmp_time . "\",
    \"" . $u_file . "\",
    \"" . $filesize . "\"
    )");
    mysqli_close($cone); //close connection
}

function sqlconnect(){
    global $cone, $sql_cfg;
    $cone = mysqli_connect($sql_cfg["host"], $sql_cfg["username"], $sql_cfg["password"], $sql_cfg["dbname"]); // Create connection
    if (mysqli_connect_errno()) { // Check connection
        echo "SQLerror:" . mysqli_connect_error(); 
        return false;
    }

    return true;
}
?>
