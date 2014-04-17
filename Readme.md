Incapsula Ip Fixer
==================

[![Build Status](https://travis-ci.org/intval/IncapsulaIpFixer.png?branch=master)](https://travis-ci.org/intval/IncapsulaIpFixer)
[![Latest Stable Version](https://poser.pugx.org/intval/incapsula-ip-fixer/v/stable.png)](https://packagist.org/packages/intval/incapsula-ip-fixer)
[![Total Downloads](https://poser.pugx.org/intval/incapsula-ip-fixer/downloads.png)](https://packagist.org/packages/intval/incapsula-ip-fixer)

Unveils real client ip hidden by [Incapusla](http://incapsula.com) proxy and cdn service.
Ip spoofing protection checks the remote addr against [allowed incapsulas IP list](http://support.incapsula.com/hc/en-us/articles/200627570-Restricting-direct-access-to-your-website-Incapsula-s-IP-addresses-)

This software is distributed under the BSD Licence. You can do whatever you want.


Usage
---------
Execute the method `FixRemoteAddrInServerArray`  
it will update the passed _SERVER array with correct remote addr only if the request is coming through incapsula  
`(new \Intval\IncapsulaIpFixer($_SERVER))->FixRemoteAddrInServerArray();`


`GetClientIp()`  
returns real clients ip behind incapsula proxy