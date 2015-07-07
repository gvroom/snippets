<?php

// ##########################################################################
// Name: LIMITER.PHP
// Date: 07-Jul-2015
// Prog: Andrew Smith
//
// Desc: Demonstration class for voluntary self-application of rate limits
//       during API calls or other load limiting systems.
//
// ##########################################################################

// **************************************************************************
// A general limiting class allowing per second, per minute and per hour 
// limits.  If using multiple processes, whether simultaneous or not, a means
// of storing and sharing usage details will need to be devised.
// **************************************************************************
class Limiter
{
  private $queue = array();
  private $size;
  private $next;

  private $perSecond;
  private $perMinute;
  private $perHour;
  
  // ------------------------------------------------------------------------
  // Set any constructor parameter to non-zero to allow adherence to the
  // limit represented. The largest value present will be the size of the
  // circular queue used to track usage.
  // ------------------------------------------------------------------------
  function __construct($perSecond=0,$perMinute=0,$perHour=0)
  {
    $this->size = max($perSecond,$perMinute,$perHour);
    $this->next = 0;
    
    $this->perSecond = $perSecond;
    $this->perMinute = $perMinute;
    $this->perHour   = $perHour;
    
    for($i=0; $i < $this->size; $i++)
      $this->queue[$i] = 0;
  }
  
  // ------------------------------------------------------------------------
  // See if a use would violate any of the limits specified. We return true
  // if a limit has been hit.
  // ------------------------------------------------------------------------
  public function limitHit($verbose=0)
  {    
    $inSecond = 0;
    $inMinute = 0;
    $inHour   = 0;
    
    $doneSecond = 0;
    $doneMinute = 0;
    $doneHour   = 0;
    
    $now = microtime(true);
    
    if ( $verbose )
      echo "Checking if limitHit at $now<br>\n";
      
    for ($offset=1; $offset <= $this->size; $offset++)
    {
      $spot = $this->next - $offset;
      if ( $spot < 0 )
        $spot = $this->size - $offset + $this->next;
      
      if ( $verbose )
        echo "... next $this->next size $this->size offset $offset spot $spot utime " . $this->queue[$spot] . "<br>\n";
        
      // Count and track within second
      // -----------------------------
      if ( $this->perSecond && !$doneSecond && $this->queue[$spot] >= microtime(true) - 1.0 )
        $inSecond++;
      else
        $doneSecond = 1;
        
      // Count and track within minute
      // -----------------------------
      if ( $this->perMinute && !$doneMinute && $this->queue[$spot] >= microtime(true) - 60.0 )
        $inMinute++;
      else
        $doneMinute = 1;
        
      // Count and track within hour
      // ---------------------------
      if ( $this->perHour && !$doneHour && $this->queue[$spot] >= microtime(true) - 3600.0 )
        $inHour++;
      else
        $doneHour = 1;
        
      if ( $doneSecond && $doneMinute && $doneHour )
        break;
    }
    
    if ( $verbose )
      echo "... inSecond $inSecond inMinute $inMinute inHour $inHour<br>\n";
      
    if ( $inSecond && $inSecond >= $this->perSecond )
    {
      if ( $verbose )
        echo "... limit perSecond hit<br>\n";
      return TRUE;
    }
    if ( $inMinute && $inMinute >= $this->perMinute )
    {
      if ( $verbose )
        echo "... limit perMinute hit<br>\n";
      return TRUE;
    }
    if ( $inHour   && $inHour   >= $this->perHour   )
    {
      if ( $verbose )
        echo "... limit perHour hit<br>\n";
      return TRUE;
    }
      
    return FALSE;
  }
  
  // When an API is called the using program should voluntarily track usage
  // via the usage function.
  // ----------------------------------------------------------------------
  public function usage()
  {
    $this->queue[$this->next++] = microtime(true);
    if ( $this->next >= $this->size )
      $this->next = 0;
  }
}

// ##############################
// ### Test the limiter class ###
// ##############################

if ( 1 )
{
  $psec = 2;
  $pmin = 4;
  $phr  = 0;
  
  echo "Creating limiter with limits of $psec/sec and $pmin/min and $phr/hr<br><br>\n";
  $monitorA = new Limiter($psec,$pmin,$phr);
  
  for ($i=0; $i<15; $i++)
  {
    if ( !$monitorA->limitHit(1) )
    {
      echo "<br>\n";
      echo "API call A here (utime " . microtime(true) . ")<br>\n";
      echo "Voluntarily registering usage<br>\n";
      $monitorA->usage();
      usleep(250000);
    }
    else
    {
      echo "<br>\n";
      usleep(500000);
    }
  }
}
