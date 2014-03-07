Snippets Repository
===================

Various illustrative PHP snippets...

At the current time I have not decided on which open source license I should be using. It is very likely that these snippets will be integrated into some future projects.

Source Code
-----------

**asyncTCP.php**

Demonstration class showing how to send a message to a remote server asyncronously. This allows other requests to be sent while waiting for the remote server to respond.  As demonstrated the initial socket connection itself is not made asynchronously.

**cryptsyAPI.php**

Simple function showing how to assemble a message for use against Cryptsy APIs. Key visibility and nonce management concepts are hinted at but not detailed.

**getNonce.php**

Using PHP's microtime() function to generate fine grained time based nonce values. High frequency API use will consume a lot of "nonce space" if a second's worthy of "nonce space" needs to be consumed per API request.
