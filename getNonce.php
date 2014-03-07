<?php

// ##########################################################################
// Name: getNonce.php
// Date: 08-Mar-2014
// Prog: Andrew Smith
//       gvroom [at] gmail.com (http://hackedcpu.com/)
//
// Desc: Slightly more sophisticated nonce value management for those APIs
//       that require an ever incrementing value. Be sure to check the nonce
//       details for the API in question before using this code.
//
// Note: You have the option of repeatedly generating time based nonce values
//       or performing a pre-increment (via ++var notation) during subsequent
//       API calls.
// ##########################################################################

// **************************************************************************
// Return a microtime driven nonce value.
//
// We add four digits of microtime to the standard time value (cutting off
// any remaining decimal) and then pad out the overall integer to ensure
// shorter microtime results don't show up as smaller nonce values.
//
// Beware of timezone issues when moving code from server to server!
// ***************************************************************************
function getNonce()
{
  return str_pad(floor((microtime(TRUE) * 10000)),strlen(time())+4,'0');
}
