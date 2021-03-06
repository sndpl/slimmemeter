<?php
namespace Sm\LogBundle\Writer;

use Psr\Log\LoggerInterface;
use Sm\LogBundle\Dto\Telegram;
use Sm\LogBundle\Writer\Rrd\CurrentPower;
use Sm\LogBundle\Writer\Rrd\CurrentPowerFase;
use Sm\LogBundle\Writer\Rrd\Channel;
use Symfony\Component\Console\Output\OutputInterface;

class Rrd
{
    protected $rddWriters = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->registerWriter(new CurrentPower($logger));
        $this->registerWriter(new CurrentPowerFase($logger));

        $this->registerWriter(new Channel(1, $logger));
        $this->registerWriter(new Channel(2, $logger));
        $this->registerWriter(new Channel(3, $logger));
        $this->registerWriter(new Channel(4, $logger));
    }

    protected function registerWriter($writer)
    {
        $this->rddWriters[] = $writer;
    }

    public function write(Telegram $telegram)
    {
        /** @var CurrentPower $writer */
        foreach($this->rddWriters as $writer)
        {
            $writer->updateDb($telegram);
        }
    }
}
