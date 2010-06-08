#!/usr/bin/env php
#
#  A simple PHP script to determine whether a site is running on Drupal.
#
#  Created by Irakli Nadareishvili, based on similar shell script by Lasha Dolidze.
#  Distributed under GPL license v 2.x or later
#  http://www.gnu.org/licenses/gpl-2.0.html
#  

<?php
error_reporting(E_ERROR | E_PARSE);
$invalidArguments = False;

if (sizeof($argv) != 2) {
	$invalidArguments = True;
}

$url = $argv[1];
$parsed_url = @parse_url($url);
if (empty($parsed_url['host'])) {
	exit("ERROR: invalid URL. \n");
}


if ($invalidArguments) {
	echo ("USAGE: $argv[0] [validUrl] \n");
	exit ("  Example: $argv[0] http://example.com \n");
}

if (!function_exists("curl_init")) {
	exit ("ERROR: CURL PHP Extension is required \n");
}


$is_drupal = is_drupal($url);

if (is_numeric($is_drupal)) {
	exit ("Yes, this appears to be a Drupal site version $is_drupal \n");
}
elseif ($is_drupal == True) {
	exit ("Yes, this appears to be a Drupal site. Probably version 4 or older. \n");
}
else {
	exit ("No, this does not appear to be a Drupal site. \n");
}

function is_drupal($url) {
	if (curl_http_url_exists("$url/misc/drupal.js")) { // Definitely a Drupal site	
		if (curl_http_url_exists("$url/misc/timezone.js")) {
			return 7;
		}	
		elseif (curl_http_url_exists("$url/modules/system/system.js")) {
			return 6;
		}
		elseif (curl_http_url_exists("$url/modules/system/system.css")) {
			return 5;
		}
		else { // Not sure which version
			return True;
		}
	}	
	else {
		return False;
	}
}

function curl_http_url_exists($url) {
    $options = array(
        CURLOPT_RETURNTRANSFER => True,     // return web page
        CURLOPT_HEADER         => True,     // return headers
				CURLOPT_NOBODY				 => True,			// Don't return body.
        CURLOPT_FOLLOWLOCATION => True,     // follow redirects
				CURLOPT_MAXREDIRS			 => 5,				// no more than 5 redirects!
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "isDrupal Script", // who am i
        CURLOPT_AUTOREFERER    => True,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
    );

    $ch      = curl_init($url);
    curl_setopt_array($ch, $options);
		$content = curl_exec($ch);
    $header  = curl_getinfo($ch);
    curl_close($ch);
		
		//print_r($header);
		//print_r($content . "\n");
		if (!empty($header['http_code']) && ($header['http_code'] > 199 && $header['http_code'] < 399)) {
			return True;
		}
		else {
			return False;
		}
}