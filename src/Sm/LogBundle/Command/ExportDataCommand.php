<?php
namespace Sm\LogBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('log:export-data')
            ->addOption('max-runtime', 'r', InputOption::VALUE_REQUIRED, 'The maximum runtime in seconds.', 900)
            ->setDescription('Runs as cron script to export data from the P1 port')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // -s now-3h -e now --step 300 DEF:a=/home/pi/temps/temperatures.rrd:temp:AVERAGE XPORT:a:"Livingroom" > /var/www/temps/data/temps3h.xml
        // -s now-1h -e now --step 10 DEF:a=current_power.rrd:powerin:AVERAGE DEF:b=current_power.rrd:powerout:AVERAGE XPORT:a:"Current power" XPORT:b:"CUrrent power low"
        $currentPower = $this->getExportDataCurrentPower();
        foreach ($currentPower as $data) {
            $xml = rdd_xport($data);

        }
    }

    protected function getDbFilename($filename)
    {
        $dir = __DIR__ . '/../../../../../data/';
        return $dir . $filename . '.rrd';
    }

    protected function getExportDataCurrentPower()
    {
        $db = $this->getDbFilename('current_power');
        $exports = [];

        $currentPower1h = [
            '-s', 'now-1h',
            '-e', 'now',
            '--step', '10',
            'DEF:a='.$db.':powerin_t1:AVERAGE',
            'DEF:b='.$db.':powerin_t2:AVERAGE',
            'DEF:c='.$db.':powerout_t1:AVERAGE',
            'DEF:d='.$db.':powerout_t2:AVERAGE',
            'XPORT:a:"Current power in T1"',
            'XPORT:b:"Current power in T1"',
            'XPORT:b:"Current power out T2"',
            'XPORT:b:"Current power out T2"',
        ];

        $currentPower1d = [
            '-s', 'now-1d',
            '-e', 'now',
            '--step', '10',
            'DEF:a='.$db.':powerin_t1:AVERAGE',
            'DEF:b='.$db.':powerin_t2:AVERAGE',
            'DEF:c='.$db.':powerout_t1:AVERAGE',
            'DEF:d='.$db.':powerout_t2:AVERAGE',
            'XPORT:a:"Current power in T1"',
            'XPORT:b:"Current power in T1"',
            'XPORT:b:"Current power out T2"',
            'XPORT:b:"Current power out T2"',
        ];

        $exports[] = $currentPower1h;
        $exports[] = $currentPower1d;
        return $exports;
    }
}
