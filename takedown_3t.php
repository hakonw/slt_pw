<?php
$U_PATH = "/var/www/i/";  //her kunne jeg også ha bare ./upload tror jeg
$U_PATH_LOG = "sys/"; // path tol logg folderen
$U_WEB = "http://slt.pw/"; //siden
$U_LOG_H = "log.html"; // navnet på log for html loggen
$U_LOG_C = "log.cfg"; // navn på loggen for image=ip;

if (empty($_GET["u"])) {
    echo "No argument";
} elseif (!file_exists($U_PATH . $_GET["u"])) {
    echo "There isnt a file named that.";
} else {
    checkip($_GET["u"]);
}

function checkip($file_name) {
    global $U_WEB, $U_LOG_H, $U_LOG_C, $U_PATH_LOG;
    $u_cfg_line = file_get_contents($U_WEB . $U_PATH_LOG . $U_LOG_C); // gjør hele filen til 1 string
    $file = $file_name . "=" . $_SERVER["REMOTE_ADDR"];
    $htmlwrite = "<p> " . $_SERVER["REMOTE_ADDR"] . ":" . $_SERVER["REMOTE_PORT"] . " " . date("d.m H:i:s") . " deleted=" . $file_name . "</p>";
    if (strpos($u_cfg_line, $file) !== false) { // skell om stringen inneholder imgnavn=ip
        rmfile($file_name); // om filen finnes i loggen, slett filen
        cleanup($file, $U_LOG_C); // fjern filen fra loggen
        cleanup($file_name, $U_LOG_H, true); // fjern filen fra html loggen
        filewrite($htmlwrite, $U_LOG_H); // write at fil er slettet i log
    } else {
        echo "The file does not belong to you! \r\n";
    }
}

function cleanup($delete, $log_file, $searchfor = false) {

    global $U_PATH, $U_PATH_LOG;
    $data = file($U_PATH . $U_PATH_LOG . $log_file); // gjør log filen til et array, [x] = linje -1
    $out[] = array(); // bare lag et array for senere

    if (!$searchfor) { // 2 måter å finne koden, denne må linken være helt lik  " KAn da bort men vil ha her"
        foreach ($data as $line) { //  http://php.net/manual/en/control-structures.foreach.php
            if (trim($line) != $delete) { // alt som er ikke det man skal slette
                $out[] = $line; // f? til bake til et arary som skal bli filen
            } // else put un "Was removed??"
        }
    } else {
        foreach ($data as $line) { // mens hher må den bare inneholde stringen, KAN FIKSE
            if (!strpos($line, $delete)) {
                $out[] = $line;
            }
        }
    }

    $fp = fopen($U_PATH . $U_PATH_LOG . $log_file, "w+"); // w+ sletter alt i filen, og setter pointeren til 0 ( starten )
    //flock($fp, LOCK_EX); // l?ser s? ingen andre kan skrive noe fil filen
    foreach ($out as $line) { // skriv alt i arrayet til fil
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

function filewrite($write, $file) { // laget for å gjøre selve checkip() kortere
    global $U_PATH, $U_PATH_LOG;
    $fp = fopen($U_PATH . $U_PATH_LOG . $file, "a");
    fwrite($fp, $write);
    fclose($fp);
}


?>
