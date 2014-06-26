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
class FetchDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('log:fetch-data')
            ->addOption('max-runtime', 'r', InputOption::VALUE_REQUIRED, 'The maximum runtime in seconds.', 900)
            ->setDescription('Runs as a worker fetching data from the P1 port')
            ->addOption('logInterval', 'l', InputOption::VALUE_OPTIONAL, 'Log frequency in 10 second-units', 1)
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Output mode (rrd, screen)', 'rrd')
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

        $simulateMode = $input->getOption('test');
        if (!$simulateMode) {
            $serial = $this->getSerialPort();
        }

        // Only allow running the jobs we set on the command line. This allows
        // us to group and throttle the workers for particular tasks.
        do {
            $output->writeln('Fetch data - ' . date("H:i:s"));

            if ($simulateMode) {
                $read = $this->getReadData();
                $data = $parser->parse($read);
                $writer->write($data);
                sleep($this->deltaSleep(10));
            } else {
                $read = $serial->readPort();
                if (trim($read) != '') {
                    $data = $parser->parse($read);
                    $writer->write($data);
                }
                sleep(1);
            }

            pcntl_signal_dispatch();
        } while (!$terminate && (time() - $startTime) < $maxRuntime);

        if (!$simulateMode) {
            $serial->deviceClose();
        }
        return 0;
    }

    protected function getSerialPort()
    {
        $serial = new Serial();
        $serial->deviceSet("COM1");

        $serial->confBaudRate(115200);
        $serial->confParity("none");
        $serial->confCharacterLength(8);
        $serial->confStopBits(1);
        $serial->confFlowControl("xon/xoff");

        $serial->deviceOpen();

        return $serial;
    }

    protected function getReadData()
    {
        $values = [0 => [1,800,1500], 1 =>[1,500,700],2 => [1,500,700], 3 => [1,400,500], 4 => [1,300,400], 5 => [1,300,400],
            6 => [1,300,400], 7 => [0,400,500], 8 => [0,400,600], 9=> [0,400,600], 10 => [0,400,500], 11 => [0,300,400],
            12 => [0,300,400], 13 => [0,300,400], 14 => [0,300,400], 15 => [0,300,400], [16 => 0,300,400], 17 => [0,300,400],
            18 => [0,900,1500],19 => [0,700,1200],20 => [0,500,700],21 => [0,400,600],22 => [0,400,600],23 => [1,500,700]];

        # get current hour and generate simulated power
        $currentHour = intval(date('H')); # get current hour
        $value = $values[$currentHour];
        $powerValue = floatval(rand($value[1], $value[2]))/1000;
        $currentPower = number_format($powerValue, 2);
        $tariffCode = $value[0]; # get tariffcode 0=High,1=Low
        $currentDate = date('ymdHis');

        $telegram = <<< MSG
/XMX5LGBBFFB231096081

1-3:0.2.8(40)
0-0:1.0.0({$currentDate}S)
0-0:96.1.1(4530303035303031353538323031323134)
1-0:1.8.1(000037.466*kWh)
1-0:2.8.1(000000.047*kWh)
1-0:1.8.2(000011.423*kWh)
1-0:2.8.2(000000.000*kWh)
0-0:96.14.0(000{$tariffCode})
1-0:1.7.0({$currentPower}*kW)
1-0:2.7.0(00.000*kW)
0-0:17.0.0(999.9*kW)
0-0:96.3.10(1)
0-0:96.7.21(00006)
0-0:96.7.9(00000)
1-0:99.97.0(0)(0-0:96.7.19)
1-0:32.32.0(00001)
1-0:52.32.0(00000)
1-0:72.32.0(00000)
1-0:32.36.0(00000)
1-0:52.36.0(00000)
1-0:72.36.0(00000)
0-0:96.13.1()
0-0:96.13.0()
1-0:31.7.0(000*A)
1-0:51.7.0(003*A)
1-0:71.7.0(001*A)
1-0:21.7.0(00.040*kW)
1-0:41.7.0(00.217*kW)
1-0:61.7.0(00.149*kW)
1-0:22.7.0(00.000*kW)
1-0:42.7.0(00.000*kW)
1-0:62.7.0(00.000*kW)
0-1:24.1.0(003)
0-1:96.1.0(4730303136353631323037373133373134)
0-1:24.2.1(140622160000S)(00003.800*m3)
0-1:24.4.0(1)
!A79E
MSG;
        return $telegram;
    }

    /**
     * due to crontab startup delay, starttime is synced up to t seconds
     *
     * @param $t
     * @return mixed
     */
    protected function deltaSleep($t)
    {
        $sec = floatval(date('s.u')); # get seconds.microseconds
        $r = $sec % $t; # calculate remainder
        $delta = $t - $r; # calculate time diff in seconds so 0 <= delta <= t
        return floatval($delta); # return delta as sleep time
    }

}
