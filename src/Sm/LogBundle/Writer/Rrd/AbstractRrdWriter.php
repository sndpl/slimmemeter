<?php

namespace Sm\LogBundle\Writer\Rrd;

use Psr\Log\LoggerInterface;
use Sm\LogBundle\Dto\Telegram;


abstract class AbstractRrdWriter
{
    protected $dbFilename = '';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    abstract protected function getDbOptions();
    abstract protected function getUpdateOptions(Telegram $telegram, $lastUpdate);

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
    public function updateDb(Telegram $telegram)
    {
        $filename = $this->getDbFilename();
        $lastUpdate = rrd_lastupdate($filename);
        $options = $this->getUpdateOptions($telegram, $lastUpdate);
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
}
