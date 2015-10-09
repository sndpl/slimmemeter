<?php
namespace Sm\LogBundle\Command;

use Sm\LogBundle\Writer\Rrd\Weather;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchWeatherCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('log:fetch-weather')
            ->setDescription('Runs as cron script to fetch the weather')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');

        $openWeatherMap =$this->getContainer()->get('endroid.openweathermap');

        // Retrieve the current weather for Vlissingen
        $weather = $openWeatherMap->getWeather('Vlissingen,nl');
        if ($weather != false) {
            $writer = new Weather($logger);
            $writer->updateDb($weather);
        }
    }

}
