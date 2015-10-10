<?php
namespace Sm\LogBundle\Tests\Parser;

use Sm\LogBundle\Tests\FixturesTrait;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    use FixturesTrait;

    /**
     * @var \Sm\LogBundle\Parser\Parser
     */
    protected $parser;

    public function setUp()
    {
        $this->parser = new \Sm\LogBundle\Parser\Parser();
    }

    public function testParser()
    {
        $telegram = $this->parser->parse($this->getData());

        $this->assertEquals('XMX', $telegram->getMeterSupplier());
        $this->assertEquals('XMX5LGBBFFB231096081', $telegram->getHeader());

        $this->assertEquals(40, $telegram->getDsmrVersion());
        $this->assertEquals('140622161029', $telegram->getTimestamp()->format('ymdHis'));
        $this->assertEquals('4530303035303031353538323031323134', $telegram->getEquipmentId());


        $this->assertEquals(000037.466, $telegram->getMeterreadingIn1()->getMeterReading());
        $this->assertEquals('kWh', $telegram->getMeterreadingIn1()->getUnit());
        $this->assertEquals(000011.423, $telegram->getMeterreadingIn2()->getMeterReading());
        $this->assertEquals('kWh', $telegram->getMeterreadingIn2()->getUnit());

        $this->assertEquals(000000.047, $telegram->getMeterreadingOut1()->getMeterReading());
        $this->assertEquals('kWh', $telegram->getMeterreadingOut1()->getUnit());
        $this->assertEquals(000000.000, $telegram->getMeterreadingOut2()->getMeterReading());
        $this->assertEquals('kWh', $telegram->getMeterreadingOut2()->getUnit());

        $this->assertEquals(1, $telegram->getCurrentTariff());
        $this->assertEquals(00.407, $telegram->getCurrentPowerIn()->getMeterReading());
        $this->assertEquals('kW', $telegram->getCurrentPowerIn()->getUnit());
        $this->assertEquals(00.000, $telegram->getCurrentPowerOut()->getMeterReading());
        $this->assertEquals('kW', $telegram->getCurrentPowerOut()->getUnit());
        $this->assertEquals(999.9, $telegram->getCurrentTreshold()->getMeterReading());
        $this->assertEquals('kW', $telegram->getCurrentTreshold()->getUnit());
        $this->assertEquals(1, $telegram->getCurrentSwitchPosition());

        $this->assertEquals(6, $telegram->getPowerFailures());
        $this->assertEquals(0, $telegram->getLongPowerFailures());
        $this->assertEquals('', $telegram->getLongPowerFailuresLog());

        $this->assertEquals(1, $telegram->getVoltageSagsL1());
        $this->assertEquals(0, $telegram->getVoltageSagsL2());
        $this->assertEquals(0, $telegram->getVoltageSagsL3());

        $this->assertEquals(0, $telegram->getVoltageSwellsL1());
        $this->assertEquals(0, $telegram->getVoltageSwellsL2());
        $this->assertEquals(0, $telegram->getVoltageSwellsL3());

        $this->assertEquals(0, $telegram->getInstantaneousCurrentL1()->getMeterReading());
        $this->assertEquals('A', $telegram->getInstantaneousCurrentL1()->getUnit());
        $this->assertEquals(3, $telegram->getInstantaneousCurrentL2()->getMeterReading());
        $this->assertEquals('A', $telegram->getInstantaneousCurrentL2()->getUnit());
        $this->assertEquals(1, $telegram->getInstantaneousCurrentL3()->getMeterReading());
        $this->assertEquals('A', $telegram->getInstantaneousCurrentL3()->getUnit());

        $this->assertEquals(0.040, $telegram->getInstantaneousActivePowerInL1()->getMeterReading());
        $this->assertEquals('kW', $telegram->getInstantaneousActivePowerInL1()->getUnit());
        $this->assertEquals(0.217, $telegram->getInstantaneousActivePowerInL2()->getMeterReading());
        $this->assertEquals('kW', $telegram->getInstantaneousActivePowerInL2()->getUnit());
        $this->assertEquals(0.149, $telegram->getInstantaneousActivePowerInL3()->getMeterReading());
        $this->assertEquals('kW', $telegram->getInstantaneousActivePowerInL3()->getUnit());

        $this->assertEquals(0.0, $telegram->getInstantaneousActivePowerOutL1()->getMeterReading());
        $this->assertEquals('kW', $telegram->getInstantaneousActivePowerOutL1()->getUnit());
        $this->assertEquals(0.0, $telegram->getInstantaneousActivePowerOutL2()->getMeterReading());
        $this->assertEquals('kW', $telegram->getInstantaneousActivePowerOutL2()->getUnit());
        $this->assertEquals(0.0, $telegram->getInstantaneousActivePowerOutL3()->getMeterReading());
        $this->assertEquals('kW', $telegram->getInstantaneousActivePowerOutL3()->getUnit());

        $this->assertEquals('', $telegram->getMessageCode());
        $this->assertEquals('', $telegram->getMessageText());

        $this->assertEquals('A79E', $telegram->getCrc());
    }

    public function testChannelData()
    {
        $telegram = $this->parser->parse($this->getData());
        $this->assertEquals(1, $telegram->getChannel(1)->getId());
        $this->assertEquals(3, $telegram->getChannel(1)->getTypeId());
        $this->assertEquals('Gas', $telegram->getChannel(1)->getTypeDescription());
        $this->assertEquals('20140622160000', $telegram->getChannel(1)->getTimestamp()->format('YmdHis'));
        $this->assertEquals(0003.800, $telegram->getChannel(1)->getReadingValue()->getMeterReading());
        $this->assertEquals('m3', $telegram->getChannel(1)->getReadingValue()->getUnit());
        $this->assertEquals(1, $telegram->getChannel(1)->getValvePosition());

        $this->assertNull($telegram->getChannel(2));
        $this->assertNull($telegram->getChannel(3));
        $this->assertNull($telegram->getChannel(4));
    }

    public function testCrc()
    {
        $data = $this->getFixture('telegram_with_gas_meter2.txt');
        $telegram = $this->parser->parse($data);
        $this->assertEquals('4D8E', $telegram->getCrc());
    }

    protected function getData()
    {

        $fixture = $this->getFixture('telegram_with_gas_meter.txt');
        return $fixture;
    }
}
