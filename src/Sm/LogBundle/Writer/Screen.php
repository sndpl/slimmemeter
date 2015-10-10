<?php
namespace Sm\LogBundle\Writer;

use Sm\LogBundle\Dto\ReadingValue;
use Sm\LogBundle\Dto\Telegram;
use Symfony\Component\Console\Output\OutputInterface;

class Screen
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output) {
        $this->output = $output;
    }

    /**
     * @param Telegram $data
     */
    public function write(Telegram $data)
    {
        $this->output->writeLn('P1 telegram received on: ' . $data->getTimestamp()->format('Y-m-d H:i:s'));
        $this->output->write('Meter supplier: ');
        switch ($data->getMeterSupplier()) {
            case 'KMP':
                $this->output->writeln('Kamstrup');
                break;
            case 'ISk':
                $this->output->writeln('IskraEmeco');
                break;
            case 'XMX':
                $this->output->writeln('Xemex');
                break;
            case 'KFM':
                $this->output->writeln('Kaifa');
                break;
            default:
                $this->output->writeln('Unknown - ' . $data->getMeterSupplier());
                break;
        }
        $this->output->writeln('Meter information: ' . $data->getHeader());
        $this->output->writeln(' 0. 2. 8 - DSMR versie: ' . $data->getDsmrVersion());
        $this->output->writeln('96. 1. 1 - Meter number electricity: ' . $data->getEquipmentId());
        $this->output->writeln(' 1. 8. 1 - Meter reading electricity in (T1): ' . $this->getReadingValue($data->getMeterreadingIn1()));
        $this->output->writeln(' 1. 8. 2 - Meter reading electricity in (T2): ' . $this->getReadingValue($data->getMeterreadingIn2()));
        $this->output->writeln(' 2. 8. 1 - Meter reading electricity out (T1): ' . $this->getReadingValue($data->getMeterreadingOut1()));
        $this->output->writeln(' 2. 8. 2 - Meter reading electricity out (T2): ' . $this->getReadingValue($data->getMeterreadingOut2()));
        $this->output->writeln('96.14. 0 - Current trariff: ' . $data->getCurrentTariff());
        $this->output->writeln(' 1. 7. 0 - Current power in (+P): ' . $this->getReadingValue($data->getCurrentPowerIn()));
        $this->output->writeln(' 2. 7. 0 - Current power out (-P): ' . $this->getReadingValue($data->getCurrentPowerOut()));
        $this->output->writeln('17. 0. 0 - Current threshold: ' . $this->getReadingValue($data->getCurrentTreshold()));
        $this->output->writeln('96. 3.10 - Switch position: ' . $data->getCurrentSwitchPosition());
        $this->output->writeln('96. 7.21 - Number of powerfailures: ' . $data->getPowerFailures());
        $this->output->writeln('96. 7. 9 - Number of long powerfailures: ' . $data->getLongPowerFailures());
        $this->output->writeln('99.97. 0 - Long powerfailures log: ' . $data->getLongPowerFailuresLog());

        $this->output->writeln('32.32. 0 - Short voltage sags in fase 1: ' . $data->getVoltageSagsL1());
        $this->output->writeln('52.32. 0 - Short voltage sags in fase 2: ' . $data->getVoltageSagsL2());
        $this->output->writeln('72.32. 0 - Short voltage sags in fase 3: ' . $data->getVoltageSagsL3());
        $this->output->writeln('32.36. 0 - Short voltage swells in fase 1: ' . $data->getVoltageSwellsL1());
        $this->output->writeln('52.36. 0 - Short voltage swells in fase 2: ' . $data->getVoltageSwellsL2());
        $this->output->writeln('72.36. 0 - Short voltage swells in fase 3: ' . $data->getVoltageSwellsL3());
        $this->output->writeln('31. 7. 0 - Current instantaneous in fase 1: ' . $this->getReadingValue($data->getInstantaneousCurrentL1()));
        $this->output->writeln('51. 7. 0 - Current instantaneous in fase 2: ' . $this->getReadingValue($data->getInstantaneousCurrentL2()));
        $this->output->writeln('71. 7. 0 - Current instantaneous in fase 3: ' . $this->getReadingValue($data->getInstantaneousCurrentL3()));

        $this->output->writeln('21. 7. 0 - Current instantaneous active power (+P) in fase 1: ' . $this->getReadingValue($data->getInstantaneousActivePowerInL1()));
        $this->output->writeln('41. 7. 0 - Current instantaneous active power (+P) in fase 2: ' . $this->getReadingValue($data->getInstantaneousActivePowerInL2()));
        $this->output->writeln('61. 7. 0 - Current instantaneous active power (+P) in fase 3: ' . $this->getReadingValue($data->getInstantaneousActivePowerInL3()));

        $this->output->writeln('22. 7. 0 - Current instantaneous active power (-P) out fase 1: ' . $this->getReadingValue($data->getInstantaneousActivePowerOutL1()));
        $this->output->writeln('42. 7. 0 - Current instantaneous active power (-P) out fase 2: ' . $this->getReadingValue($data->getInstantaneousActivePowerOutL2()));
        $this->output->writeln('62. 7. 0 - Current instantaneous active power (-P) out fase 3: ' . $this->getReadingValue($data->getInstantaneousActivePowerOutL3()));

        $this->output->writeln('96.13. 1 - Message code: ' . $data->getMessageCode());
        $this->output->writeln('96.31. 0 - Message text: ' . $data->getMessageText());

        // Channels
        for($i = 1; $i <= 4; $i++) {
            $channelData = $data->getChannel($i);
            if (is_null($channelData)) {
                $this->output->writeln('MBus Meterchannel ' . $i . ': Not installed');
            } else {
                $this->output->writeln('MBus Meterchannel ' . $i . ': Installed');

                $this->output->writeln('24. 1. 0 - Channel type: ' . $channelData->getTypeId() . ' (' . $channelData->getTypeDescription() .')');
                $this->output->writeln('91. 1. 0 - Meter number: ' . $channelData->getEquipmentId());
                if ($data->getDsmrVersion() != '40') {
                    $this->output->writeln('24. 3. 0 - Timestamp reading: ' . $channelData->getTimestamp()->format('Y-m-d H:i:s'));
                    $this->output->writeln('24. 3. 0 - Meter reading: ' . $this->getReadingValue($channelData->getReadingValue()));
                } else {
                    $this->output->writeln('24. 2. 1 - Timestamp reading: ' . $channelData->getTimestamp()->format('Y-m-d H:i:s'));
                    $this->output->writeln('24. 2. 1 - Meter reading: ' . $this->getReadingValue($channelData->getReadingValue()));
                }
                $this->output->writeln('24. 4. 0 - Actual valve position: ' . $channelData->getValvePosition());
            }
        }
    }

    /**
     * @param ReadingValue $readingValue
     *
     * @return string
     */
    protected function getReadingValue($readingValue)
    {
        if (!$readingValue instanceof ReadingValue) {
            return '-';
        }
        return number_format($readingValue->getMeterReading(), 3) . ' ' . $readingValue->getUnit();
    }
}
