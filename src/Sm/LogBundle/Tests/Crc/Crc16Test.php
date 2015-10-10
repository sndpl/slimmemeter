<?php
namespace Sm\LogBundle\Tests\Parser;

use Sm\LogBundle\Crc\Crc16;

class Crc16Test extends \PHPUnit_Framework_TestCase
{
    public function testInt()
    {
        $this->assertEquals('A455', strtoupper(dechex(Crc16::hash('12345'))), "Hash of 12345 isn't correct");
    }

    public function testString()
    {
        $this->assertEquals('7E25', strtoupper(dechex(Crc16::hash('Slimmemeter'))), "Hash of 'Slimmemeter' isn't correct");
    }

    public function testMessage()
    {
        $message = $this->getMessage();
        $message = str_replace("\n", "\r\n", $message);
        $this->assertEquals('A31B', strtoupper(dechex(Crc16::hash($message))), "Hash of Message isn't correct");
    }

    protected function getMessage()
    {
        return '/XMX5LGBBFFB231096081

1-3:0.2.8(42)
0-0:1.0.0(151010155809S)
0-0:96.1.1(4530303035303031353538323031323134)
1-0:1.8.1(002504.423*kWh)
1-0:2.8.1(000000.047*kWh)
1-0:1.8.2(002439.071*kWh)
1-0:2.8.2(000000.000*kWh)
0-0:96.14.0(0001)
1-0:1.7.0(02.015*kW)
1-0:2.7.0(00.000*kW)
0-0:96.7.21(00006)
0-0:96.7.9(00000)
1-0:99.97.0(0)(0-0:96.7.19)
1-0:32.32.0(00001)
1-0:52.32.0(00000)
1-0:72.32.0(00000)
1-0:32.36.0(00000)
1-0:52.36.0(00000)
1-0:72.36.0(00000)
0-0:96.13.1()
0-0:96.13.0()
1-0:31.7.0(009*A)
1-0:51.7.0(000*A)
1-0:71.7.0(000*A)
1-0:21.7.0(01.958*kW)
1-0:41.7.0(00.008*kW)
1-0:61.7.0(00.049*kW)
1-0:22.7.0(00.000*kW)
1-0:42.7.0(00.000*kW)
1-0:62.7.0(00.000*kW)
0-1:24.1.0(003)
0-1:96.1.0(4730303136353631323037373133373134)
0-1:24.2.1(151010150000S)(01087.668*m3)
!';
    }
}
