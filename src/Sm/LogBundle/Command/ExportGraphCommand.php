<?php
namespace Sm\LogBundle\Command;

use Sm\LogBundle\Writer\WriterFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportGraphCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('log:export-graph')
            ->setDescription('Runs as cron script to export graphs from the P1 port')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');
        $writerFactory = new WriterFactory();
        $writer = $writerFactory->create(WriterFactory::GRAPH, $output, $logger);
        $writer->write();
    }

}
