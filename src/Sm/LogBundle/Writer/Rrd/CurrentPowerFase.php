<?php

namespace Sm\LogBundle\Writer\Rrd;

use Sm\LogBundle\Dto\Telegram;

class CurrentPowerFase extends AbstractRrdWriter
{
    protected $dbFilename = 'current_power_fase';

    /**
     * @return array
     */
    protected function getDbOptions()
    {
        return [
            "--step", "10",
            "DS:powerinfase1:GAUGE:60:U:U",
            "DS:powerinfase2:GAUGE:60:U:U",
            "DS:powerinfase3:GAUGE:60:U:U",
            "DS:poweroutfase1:GAUGE:60:U:U",
            "DS:poweroutfase2:GAUGE:60:U:U",
            "DS:poweroutfase3:GAUGE:60:U:U",
            "RRA:AVERAGE:0.5:1:12",
            "RRA:AVERAGE:0.5:1:288",
            "RRA:AVERAGE:0.5:12:168",
            "RRA:AVERAGE:0.5:12:720",
            "RRA:AVERAGE:0.5:288:365"
        ];
    }

    /**
     * @param Telegram $telegram
     * @param $lastUpdate
     * @return array
     */
    protected function getUpdateOptions(Telegram $telegram, $lastUpdate)
    {
        $this->logger->debug('Add Current Power Fase data:');
        return [
            $telegram->timestamp->format('U') . ':' .
            ($telegram->instantaneous_active_power_in_l1*1000) . ':' .
            ($telegram->instantaneous_active_power_in_l2*1000) . ':' .
            ($telegram->instantaneous_active_power_in_l3*1000) . ':' .
            ($telegram->instantaneous_active_power_out_l1*1000) . ':' .
            ($telegram->instantaneous_active_power_out_l2*1000) . ':' .
            ($telegram->instantaneous_active_power_out_l3*1000)
        ];
    }
}
