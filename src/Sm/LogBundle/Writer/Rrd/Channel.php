<?php

namespace Sm\LogBundle\Writer\Rrd;

use Sm\LogBundle\Dto\Telegram;
use Symfony\Component\Console\Output\OutputInterface;

class Channel extends AbstractRrdWriter
{
    protected $dbFilename = '';
    protected $channelId;
    protected $channelData;

    public function __construct($channelId, $logger)
    {
        $this->logger = $logger;
        $this->channelId = $channelId;
        $this->dbFilename = 'channel_' . $this->getChannelName($channelId);

        $filename = $this->getDbFilename();
        if (!file_exists($filename)) {
            $this->createRrdFile();
        }
    }

    protected function getChannelName($channelId)
    {
        switch($channelId) {
            case 1:
                return 'one';
                break;
            case 2:
                return 'two';
                break;
            case 3:
                return 'three';
                break;
            case 4:
                return 'four';
                break;
        }
        return 'unknown';
    }

    /**
     * @return array
     */
    protected function getDbOptions()
    {
        return [
            "--step", "3600",
            "DS:meterreading:GAUGE:7200:U:U",
            "DS:consuming:GAUGE:7200:U:U",
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
    protected function getUpdateOptions($telegram, $lastUpdate)
    {
        $lastUpdateTimestamp = 0;
        $lastMeterReading = 0;
        if (is_array($lastUpdate)) {
            $lastUpdateTimestamp = $lastUpdate['last_update'];
            $lastMeterReading = $lastUpdate['data'][0];
        }

        $channel = 'channel' . $this->channelId;
        $channelData = $telegram->$channel;
        $this->channelData = $channelData;

        if (is_null($this->channelData) || ($channelData->timestamp->format('U') - $lastUpdateTimestamp) < 61) {
            $this->logger->debug('Channel ' . $this->channelId . ': No data to add');
            return null;
        }

        $consuming = $channelData->meterReading - $lastMeterReading;
        $this->logger->debug('Add Channel ' . $this->channelId . ' data: MeterReading: ' . $channelData->meterReading . $channelData->unit . ' | Consuming: ' . $consuming . $channelData->unit);

        return [
            $channelData->timestamp->format('U') . ':' .
            $channelData->meterReading . ':' .
            $consuming
        ];
    }
}
