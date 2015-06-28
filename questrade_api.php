<?php

// ############################################################################
// Name: INC_QTAPI.PHP
// Date: 31-May-2015
// Prog: Andrew Smith
//
// Desc: After an initial proof of concept effort this file represents a pass
//       at improvement. Basic capabilities will be standardized for easier
//       reuse.
// ############################################################################

// ****************************************************************************
// CURL callback function allow curl return code retrieval. Used by qt_post
// and qt_get.
// ****************************************************************************
function qt_curl_code($setcode,$retclear=0)
{
static $code = '';

  if ( $retclear )
  {
    $x = $code;
    $code = '';
    return $x;
  }

  $code = $setcode;
}

// ****************************************************************************
// CURL callback function to allow curl header retrieval. Used by qt_post
// and qt_get.
// ****************************************************************************
function qt_curl_header($curl,$headerline,$retclear=0)
{
static $headers = '';

  if ( $retclear )
  {
    $x = $headers;
    $headers = '';
    return $x;
  }
  $headers .= $headerline;
  return strlen($headerline);
}

// ****************************************************************************
// A unified CURL post utility that will accept either an array of parameters
// to be URL encoded or a preformatted string that is expected to be a JSON
// list.
//
// Valid coding values are "URL" and "JSON" only.
// ****************************************************************************
function qt_post($protocol,$host,$uri,$auth,$parms,$encoding)
{
  $url = "$protocol://$host";
  if (strlen($uri))
    $url .= $uri;

  $post = '';
  if ( strtoupper($encoding) == 'URL' )
  {
    foreach($parms as $k => $v)
      $post .= $k . '=' . $v . '&';
    $post = rtrim($post,'&');
  }
  if ( strtoupper($encoding) == 'JSON' )
  {
    $post = json_encode($parms);
  }
   
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_USERAGENT, 'phpAPI');

  qt_curl_header('','',1);
  qt_curl_code('',1);
  curl_setopt($ch, CURLOPT_HEADERFUNCTION, "qt_curl_header");
  if ( strlen($auth) )
  {
    curl_setopt($ch, CURLOPT_HTTPHEADER,array("Authorization: $auth"));
    echo "auth: $auth<br>\n";
  }

  $out = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  qt_curl_code($httpCode);
  curl_close($ch);
  
  return $out;
}

// ****************************************************************************
// A unified CURL get utility that will accepting an array of parameters to be
// URL encoded.
// ****************************************************************************
function qt_get($protocol,$host,$uri,$auth,$parms = array())
{
  $url = "$protocol://$host";
  if ( strlen($uri) )
    $url .= $uri;

  if ( !empty($parms) )
  {
    $url .= '?';

    foreach($parms as $k => $v)
      $url .= $k . '=' . $v . '&';
    $url = rtrim($url,'&');
  }

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERAGENT, 'phpAPI');

  qt_curl_header('','',1);
  qt_curl_code('',1);
  curl_setopt($ch, CURLOPT_HEADERFUNCTION, "qt_curl_header");
  if ( strlen($auth) )
    curl_setopt($ch, CURLOPT_HTTPHEADER,array("Authorization: $auth"));

  $out = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  qt_curl_code($httpCode);
  curl_close($ch);

  return $out;
}

// ****************************************************************************
// Passing in the refresh token will cause a refresh attempt to occur. The
// caller must check the returned array to determine success/failure.
// ****************************************************************************
function qt_refresh($protocol,$host,$uri,$auth)
{
  $parms                  = array();
  $parms['grant_type']    = 'refresh_token';
  $parms['refresh_token'] = $auth;

  $encoding = 'URL';
  
  // Get time prior to request, may cause refresh slightly before expiration
  // -----------------------------------------------------------------------
  $granted = time();
  $result = qt_post($protocol,$host,$uri,$auth,$parms,$encoding);
  $res = json_decode($result);
  $headers = qt_curl_header('','',1);
  $code = qt_curl_code('',1);
  
  // Parse the returned details and assemble response
  // ------------------------------------------------
  $result = array();
	$result['code']           = $code;
	if ( $code > 200 )
	   return $result;

  $result['access_token']   = $res->access_token;
  $result['access_renew']   = $res->refresh_token;
  $result['access_server']  = str_replace('https://','',$res->api_server);
  $result['access_type']    = $res->token_type;
  $result['access_seconds'] = $res->expires_in;
	$result['access_expire']  = $granted + $res->expires_in;
	$result['headers']        = $headers;
	
	return $result;
}
