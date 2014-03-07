<?php

// ##########################################################################
// Name: cryptsyAPI.php
// Date: 07-Mar-2014
// Prog: Andrew Smith
//       gvroom [at] gmail.com (http://hackedcpu.com/)
//
// Desc: Simple function demonstrating interactions with the Crypsy API.
// ##########################################################################

// **************************************************************************
// Create, sign and send the API request to the Cryptsy endpoint. Response is
// returned unprocessed. Caller must check for errors.
// **************************************************************************
function cryptsyAPI($pubkey,$prvkey,$params=array(),$nonce=0)
{
  // Generate a simplistic nonce value if none was given.
  // ----------------------------------------------------
  if ( !$nonce )
     $nonce = time();

  // Assemble the parameters to be sent as a POST request. Done manually
  // for illustrative purposes... instead of using http_build_query().
  // --------------------------------------------------------------------
  $post = '';
  $sep  = '';
  foreach ($params as $key => $value)
  {
    $post .= $sep . $key . '=' . urlencode($value);
    $sep   = '&';
  }

  // Sign the completely assembed post data string
  // ---------------------------------------------
  $sig = hash_hmac("sha512", $post, $prvkey);

  // Cryptsy API specific header creation
  // ------------------------------------
  $headers = array();
  $headers[] = "Sign: $sig";
  $headers[] = "Key: $pubkey";

  // Prepare CURL before sending the request to Cryptsy
  // --------------------------------------------------
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_URL, 'https://api.cryptsy.com/api');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

  // Send request and return results to caller
  // -----------------------------------------
  return curl_exec($ch);
}
