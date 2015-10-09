<?php

namespace Sm\LogBundle\Writer\Rrd;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class Weather extends AbstractRrdWriter
{
    protected $dbFilename = 'weather';

    public function __construct($logger)
    {
        $this->logger = $logger;

        $filename = $this->getDbFilename();
        if (!file_exists($filename)) {
            $this->createRrdFile();
        }
    }

    /**
     * @return array
     */
    protected function getDbOptions()
    {
        return [
            "--step", "3600",
            "DS:temperature:GAUGE:7200:U:U",
            "RRA:AVERAGE:0.5:1:12",
            "RRA:AVERAGE:0.5:1:288",
            "RRA:AVERAGE:0.5:12:168",
            "RRA:AVERAGE:0.5:12:720",
            "RRA:AVERAGE:0.5:288:365"
        ];
    }

    /**
     * @param $weather
     * @param $lastUpdate
     * @return array
     */
    protected function getUpdateOptions($weather, $lastUpdate)
    {
        $this->logger->debug('Add Weather Temperature: ' . $weather->main->temp);
        $timestamp = new \DateTime();


        return [
            $timestamp->format('U') . ':' .
            $weather->main->temp
        ];
    }
}
