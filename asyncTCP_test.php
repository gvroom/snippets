<?php 

require './asyncTCP.php';

// #################################### 
// ### TEST TCP AND SSL CONNECTIONS ### 
// #################################### 

$atime = microtime(TRUE); 

echo "initialize object data:<br>\n"; 
$elapsed = sprintf("%0.7f",microtime(TRUE) - $atime); 
echo "... elapsed: $elapsed<br>\n"; 
echo "<br>\n"; 

$c1 = new asyncTCP('tcp','www.google.com','80','/'); 
$elapsed = sprintf("%0.7f",microtime(TRUE) - $atime); 
echo "c1 " . $c1->getStatus() . "<br>\n... elapsed: $elapsed<br>\n"; 

$c2 = new asyncTCP('ssl','gmail.google.com','443','/'); 
$elapsed = sprintf("%0.7f",microtime(TRUE) - $atime); 
echo "c2 " . $c2->getStatus() . "<br>\n... elapsed: $elapsed<br>\n"; 

echo "<br>\n"; 
for ( ;; ) 
{ 
  if ( $c1->isReady() ) 
  { 
    echo "... issueing c1 connect "; 
    $c1->connect(); 
    $elapsed = sprintf("%0.7f",microtime(TRUE) - $atime); 
    echo "... c1 " . $c1->getStatus() . " at elapsed $elapsed<br>\n<br>\n";
  } 

  if ( $c2->isReady() ) 
  { 
    echo "... issueing c2 connect "; 
    $c2->connect(); 
    $elapsed = sprintf("%0.7f",microtime(TRUE) - $atime); 
    echo "... c2 " . $c2->getStatus() . " at elapsed $elapsed<br>\n<br>\n";
  } 

  if ( !$c1->isDone() ) 
    $c1->advance(); 
  if ( !$c2->isDone() ) 
    $c2->advance(); 

  $elapsed = sprintf("%0.7f",microtime(TRUE) - $atime); 
  echo "elapsed: $elapsed<br>\n"; 
  echo "... c1: " . $c1->getStatus() . "<br>\n"; 
  echo "... c2: " . $c2->getStatus() . "<br>\n"; 

  if ( $c1->isDone() && $c2->isDone() ) 
    break; 

  usleep(5000); 
} 

echo "<br>\n"; 
echo "c1:<br>\n" . htmlentities($c1->getData()) . "<br><br>\n"; 
echo "c2:<br>\n" . htmlentities($c2->getData()) . "<br><br>\n";
