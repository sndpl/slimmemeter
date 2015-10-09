<?php

namespace Sm\SiteBundle\Service;

class TelegramService
{
    /**
     * @var string
     */
    protected $dbRoot;

    /**
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->dbRoot = realpath($rootDir . '/../data');
    }

    /**
     * Get last saved telegram
     * @return bool|mixed
     */
    public function getLastTelegram()
    {
        $filename = $this->dbRoot . '/lastTelegram';
        if (file_exists($filename)) {
            $telegram = file_get_contents($filename);
            if ($telegram !== false) {
                return unserialize($telegram);
            }
        }
        return false;
    }
}
