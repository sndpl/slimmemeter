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
        $graphOptions = [
            'gas_show_weather' => $this->getContainer()->getParameter('graph_options.gas.show_weather'),
            'power_show_baseline' => $this->getContainer()->getParameter('graph_options.power.show_baseload'),
            'power_contract_delivery' => $this->getContainer()->getParameter('contract.delivery'),
            'power_contract_single_rate' => $this->getContainer()->getParameter('contract.single.rate'),
            'power_contract_highlow_region' => $this->getContainer()->getParameter('contract.highlow.region')
        ];
        $writerFactory = new WriterFactory();
        $writer = $writerFactory->create(WriterFactory::GRAPH, $output, $logger);
        $writer->setSettings($graphOptions);
        $writer->write();
    }

}
