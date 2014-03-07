<?php

// ##########################################################################
// Name: cryptsyAPI_test.php
// Date: 07-Mar-2014
// Prog: Andrew Smith
//       gvroom [at] gmail.com (http://hackedcpu.com/)
//
// Desc: Test driver to illustrate use of the cryptsyAPI function.
// ##########################################################################

// --------------------------------------------------------------------------
// Include necessary files.  You could also define your own constants here
// instead of using an include file.
//
// define('CRYPTSY_PUB_KEY','...your_public_key...');
// define('CRYPTSY_PRV_KEY','...your private key...');
//
// Using an include file makes it easier to publish this code without
// revealing API keys.
// --------------------------------------------------------------------------

require "cryptsyAPI.php";  // Contains cryptsyAPI function
require "apikeys.php";     // Define your keys as constants

// --------------------------------------------------------------------------
// You can simply use the time() function for initial testing. For real world
// use you'll probably want to manage the nonce value in a serious manner.
// --------------------------------------------------------------------------
$nonce = time();

// Create an array of parameters for the call being made
// -----------------------------------------------------
$params = array();
$params['method'] = 'getinfo';
$params['nonce']  = $nonce;

// Make it so
// ----------
echo "Calling the Cryptsy API " . $params['method'] . " method...<br><br>\n";

$res = cryptsyAPI(CRYPTSY_PUB_KEY, CRYPTSY_PRV_KEY, $params, $nonce);
if ( !$res )
   die("ERROR: Invalid response from Cryptsy!\n");

// Parse the return into an associative array and dump the output
// --------------------------------------------------------------
$data = json_decode($res,TRUE);
echo "Response:<br>\n";
var_dump($data);
