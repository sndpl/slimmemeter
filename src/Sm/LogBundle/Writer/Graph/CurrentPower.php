<?php

namespace Sm\LogBundle\Writer\Graph;

/**
 * Class CurrentPower
 * @package Sm\LogBundle\Writer\Rdd
 */
class CurrentPower extends AbstractGraphWriter
{
    /**
     * @var string
     */
    protected $dbFilename = 'current_power';

    /**
     * @var string
     */
    protected $graphFilename = 'current_power';

    /**
     * @param string $start
     * @param string $name
     * @return array
     */
    protected function getGraphOptions($start = '-1h', $name = '')
    {
        $currentDate = date('d-m-Y H:i:s');
        return [
            "--width", "800",
            "--height", "200",
            "--vertical-label", "W",
            "--lower", "0.3",
            "--start", $start,
            "--watermark", "{$currentDate}",
            "--title", "Current Power Usage - {$name}",
            "DEF:powerin_t1={$this->getDbFilename()}:powerin_t1:AVERAGE",
            "DEF:powerout_t1={$this->getDbFilename()}:powerout_t1:AVERAGE",
            "DEF:powerin_t2={$this->getDbFilename()}:powerin_t2:AVERAGE",
            "DEF:powerout_t2={$this->getDbFilename()}:powerout_t2:AVERAGE",
            "COMMENT: \l",
            "COMMENT:-----------------------------------------------------------------------------------\l",
            "COMMENT:                           ",
            "COMMENT:    Current",
            "COMMENT:         Min",
            "COMMENT:     Average",
            "COMMENT:         Max\l",
            "COMMENT:-----------------------------------------------------------------------------------\l",
            "AREA:powerin_t1#264A9C:Ingaande stroom laag    ",
            "GPRINT:powerin_t1:LAST:%12.2lf",
            "GPRINT:powerin_t1:MIN:%12.2lf",
            "GPRINT:powerin_t1:AVERAGE:%12.2lf",
            "GPRINT:powerin_t1:MAX:%12.2lf\\l",
            "AREA:powerin_t2#85A0DD:Ingaande stroom normaal ",
            "GPRINT:powerin_t2:LAST:%12.2lf",
            "GPRINT:powerin_t2:MIN:%12.2lf",
            "GPRINT:powerin_t2:AVERAGE:%12.2lf",
            "GPRINT:powerin_t2:MAX:%12.2lf\\l",
            "AREA:powerout_t1#419334:Uitgaande stroom laag   ",
            "GPRINT:powerout_t1:LAST:%12.2lf",
            "GPRINT:powerout_t1:MIN:%12.2lf",
            "GPRINT:powerout_t1:AVERAGE:%12.2lf",
            "GPRINT:powerout_t1:MAX:%12.2lf\\l",
            "AREA:powerout_t2#A4D39D:Uitgaande stroom normaal",
            "GPRINT:powerout_t2:LAST:%12.2lf",
            "GPRINT:powerout_t2:MIN:%12.2lf",
            "GPRINT:powerout_t2:AVERAGE:%12.2lf",
            "GPRINT:powerout_t2:MAX:%12.2lf\\l",
        ];
    }
}
