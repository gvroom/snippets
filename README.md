Snippets Repository
===================

Various illustrative PHP snippets...

These are published with the MIT license (see below).  Be free!

Source Code
-----------

**questrade_api.php**

PHP based functions using CURL to make API requests using either GET or POST functionality. Created for use against the Questrade API but reworkable for any similar back end. Improvements in character encoding required before non-personal usage.

**asyncTCP.php**

Demonstration class showing how to send a message to a remote server asyncronously. This allows other requests to be sent while waiting for the remote server to respond.  As demonstrated the initial socket connection itself is not made asynchronously.

**cryptsyAPI.php**

Simple function showing how to assemble a message for use against Cryptsy APIs. Key visibility and nonce management concepts are hinted at but not detailed.

**getNonce.php**

Using PHP's microtime() function to generate fine grained time based nonce values. High frequency API use will consume a lot of "nonce space" if a second's worth of "nonce space" needs to be consumed per API request.

License
=======

This snippets repository is published under the terms of the MIT license (see LICENSE.txt). 

Need more information? Read more about various open source [licensing](http://choosealicense.com/licenses/) schemes.
