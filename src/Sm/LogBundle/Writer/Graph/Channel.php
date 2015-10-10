<?php

namespace Sm\LogBundle\Writer\Graph;

use Psr\Log\LoggerInterface;
use Sm\LogBundle\Dto\Telegram;
use Sm\LogBundle\Dto\Channel as ChannelDto;


class Channel extends AbstractGraphWriter
{
    /**
     * @var string
     */
    protected $dbFilename = '';

    /**
     * @var string
     */
    protected $graphFilename = '';

    /**
     * @var int
     */
    protected $channelId;

    /**
     * @var null|ChannelDto
     */
    protected $channelData;

    /**
     * @param int $channelId
     * @param LoggerInterface $logger
     * @throws \Exception
     */
    public function __construct($channelId, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->channelId = $channelId;
        $channelName = $this->getChannelName($channelId);
        $this->dbFilename = 'channel_' . $channelName;
        $this->graphFilename = 'channel_' . $channelName;

        $filename = $this->getDbFilename();
        if (!file_exists($filename)) {
            throw new \Exception('No channel database found: ' . $this->dbFilename);
        }
        $lastTelegram = $this->getLastTelegram();
        $this->channelData = $lastTelegram->getChannel($channelId);
    }

    /**
     * Convert channel id to name
     * @param integer $channelId
     * @return string
     */
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
     * @return Telegram
     */
    protected function getLastTelegram()
    {
        $file = __DIR__ . '/../../../../../data/lastTelegram';
        $data = file_get_contents($file);
        if ($data !== false) {
            $telegram = unserialize($data);
        } else {
            $telegram = new Telegram();
        }
        return $telegram;
    }

    /**
     * @param string $start
     * @param string $name
     * @param $settings
     * @return array
     */
    protected function getGraphOptions($start = '-1h', $name = '', $settings)
    {
        if ($start == '-1h') {
            return false;
        }
        $unit = '';
        $typeDescription = 'not installed';
        if (!is_null($this->channelData)) {
            $unit = $this->channelData->getReadingValue()->getUnit();
            $typeDescription = $this->channelData->getTypeDescription();
        }
        $currentDate = date('d-m-Y H:i:s');
        return [
            "--width", "800",
            "--height", "200",
            "--vertical-label", 'usage [' . $unit . ']',
            "--lower", "0.3",
            "--start", $start,
            "--title", "Channel 1 - {$typeDescription} {$name}",
            "--watermark", "{$currentDate}",
            "DEF:consuming={$this->getDbFilename()}:consuming:AVERAGE",
            "AREA:consuming#264A9C:Consuming {$typeDescription}\\r",
        ];
    }
}
