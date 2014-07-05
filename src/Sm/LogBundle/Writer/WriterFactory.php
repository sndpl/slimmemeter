<?php

namespace Sm\LogBundle\Writer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WriterFactory
{
    const SCREEN = 'screen';
    const RRD = 'rrd';
    const GRAPH = 'graph';


    public function create($type, OutputInterface $output, LoggerInterface $logger)
    {
        switch ($type) {
            case self::SCREEN:
                return new Screen($output);
                break;
            case self::RRD:
                return new Rrd($logger);
                break;
            case self::GRAPH:
                return new Graph($logger);
                break;
            default:
                throw new \InvalidArgumentException("$type is not a valid Writer");
        }
    }
}
