<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
ini_set('error_reporting', E_ALL);
ini_set('pcre.backtrack_limit', 10000000);

// get model

require_once "mod/googleappslogin/models/Http.php";
require_once "mod/googleappslogin/models/Google_OpenID.php";

$google = new Google_OpenID();

$google->set_home_url("elgg.flatsourcing.com");
$google->set_return_url("elgg.flatsourcing.com/action/googleappslogin/return");

//$google->set_start_url("https://www.google.com/accounts/o8/id");
$google->set_start_url('https://www.google.com/accounts/o8/site-xrds?ns=2&hd=flatsoft.com');

$url = $google->get_authorization_url();

echo '<a href=' . $url . '>' . $url . "</a><br><br>\n\n";

//$end_point_url = $google->resolve_endpoint_url();

//var_dump($end_point_url);

/*
 RSA-SHA1:

      case 'RSA-SHA1':

        // Pull the private key ID from the certificate
        $privatekeyid = openssl_get_privatekey($cert);

        // Sign using the key
        $ok = openssl_sign($base_string, $signature, $privatekeyid);


        // Release the key resource
        openssl_free_key($privatekeyid);

        $retval = base64_encode($signature);


        break;
 */