<?php
/**
 * $Id: get_remote_ip_address.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.security
 */

/**
 * Description of remote_addr
 *
 * @author Samumon
 */
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
else 
    if (isset($_SERVER['REMOTE_ADDR']))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "UNKNOWN";
echo $ip;
?>
