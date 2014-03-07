<?php

// ##########################################################################
// Name: getNonce_test.php
// Date: 08-Mar-2014
// Prog: Andrew Smith
//       gvroom [at] gmail.com (http://hackedcpu.com/)
//
// Desc: Demonstrate use of the getNonce function. You could also manually
//       increment the nonce per API call (via ++var notation).
// ##########################################################################

require ("getNonce.php");

date_default_timezone_set("UTC");

echo "The following outputs show the unix time followed by two nonce<br>\n";
echo "values generated before and after a usleep(2000) statement. Following<br>\n";
echo "this are a couple ++var generated nonce values.<br><br>\n";

echo time();
echo " ... time()\n";
echo "<br><br>\n";

echo getNonce();
echo " ... getNonce()\n";
echo "<br><br>\n";

usleep(2000);

$nonce = getNonce();
echo $nonce;
echo " ... getNonce()\n";
echo "<br><br>\n";

echo ++$nonce;
echo "... ++nonce\n";
echo "<br><br>\n";

echo ++$nonce;
echo "... ++nonce\n";
echo "<br><br>\n";
