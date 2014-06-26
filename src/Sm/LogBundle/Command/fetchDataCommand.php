<?php
namespace Sm\LogBundle\Command;

use Sm\LogBundle\Parser\Parser;
use Sm\LogBundle\Writer\Screen;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sm\LogBundle\Serial\Serial;

/**
 * A command that runs constantly in the background, waiting for new queue
 * jobs to arrive and then immediately executing them.
 */
class fetchDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('log:fetch-data')
            ->addOption('max-runtime', 'r', InputOption::VALUE_REQUIRED, 'The maximum runtime in seconds.', 900)
            ->setDescription('Runs as a worker fetching data from the P1 port')
            ->addOption('logInterval', 'l', InputOption::VALUE_OPTIONAL, 'Log frequency in 10 second-units', 1)
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Output mode (db, screen, csv)', 'db')
            ->addOption('test', 't', InputOption::VALUE_NONE, 'Use test data (meter + gas meter)');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = time();

        $maxRuntime = (integer) $input->getOption('max-runtime');
        if ($maxRuntime <= 0) {
            throw new \InvalidArgumentException('The maximum runtime must be greater than zero.');
        }

        $outputMode = (string) $input->getOption('output');
        $logInterval = (integer) $input->getOption('logInterval');

        // If we receive a request to shutdown cleanly (SIGTERM) or a more
        // immediate CTRL+C (SIGINT), then we wait until the end of the loop.
        $terminate = false;
        $terminationHandler = function () use (&$terminate, $output) {
            $output->writeln('Signal received. Exiting...');
            $terminate = true;
        };
        pcntl_signal(SIGTERM, $terminationHandler);
        pcntl_signal(SIGINT, $terminationHandler);

        $parser = new Parser();
        $writer = new Screen($output);

        if ($input->getOption('test')) {
            $read = file_get_contents(__DIR__ . '/../Resources/fixtures/telegram_with_gas_meter.txt');
            $data = $parser->parse($read);
            $writer->write($data);
            return 0;
        }
        $serial = $this->getSerialPort();

        // Only allow running the jobs we set on the command line. This allows
        // us to group and throttle the workers for particular tasks.
        do {
            $output->writeln('Fetch data - ' . date("H:i:s"));
            $read = $serial->readPort();
            if (trim($read) != '') {
                $data = $parser->parse($read);
                $writer->write($data);
            }
            sleep(1);

            pcntl_signal_dispatch();
        } while (!$terminate && (time() - $startTime) < $maxRuntime);

        $serial->deviceClose();
        return 0;
    }

    protected function getSerialPort()
    {
        $serial = new Serial;
        $serial->deviceSet("COM1");

        $serial->confBaudRate(115200);
        $serial->confParity("none");
        $serial->confCharacterLength(8);
        $serial->confStopBits(1);
        $serial->confFlowControl("xon/xoff");

        $serial->deviceOpen();

        return $serial;
    }

}
