<?php

namespace Sm\LogBundle\Writer\Rrd;

use Psr\Log\LoggerInterface;
use Sm\LogBundle\Dto\Telegram;
use Sm\LogBundle\Dto\ReadingValue;


abstract class AbstractRrdWriter
{
    protected $dbFilename = '';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    abstract protected function getDbOptions();
    abstract protected function getUpdateOptions($data, $lastUpdate);

    /**
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $filename = $this->getDbFilename();
        if (!file_exists($filename)) {
            $this->createRrdFile();
        }
    }

    /**
     * @param Telegram $telegram
     * @throws \Exception
     */
    public function updateDb($data)
    {
        $filename = $this->getDbFilename();
        $lastUpdate = rrd_lastupdate($filename);
        $options = $this->getUpdateOptions($data, $lastUpdate);
        if (!is_null($options)) {
            $this->logger->debug('Update Db - ' . $filename);
            rrd_update(
                $filename,
                $options
            );
            $error = rrd_error();
            if ($error !== false) {
                throw new \Exception($error);
            }
        }
    }

    /**
     * Create Rrd database
     * @throws \Exception
     */
    protected function createRrdFile()
    {
        $options = $this->getDbOptions();
        $filename = $this->getDbFilename();
        $this->logger->info('Create Db - ' . $filename);

        rrd_create(
            $filename,
            $options
        );
        $error = rrd_error();
        if ($error !== false) {
            throw new \Exception($error);
        }
    }

    /**
     * @return string
     */
    protected function getDbFilename()
    {
        $dir = __DIR__ . '/../../../../../data/';
        return $dir . $this->dbFilename . '.rrd';
    }

    /**
     * @param ReadingValue $readingValue
     *
     * @return float|int
     */
    protected function getReadingValue($readingValue)
    {
        if ($readingValue instanceof ReadingValue) {
            return $readingValue->getMeterReading() * 1000;
        }
        return 0;
    }
}
