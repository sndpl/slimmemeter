<?php

namespace Sm\LogBundle\Writer\Graph;

class CurrentPowerFase extends AbstractGraphWriter
{
    /**
     * @var string
     */
    protected $dbFilename = 'current_power_fase';

    /**
     * @var string
     */
    protected $graphFilename = 'current_power_fase';


    /**
     * @param string $start
     * @param string $name
     * @return array
     */
    protected function getGraphOptions($start = '-1h', $name = '', $settings)
    {
        $currentDate = date('d-m-Y H:i:s');
        if ($settings['power_contract_delivery'] === false) {
            $options = [
                "--width", "800",
                "--height", "220",
                "--vertical-label", "usage [Wh]",
                "--lower", "0.3",
                "--start", $start,
                "--title", "Current Power Usage Fase - {$name}",
                "--watermark", "{$currentDate}",
                "DEF:fase1in={$this->getDbFilename()}:powerinfase1:AVERAGE",
                "DEF:fase2in={$this->getDbFilename()}:powerinfase2:AVERAGE",
                "DEF:fase3in={$this->getDbFilename()}:powerinfase3:AVERAGE",
                "COMMENT: \l",
                "COMMENT:-----------------------------------------------------------------------------------\l",
                "COMMENT:             ",
                "COMMENT:    Current",
                "COMMENT:         Min",
                "COMMENT:     Average",
                "COMMENT:         Max\l",
                "COMMENT:-----------------------------------------------------------------------------------\l",
                "AREA:fase1in#57A310:Fase 1 in ",
                "GPRINT:fase1in:LAST:%12.2lf",
                "GPRINT:fase1in:MIN:%12.2lf",
                "GPRINT:fase1in:AVERAGE:%12.2lf",
                "GPRINT:fase1in:MAX:%12.2lf\\l",
                "AREA:fase2in#10A390:Fase 2 in :STACK",
                "GPRINT:fase2in:LAST:%12.2lf",
                "GPRINT:fase2in:MIN:%12.2lf",
                "GPRINT:fase2in:AVERAGE:%12.2lf",
                "GPRINT:fase2in:MAX:%12.2lf\\l",
                "AREA:fase3in#105AA3:Fase 3 in :STACK",
                "GPRINT:fase3in:LAST:%12.2lf",
                "GPRINT:fase3in:MIN:%12.2lf",
                "GPRINT:fase3in:AVERAGE:%12.2lf",
                "GPRINT:fase3in:MAX:%12.2lf\\l",
            ];
        } else {
            $options = [
                "--width", "800",
                "--height", "220",
                "--vertical-label", "usage [Wh]",
                "--lower", "0.3",
                "--start", $start,
                "--title", "Current Power Usage Fase - {$name}",
                "--watermark", "{$currentDate}",
                "DEF:fase1in={$this->getDbFilename()}:powerinfase1:AVERAGE",
                "DEF:fase2in={$this->getDbFilename()}:powerinfase2:AVERAGE",
                "DEF:fase3in={$this->getDbFilename()}:powerinfase3:AVERAGE",
                "DEF:fase1out={$this->getDbFilename()}:poweroutfase1:AVERAGE",
                "DEF:fase2out={$this->getDbFilename()}:poweroutfase2:AVERAGE",
                "DEF:fase3out={$this->getDbFilename()}:poweroutfase3:AVERAGE",
                "COMMENT: \l",
                "COMMENT:-----------------------------------------------------------------------------------\l",
                "COMMENT:             ",
                "COMMENT:    Current",
                "COMMENT:         Min",
                "COMMENT:     Average",
                "COMMENT:         Max\l",
                "COMMENT:-----------------------------------------------------------------------------------\l",
                "AREA:fase1in#57A310:Fase 1 in ",
                "GPRINT:fase1in:LAST:%12.2lf",
                "GPRINT:fase1in:MIN:%12.2lf",
                "GPRINT:fase1in:AVERAGE:%12.2lf",
                "GPRINT:fase1in:MAX:%12.2lf\\l",
                "AREA:fase2in#10A390:Fase 2 in :STACK",
                "GPRINT:fase2in:LAST:%12.2lf",
                "GPRINT:fase2in:MIN:%12.2lf",
                "GPRINT:fase2in:AVERAGE:%12.2lf",
                "GPRINT:fase2in:MAX:%12.2lf\\l",
                "AREA:fase3in#105AA3:Fase 3 in :STACK",
                "GPRINT:fase3in:LAST:%12.2lf",
                "GPRINT:fase3in:MIN:%12.2lf",
                "GPRINT:fase3in:AVERAGE:%12.2lf",
                "GPRINT:fase3in:MAX:%12.2lf\\l",
                "AREA:fase1out#9EA310:Fase 1 out",
                "GPRINT:fase1out:LAST:%12.2lf",
                "GPRINT:fase1out:MIN:%12.2lf",
                "GPRINT:fase1out:AVERAGE:%12.2lf",
                "GPRINT:fase1out:MAX:%12.2lf\\l",
                "AREA:fase2out#A37510:Fase 2 out:STACK",
                "GPRINT:fase2out:LAST:%12.2lf",
                "GPRINT:fase2out:MIN:%12.2lf",
                "GPRINT:fase2out:AVERAGE:%12.2lf",
                "GPRINT:fase2out:MAX:%12.2lf\\l",
                "AREA:fase3out#A33A10:Fase 3 out:STACK",
                "GPRINT:fase3out:LAST:%12.2lf",
                "GPRINT:fase3out:MIN:%12.2lf",
                "GPRINT:fase3out:AVERAGE:%12.2lf",
                "GPRINT:fase3out:MAX:%12.2lf\\l",
            ];
        }

        return $options;

    }
}
