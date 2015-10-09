<?php
namespace Sm\LogBundle\Writer;

use Psr\Log\LoggerInterface;
use Sm\LogBundle\Writer\Graph\CurrentPower;
use Sm\LogBundle\Writer\Graph\CurrentPowerFase;
use Sm\LogBundle\Writer\Graph\Channel;
use Symfony\Component\Console\Output\OutputInterface;

class Graph
{
    protected $graphWriters = [];
    protected $settings = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->registerWriter(new CurrentPower($logger));
        $this->registerWriter(new CurrentPowerFase($logger));
        $this->registerWriter(new Channel(1, $logger));
        $this->registerWriter(new Channel(2, $logger));
        $this->registerWriter(new Channel(3, $logger));
        $this->registerWriter(new Channel(4, $logger));
    }

    public function setSettings($settings = [])
    {
        $this->settings = $settings;
    }

    protected function registerWriter($writer)
    {
        $this->graphWriters[] = $writer;
    }

    public function write()
    {
        /** @var CurrentPower $writer */
        foreach($this->graphWriters as $writer)
        {
            $writer->updateGraph($this->settings);
        }
    }
}
