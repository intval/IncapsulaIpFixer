<?php

namespace Intval;

class IncapsulaIpFixerTest  extends \PHPUnit_Framework_TestCase
{
    const IP_IN_INCAPSULA_RANGE = '198.143.41.1';

    /** @expectedException \InvalidArgumentException */
    public function testArrayWithoutRemoteAddr()
    {
        $emptyArray = array();
        new IncapsulaIpFixer($emptyArray);
    }

    /** @expectedException \PHPUnit_Framework_Error */
    public function testNotArrayAsInput()
    {
        $null = null;
        new IncapsulaIpFixer($null);
    }

    /** @expectedException \InvalidArgumentException */
    public function testRemoteAddrInArrayIsNotAValidIp()
    {
        $server = array('REMOTE_ADDR' => null);
        new IncapsulaIpFixer($server);
    }

    public function testNoIncapsulaHeaderPresent_ReturnsSameIp()
    {
        $ip = '127.0.0.1';
        $server = array('REMOTE_ADDR' => $ip);
        $fixer = new IncapsulaIpFixer($server);
        $this->assertEquals($ip, $fixer->GetClientIp());
    }

    public function testIncapsulaIpIsPresent_ButRequestOriginatedNotThroughIncapsula()
    {
        $ip = '127.0.0.1';
        $server = array('REMOTE_ADDR' => $ip, IncapsulaIpFixer::INCAPSULA_CLIENT_IP_HEADER => '123.123.123.123');
        $fixer = new IncapsulaIpFixer($server);
        $this->assertEquals($ip, $fixer->GetClientIp());
    }

    public function testIncapsulaIpIsPresent_ButNotInValidFormat()
    {
        $ip = self::IP_IN_INCAPSULA_RANGE;
        $server = array('REMOTE_ADDR' => $ip, IncapsulaIpFixer::INCAPSULA_CLIENT_IP_HEADER => null);
        $fixer = new IncapsulaIpFixer($server);
        $this->assertEquals($ip, $fixer->GetClientIp());
    }

    public function testIncapsulaIpIsPresent_AndReturned()
    {
        $ip = self::IP_IN_INCAPSULA_RANGE;
        $ipBehindProxy = '127.128.129.130';

        $server = array('REMOTE_ADDR' => $ip, IncapsulaIpFixer::INCAPSULA_CLIENT_IP_HEADER => $ipBehindProxy);
        $fixer = new IncapsulaIpFixer($server);
        $this->assertEquals($ipBehindProxy, $fixer->GetClientIp());
    }


    public function testFixerFixesIpInServerArray()
    {
        $ip = self::IP_IN_INCAPSULA_RANGE;
        $ipBehindProxy = '127.128.129.130';

        $server = array('REMOTE_ADDR' => $ip, IncapsulaIpFixer::INCAPSULA_CLIENT_IP_HEADER => $ipBehindProxy);
        $fixer = new IncapsulaIpFixer($server);
        $fixer->FixRemoteAddrInServerArray();

        $this->assertEquals($server['REMOTE_ADDR'], $fixer->GetClientIp());
        $this->assertEquals($server['REMOTE_ADDR'], $ipBehindProxy);
    }
} 