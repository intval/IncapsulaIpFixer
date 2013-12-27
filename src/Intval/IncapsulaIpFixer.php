<?php

namespace Intval;

class IncapsulaIpFixer
{
    /*** @const
     *   @see http://support.incapsula.com/hc/en-us/articles/200627570-Restricting-direct-access-to-your-website-Incapsula-s-IP-addresses-
     */
    private static $incapsulaIpRanges = array(
        array('199.83.128.1','199.83.135.254'),
        array('198.143.32.1','198.143.63.254'),
        array('149.126.72.1','149.126.79.254'),
        array('103.28.248.1','103.28.251.254'),
        array('185.11.124.1','185.11.127.254')
    );

    const INCAPSULA_CLIENT_IP_HEADER = 'HTTP_INCAP_CLIENT_IP';
    const REGULAR_CLIENT_IP_HEADER = 'REMOTE_ADDR';

    private $remoteAddr = null;
    private $ipBehindProxy = null;

    public function __construct(array & $SERVER)
    {
        if(!isset($SERVER[self::REGULAR_CLIENT_IP_HEADER]) || !filter_var($SERVER[self::REGULAR_CLIENT_IP_HEADER], FILTER_VALIDATE_IP))
            throw new \InvalidArgumentException('Expected to have $_SERVER variable as input,
            which will contain '. self::REGULAR_CLIENT_IP_HEADER. ', but this key was not present in the array or was not a valid ip ');

        $this->remoteAddr = & $SERVER[self::REGULAR_CLIENT_IP_HEADER];
        $this->ipBehindProxy = $this->ExtractIncapsulaHiddenIp($SERVER);
    }

    public function FixRemoteAddrInServerArray()
    {
        $this->remoteAddr = $this->GetClientIp();
    }

    public function GetClientIp()
    {
        return $this->ipBehindProxy !== null ? $this->ipBehindProxy : $this->remoteAddr;
    }

    private function ExtractIncapsulaHiddenIp(array $SERVER)
    {
        return
            (
                isset($SERVER[self::INCAPSULA_CLIENT_IP_HEADER])
                && filter_var($SERVER[self::INCAPSULA_CLIENT_IP_HEADER], FILTER_VALIDATE_IP)
                && $this->RequestCameThroughIncapsula()
            )
            ?  $SERVER[self::INCAPSULA_CLIENT_IP_HEADER]
            : null;
    }

    private function RequestCameThroughIncapsula()
    {
        $requestOriginLong = ip2long($this->remoteAddr);
        foreach(self::$incapsulaIpRanges as $ipRange)
        {
            $min = ip2long($ipRange[0]);
            $max = ip2long($ipRange[1]);

            if($requestOriginLong >= $min && $requestOriginLong <= $max)
                return true;
        }

        return false;
    }
} 