<?php
/*
* This file checks the "mode" of the current site.
* Live - do nothing
* Maintenance - redirect to maintenance page
*/
require_once 'Include/connection.php';

$host = $_SERVER['SERVER_NAME'];
$host = str_replace('www.', '', $host); // remove "www." if present

$sql = 'select address, mode from website where server_name = "'.$host.'" limit 1';
$rs = mysql_query($sql, $cnRuby);
$rec = mysql_fetch_array($rs);
$address = $rec['address'];
$mode = $rec['mode'];

if ($mode == 2) { // maintenance
 header('Location: '.$address.'/maintenance.html');
}