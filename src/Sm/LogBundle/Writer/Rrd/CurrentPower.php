<?php

namespace Sm\LogBundle\Writer\Rrd;

use Sm\LogBundle\Dto\Telegram;

/**
 * Class CurrentPower
 * @package Sm\LogBundle\Writer\Rdd
 */
class CurrentPower extends AbstractRrdWriter
{
    protected $dbFilename = 'current_power';

    /**
     *
     * DS = DataSet
     * powerin = variable name
     * GAUGE = the data entered is absolute and should be entered as is without any manipulation or calculations done to it
     * 60 = Heartbeat timeout If data is not entered in at least 60 seconds then zeros are put into this DS record
     * 0 = the minimum value that will be accepted into the data base
     * 1 = the maximul value that will be accepted
     *
     * RRA = RRA directive defines how many values the the RRD database will archive and for how long
     * AVERAGE = the average of the data points is stored.
     * 0.5 = is an internal resolution value
     * 1 = specifies how many steps should be averaged before storing the final value
     * 12 = is how many "steps" we will store in the db
     *
     * @return array
     */
    protected function getDbOptions()
    {
        return [
            "--step", "10", // is the amount of time in seconds we expect data to be updated into the database
            "DS:powerin_t1:GAUGE:60:U:U", // Low tariff
            "DS:powerin_t2:GAUGE:60:U:U", // Normal tariff
            "DS:powerout_t1:GAUGE:60:U:U",
            "DS:powerout_t2:GAUGE:60:U:U",
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
        $powerInT1 = 0;
        $powerInT2 = 0;
        $powerOutT1 = 0;
        $powerOutT2 = 0;

        if (intval($telegram->current_tariff) == 2) {
            // Normal
            $powerInT2 = $telegram->current_power_in*1000;
            $powerOutT2 = $telegram->current_power_out*1000;
        } else {
            // Low
            $powerInT1 = $telegram->current_power_in*1000;
            $powerOutT1 = $telegram->current_power_out*1000;
        }
        $this->logger->debug('Add Current Power data: In T1: ' . $powerInT1 . 'W | In T2: ' . $powerInT2 . 'W | Out T1 ' .$powerOutT1 . 'W | Out T2' . $powerOutT2 .'W' );

        return [
            $telegram->timestamp->format('U') . ':' .
            $powerInT1 . ':' .
            $powerInT2 . ':' .
            $powerOutT1 . ':' .
            $powerOutT2
        ];
    }
}
