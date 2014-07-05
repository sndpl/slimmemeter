<?php
namespace Sm\LogBundle\Writer;

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
        //var_dump($data);
        $this->output->writeLn('P1 telegram received on: ' . $data->timestamp->format('Y-m-d H:i:s'));
        $this->output->write('Meter supplier: ');
        switch ($data->meter_supplier) {
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
                $this->output->writeln('Unknown - ' . $data->meter_supplier);
                break;
        }
        $this->output->writeln('Meter information: ' . $data->header);
        $this->output->writeln(' 0. 2. 8 - DSMR versie: ' . $data->dsmr_version);
        $this->output->writeln('96. 1. 1 - Meter number electricity: ' . $data->equipment_id);
        $this->output->writeln(' 1. 8. 1 - Meter reading electricity in (T1): ' . number_format($data->meterreading_in_1, 3) . ' ' . $data->unit_meterreading_in_1);
        $this->output->writeln(' 1. 8. 2 - Meter reading electricity in (T2): ' . number_format($data->meterreading_in_2, 3) . ' ' . $data->unit_meterreading_in_2);
        $this->output->writeln(' 2. 8. 1 - Meter reading electricity out (T1): ' . number_format($data->meterreading_out_1, 3) . ' ' . $data->unit_meterreading_out_1);
        $this->output->writeln(' 2. 8. 2 - Meter reading electricity out (T2): ' . number_format($data->meterreading_out_2, 3) . ' ' . $data->unit_meterreading_out_2);
        $this->output->writeln('96.14. 0 - Current trariff: ' . $data->current_tariff);
        $this->output->writeln(' 1. 7. 0 - Current power in (+P): ' . number_format($data->current_power_in, 3) . ' ' . $data->unit_current_power_in);
        $this->output->writeln(' 2. 7. 0 - Current power out (-P): ' . number_format($data->current_power_out, 3) . ' ' . $data->unit_current_power_out);
        $this->output->writeln('17. 0. 0 - Current threshold: ' . number_format($data->current_treshold, 3) . ' ' . $data->unit_current_treshold);
        $this->output->writeln('96. 3.10 - Switch position: ' . $data->current_switch_position);
        $this->output->writeln('96. 7.21 - Number of powerfailures: ' . $data->powerfailures);
        $this->output->writeln('96. 7. 9 - Number of long powerfailures: ' . $data->long_powerfailures);
        $this->output->writeln('99.97. 0 - Long powerfailures log: ' . $data->long_powerfailures_log);

        $this->output->writeln('32.32. 0 - Short voltage sags in fase 1: ' . $data->voltage_sags_l1);
        $this->output->writeln('52.32. 0 - Short voltage sags in fase 2: ' . $data->voltage_sags_l2);
        $this->output->writeln('72.32. 0 - Short voltage sags in fase 3: ' . $data->voltage_sags_l3);
        $this->output->writeln('32.36. 0 - Short voltage swells in fase 1: ' . $data->voltage_swells_l1);
        $this->output->writeln('52.36. 0 - Short voltage swells in fase 2: ' . $data->voltage_swells_l2);
        $this->output->writeln('72.36. 0 - Short voltage swells in fase 3: ' . $data->voltage_swells_l3);
        $this->output->writeln('31. 7. 0 - Current instantaneous in fase 1: ' . number_format($data->instantaneous_current_l1, 3) . ' ' . $data->unit_instantaneous_current_l1);
        $this->output->writeln('51. 7. 0 - Current instantaneous in fase 2: ' . number_format($data->instantaneous_current_l2, 3) . ' ' . $data->unit_instantaneous_current_l2);
        $this->output->writeln('71. 7. 0 - Current instantaneous in fase 3: ' . number_format($data->instantaneous_current_l3, 3) . ' ' . $data->unit_instantaneous_current_l3);

        $this->output->writeln('21. 7. 0 - Current instantaneous active power (+P) in fase 1: ' . number_format($data->instantaneous_active_power_in_l1, 3) . ' ' . $data->unit_instantaneous_active_power_in_l1);
        $this->output->writeln('41. 7. 0 - Current instantaneous active power (+P) in fase 2: ' . number_format($data->instantaneous_active_power_in_l2, 3) . ' ' . $data->unit_instantaneous_active_power_in_l2);
        $this->output->writeln('61. 7. 0 - Current instantaneous active power (+P) in fase 3: ' . number_format($data->instantaneous_active_power_in_l3, 3) . ' ' . $data->unit_instantaneous_active_power_in_l3);

        $this->output->writeln('22. 7. 0 - Current instantaneous active power (-P) out fase 1: ' . number_format($data->instantaneous_active_power_out_l1, 3) . ' ' . $data->unit_instantaneous_active_power_out_l1);
        $this->output->writeln('42. 7. 0 - Current instantaneous active power (-P) out fase 2: ' . number_format($data->instantaneous_active_power_out_l2, 3) . ' ' . $data->unit_instantaneous_active_power_out_l2);
        $this->output->writeln('62. 7. 0 - Current instantaneous active power (-P) out fase 3: ' . number_format($data->instantaneous_active_power_out_l3, 3) . ' ' . $data->unit_instantaneous_active_power_out_l3);

        $this->output->writeln('96.13. 1 - Message code: ' . $data->message_code);
        $this->output->writeln('96.31. 0 - Message text: ' . $data->message_text);

        // Channels
        for($i = 1; $i <= 4; $i++) {
            $channel = 'channel' . $i;
            $channelData = $data->$channel;
            if (is_null($channelData)) {
                $this->output->writeln('MBus Meterchannel ' . $i . ': Not installed');
            } else {
                $this->output->writeln('MBus Meterchannel ' . $i . ': Installed');

                $this->output->writeln('24. 1. 0 - Channel type: ' . $channelData->typeId . ' (' . $channelData->typeDescription .')');
                $this->output->writeln('91. 1. 0 - Meter number: ' . $channelData->equipmentId);
                if ($data->dsmr_version != '40') {
                    $this->output->writeln('24. 3. 0 - Timestamp reading: ' . $channelData->timestamp->format('Y-m-d H:i:s'));
                    $this->output->writeln('24. 3. 0 - Meter reading: ' . number_format($channelData->meterReading,3) . ' ' . $channelData->unit);
                } else {
                    $this->output->writeln('24. 2. 1 - Timestamp reading: ' . $channelData->timestamp->format('Y-m-d H:i:s'));
                    $this->output->writeln('24. 2. 1 - Meter reading: ' . number_format($channelData->meterReading,3) . ' ' . $channelData->unit);
                }
                $this->output->writeln('24. 4. 0 - Actual valve position: ' . $channelData->valvePosition);
            }
        }


    }
}
