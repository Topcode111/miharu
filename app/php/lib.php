<?php

define("LOG_P", dirname(__FILE__)."/../../log/".date("Y-m-d")."-log.log");
define("ERROR_P", dirname(__FILE__)."/../../log/".date("Y-m-d")."-error.log");
define("CRON_P", dirname(__FILE__)."/../../log/".date("Y-m-d")."-cron.log");
define("DEBUG_P", dirname(__FILE__)."/../../log/".date("Y-m-d")."-debug.log");
define("UPLINK_P", dirname(__FILE__)."/../../log/".date("Y-m-d")."-uplink.log");
define("DOWNLINK_P", dirname(__FILE__)."/../../log/".date("Y-m-d")."-downlink.log");
define("API_P", dirname(__FILE__)."/../../log/".date("Y-m-d")."-api.log");

function debug_output($text) {
    write_log(DEBUG_P, $text);
}

function error_output($text) {
    write_log(ERROR_P, $text);
}

function cron_log($text) {
    write_log(CRON_P, $text);
}

function uplink_log($text) {
    write_log(UPLINK_P, $text);
}

function downlink_log($text) {
    write_log(DOWNLINK_P, $text);
}

function write_log($file, $text) {
    file_put_contents($file, "[".date("Y/m/d H:i:s")."]".$text."\n", FILE_APPEND | LOCK_EX);
}

function api_log($text) {
    write_log(API_P, $text);
}



?>