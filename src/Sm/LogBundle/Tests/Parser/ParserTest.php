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

    public function ttestParser()
    {
        $telegram = $this->parser->parse($this->getData());

        $this->assertEquals('XMX', $telegram->meter_supplier);
        $this->assertEquals('XMX5LGBBFFB231096081', $telegram->header);
        $this->assertEquals(40, $telegram->dsmr_version);
        $this->assertEquals('140622161029', $telegram->timestamp->format('ymdHis'));
        $this->assertEquals('4530303035303031353538323031323134', $telegram->equipment_id);

        $this->assertEquals(000037.466, $telegram->meterreading_in_1);
        $this->assertEquals('kWh', $telegram->unit_meterreading_in_1);
        $this->assertEquals(000011.423, $telegram->meterreading_in_2);
        $this->assertEquals('kWh', $telegram->unit_meterreading_in_2);

        $this->assertEquals(000000.047, $telegram->meterreading_out_1);
        $this->assertEquals('kWh', $telegram->unit_meterreading_out_1);
        $this->assertEquals(000000.000, $telegram->meterreading_out_2);
        $this->assertEquals('kWh', $telegram->unit_meterreading_out_2);

        $this->assertEquals(1, $telegram->current_tariff);
        $this->assertEquals(00.407, $telegram->current_power_in);
        $this->assertEquals('kW', $telegram->unit_current_power_in);
        $this->assertEquals(00.000, $telegram->current_power_out);
        $this->assertEquals('kW', $telegram->unit_current_power_out);
        $this->assertEquals(999.9, $telegram->current_treshold);
        $this->assertEquals('kW', $telegram->unit_current_treshold);
        $this->assertEquals(1, $telegram->current_switch_position);

        $this->assertEquals(6, $telegram->powerfailures);
        $this->assertEquals(0, $telegram->long_powerfailures);
        $this->assertEquals('', $telegram->long_powerfailures_log);

        $this->assertEquals(1, $telegram->voltage_sags_l1);
        $this->assertEquals(0, $telegram->voltage_sags_l2);
        $this->assertEquals(0, $telegram->voltage_sags_l3);

        $this->assertEquals(0, $telegram->voltage_swells_l1);
        $this->assertEquals(0, $telegram->voltage_swells_l2);
        $this->assertEquals(0, $telegram->voltage_swells_l3);

        $this->assertEquals(0, $telegram->instantaneous_current_l1);
        $this->assertEquals('A', $telegram->unit_instantaneous_current_l1);
        $this->assertEquals(3, $telegram->instantaneous_current_l2);
        $this->assertEquals('A', $telegram->unit_instantaneous_current_l2);
        $this->assertEquals(1, $telegram->instantaneous_current_l3);
        $this->assertEquals('A', $telegram->unit_instantaneous_current_l3);

        $this->assertEquals(0.040, $telegram->instantaneous_active_power_in_l1);
        $this->assertEquals('kW', $telegram->unit_instantaneous_active_power_in_l1);
        $this->assertEquals(0.217, $telegram->instantaneous_active_power_in_l2);
        $this->assertEquals('kW', $telegram->unit_instantaneous_active_power_in_l2);
        $this->assertEquals(0.149, $telegram->instantaneous_active_power_in_l3);
        $this->assertEquals('kW', $telegram->unit_instantaneous_active_power_in_l3);

        $this->assertEquals(0.0, $telegram->instantaneous_active_power_out_l1);
        $this->assertEquals('kW', $telegram->unit_instantaneous_active_power_out_l1);
        $this->assertEquals(0.0, $telegram->instantaneous_active_power_out_l2);
        $this->assertEquals('kW', $telegram->unit_instantaneous_active_power_out_l2);
        $this->assertEquals(0.0, $telegram->instantaneous_active_power_out_l3);
        $this->assertEquals('kW', $telegram->unit_instantaneous_active_power_out_l3);

        $this->assertEquals('', $telegram->message_code);
        $this->assertEquals('', $telegram->message_text);

        $this->assertEquals('A79E', $telegram->crc);
    }

    public function testChannelData()
    {
        $telegram = $this->parser->parse($this->getData());
        $this->assertEquals(1, $telegram->channel1->id);
        $this->assertEquals(3, $telegram->channel1->typeId);
        $this->assertEquals('Gas', $telegram->channel1->typeDescription);
        $this->assertEquals('20140622160000', $telegram->channel1->timestamp->format('YmdHis'));
        $this->assertEquals(0003.800, $telegram->channel1->meterReading);
        $this->assertEquals('m3', $telegram->channel1->unit);
        $this->assertEquals(1, $telegram->channel1->valvePosition);

        $this->assertNull($telegram->channel2);
        $this->assertNull($telegram->channel3);
        $this->assertNull($telegram->channel4);
    }

    public function testCrc()
    {
        $data = $this->getFixture('telegram_with_gas_meter2.txt');
        $telegram = $this->parser->parse($data);
        $this->assertEquals('4D8E', $telegram->crc);
    }

    protected function getData()
    {

        $fixture = $this->getFixture('telegram_with_gas_meter.txt');
        return $fixture;
    }
}
