<?php

// ##########################################################################
// Name: asyncTCP.php
// Date: 05-Mar-2014
// Prog: Andrew Smith
//       gvroom [at] gmail.com (http://hackedcpu.com/) 
//
// Desc: Provide generalized asynchronous TCP/IP communication utility.
//
// Note: This is not expected to be used (advanced) in a tight loop. Caller
//       is expected to use sleep() or usleep() to release CPU.
// ##########################################################################

class asyncTCP
{
  // Connection parameters
  // ---------------------
  private $transport;
  private $host;
  private $port;
  private $uri;
  private $postdata;
  private $timeout;

  // Connected socket details
  // ------------------------
  private $s;
  private $status;
  private $error;
  private $written;
  private $data;

  // ***********************************************************************
  // The timeout variable is used for connections only. Socket timeouts
  // are defined in the advance function.
  // ***********************************************************************
  public function __construct($transport,$host,$port,$uri,$postdata='',$timeout=7)
  {
    $this->transport = $transport;  // tcp, ssl
    $this->host      = $host;       // domain.com, www.domain.com
    $this->port      = $port;       // 80, 8080, 443
    $this->uri       = $uri;        // /xmlapi.php /x.php?token=abc
    $this->postdata  = $postdata;   // properly encoded
    $this->timeout   = $timeout;    // 5, 20

    $this->s         = null;        // no connection yet
    $this->status    = 'ready';     // no status yet
    $this->error     = 0;           // no errors yet
    $this->written   = 0;           // not written yet
    $this->data      = '';          // accumulated response
  }

  // ***********************************************************************
  // Simple status checks
  // ***********************************************************************
  public function isReady()   { return ( $this->status == 'ready'); }
  public function isDone()    { return ( $this->status == 'done' || $this->error > 0); }
  public function isWritten() { return ( $this->written > 0 ); }
  public function wasError()  { return ( $this->error > 0 ); }
  public function getStatus() { return ( $this->status ); }
  public function getData()   { return ( $this->data); }

  // ***********************************************************************
  // Connect to the remote server.  This is not asynchronous at the current
  // time though it appears to be suported (via flag options).
  // ***********************************************************************
  public function connect()
  {
    $errno  = 0;
    $errstr  = '';
    $this->s = stream_socket_client($this->transport."://".$this->host.":".$this->port
                                   ,$errno
                                   ,$errstr
                                   ,$this->timeout
                                   ,STREAM_CLIENT_ASYNC_CONNECT|STREAM_CLIENT_CONNECT);

    $this->status = 'connected';
    if ( $errno )
    {
      $this->error = $errno;
      $this->status = 'connect failed: $errrstr ($errno)';
    }

    return $this->error;
  }

  // ***********************************************************************
  // Manage the connection. Basically, advance through a series of actions
  // to send a request and get the response.
  // ***********************************************************************
  public function advance()
  {
    static $sockets = array();
    static $seconds = 0;
    static $usec    = 100;
    static $readlen = 8192;

    $sockets['id']  = $this->s;
    $read           = $sockets;
    $write          = $sockets;
    $except         = null;

    $n = stream_select($read, $write, $except, $seconds, $usec);

    if ( $n )
    {
      // Perform available read operations
      // ---------------------------------
      foreach ($read as $r)
      {
        $chunk = fread($r, $readlen);
        if ( $chunk === FALSE )
          $this->status = 'connect or read error';
        else
        {
          $this->data  .= $chunk;
          $this->status = 'reading';
        }

        if ( strlen($chunk) < $readlen )
          $this->status = 'done';
      }

      // Perform pending write operations
      // --------------------------------
      foreach ($write as $w)
      {
        if ( !$this->written )
        {
          // Adjust for GET or POST requests
          // -------------------------------
          $method  = 'GET';
          $host    = $this->host;
          $port    = $this->port;
          $uri     = $this->uri;
          $headers = "Host: $host\r\n";

          if ( strlen($this->postdata) )
          {
            $method   = 'POST';
            $length   = strlen($this->postdata);
            $headers .= "Content-type: application/x-www-form-urlencoded\r\n";
            $headers .= "Content-length: $length\r\n";
          }
          $headers .= "\r\n";

          // Build the message bundle
          // ------------------------
          $msg  = "$method $uri HTTP/1.0\r\n";
          $msg .= $headers;
          $msg .= $this->postdata;

          fwrite($w,$msg);
          $this->status  = 'sent; waiting for response';
          $this->written = 1;
        }
      }

    } // if ( n )

    return $this->status;

  } // function advance()

} // class asyncTCP
