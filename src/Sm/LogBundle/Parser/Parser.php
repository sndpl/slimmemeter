<?php
namespace Sm\LogBundle\Parser;

use Sm\LogBundle\Dto\Telegram;
use Sm\LogBundle\Dto\Channel;


/**
 * Class Parser
 * @package Sm\LogBundle\Parser
 *
 * /XMX5LGBBFFB231096081
 *
 * 1-3:0.2.8(40)
 * 0-0:1.0.0(140622161029S)
 * 0-0:96.1.1(4530303035303031353538323031323134)
 * 1-0:1.8.1(000037.466*kWh)
 * 1-0:2.8.1(000000.047*kWh)
 * 1-0:1.8.2(000011.423*kWh)
 * 1-0:2.8.2(000000.000*kWh)
 * 0-0:96.14.0(0001)
 * 1-0:1.7.0(00.407*kW)
 * 1-0:2.7.0(00.000*kW)
 * 0-0:17.0.0(999.9*kW)
 * 0-0:96.3.10(1)
 * 0-0:96.7.21(00006)
 * 0-0:96.7.9(00000)
 * 1-0:99.97.0(0)(0-0:96.7.19)
 * 1-0:32.32.0(00001)
 * 1-0:52.32.0(00000)
 * 1-0:72.32.0(00000)
 * 1-0:32.36.0(00000)
 * 1-0:52.36.0(00000)
 * 1-0:72.36.0(00000)
 * 0-0:96.13.1()
 * 0-0:96.13.0()
 * 1-0:31.7.0(000*A)
 * 1-0:51.7.0(003*A)
 * 1-0:71.7.0(001*A)
 * 1-0:21.7.0(00.040*kW)
 * 1-0:41.7.0(00.217*kW)
 * 1-0:61.7.0(00.149*kW)
 * 1-0:22.7.0(00.000*kW)
 * 1-0:42.7.0(00.000*kW)
 * 1-0:62.7.0(00.000*kW)
 * 0-1:24.1.0(003)
 * 0-1:96.1.0(4730303136353631323037373133373134)
 * 0-1:24.2.1(140622160000S)(00003.800*m3)
 * 0-1:24.4.0(1)
 * !A79E
 */
class Parser
{
    protected $telegram;
    protected $channel;

    public function __construct()
    {
        $this->telegram = new Telegram();
        $this->channel = [
            1 => new Channel(),
            2 => new Channel(),
            3 => new Channel(),
            4 => new Channel()
        ];
    }

    public function parse($data)
    {
        if (substr($data, 0, 1) !== '/') {
            return false;
        }
        // Loop through data
        $this->telegram->timestamp = new \DateTime();
        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            $this->parseLine($line);
        }
        for($i = 1; $i < 5; $i++) {
            if ($this->channel[$i]->id != 0) {
                $channel = 'channel' . $i;
                $this->telegram->$channel = $this->channel[$i];
            }
        }
        return $this->telegram;
    }

    protected function parseLine($data)
    {
        if (substr($data, 0, 1) === '/') {
            #Header information
            #eg. /KMP5 KA6U001511209910 (Kamstrup Enexis)
            #eg. /ISk5\2ME382-1003 (InkraEmeco Liander)
            #eg. /XMX5XMXABCE000018914 (Landis&Gyr Stedin, Xemex communicatiemodule)
            #eg. /KFM5KAIFA-METER (Kaifa)
            $this->telegram->meter_supplier = substr($data, 1, 3);
            $this->telegram->header = substr($data, 1, strlen($data) - 1);
        } elseif (substr($data, 4, 5) === '1.0.0') {
            #P1 Timestamp (DSMR 4)
            #eg. 0-0:1.0.0(101209113020W)
            if (substr($data, 10, 13) != "000101010000W") {
                #Check if meter clock is running
                $timestamp = '20' . substr($data, 10, 12);
                $this->telegram->timestamp = new \DateTime($timestamp);
            } else {
                printf("warning: invalid P1-telegram date/time value '%s', system date/time used instead: '%s'", substr($data, 10, 12), $this->telegram->timestamp->format('ymdHis'));
            }
        } elseif (substr($data, 4, 5) === '0.2.8') {
            #DSMR Version (DSMR V4)
            #eg. 1-3:0.2.8(40)
            $this->telegram->dsmr_version = substr($data, 10, strlen($data) - 11);
        } elseif (substr($data, 4, 6) === '96.1.1') {
            #####
            #Channel 0 = E
            #####
            #Equipment identifier (Electricity)
            #eg. 0-0:96.1.1(204B413655303031353131323039393130)
            $this->telegram->equipment_id = substr($data, 11, strlen($data) - 12);
        } elseif (substr($data, 4, 5) === '1.8.1') {
            #Meter Reading electricity delivered to client (normal tariff)
            #eg. 1-0:1.8.1(00721.000*kWh) (DSMR 3)
            #eg. 1-0:1.8.1(000038.851*kWh) (DSMR 4)
            $value = substr($data, 10, strlen($data) - 11);
            $tmp = explode('*', $value);
            $this->telegram->meterreading_in_1 = floatval($tmp[0]);
            $this->telegram->unit_meterreading_in_1 = $tmp[1];
        } elseif (substr($data, 4, 5) === '1.8.2') {
            #Meter Reading electricity delivered to client (low tariff)
            #eg. 1-0:1.8.2(00392.000*kWh)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->meterreading_in_2 = floatval($tmp[0]);
            $this->telegram->unit_meterreading_in_2 = $tmp[1];
        } elseif (substr($data, 4, 5) === '2.8.1') {
            #Meter Reading electricity delivered by client (normal tariff)
            #eg. 1-0:2.8.1(00000.000*kWh)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->meterreading_out_1 = floatval($tmp[0]);
            $this->telegram->unit_meterreading_out_1 = $tmp[1];
        } elseif (substr($data, 4, 5) === '2.8.2') {
            #Meter Reading electricity delivered by client (low tariff)
            #eg. 1-0:2.8.2(00000.000*kWh)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->meterreading_out_2 = floatval($tmp[0]);
            $this->telegram->unit_meterreading_out_2 = $tmp[1];
        } elseif (substr($data, 4, 7) === '96.14.0') {
            #Tariff indicator electricity
            #eg. 0-0:96.14.0(0001)
            #alternative 0-0:96.14.0(1)
            $this->telegram->current_tariff = substr($data, 12, strlen($data) - 13);
        } elseif (substr($data, 4, 5) == '1.7.0') {
            #Actual electricity power delivered to client (+P)
            #eg. 1-0:1.7.0(0000.91*kW)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->current_power_in = floatval($tmp[0]);
            $this->telegram->unit_current_power_in = $tmp[1];
        } elseif (substr($data, 4, 5) == '2.7.0') {
            #Actual electricity power delivered by client (-P)
            #1-0:2.7.0(0000.00*kW)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->current_power_out = floatval($tmp[0]);
            $this->telegram->unit_current_power_out = $tmp[1];
        } elseif (substr($data, 4, 6) == '17.0.0') {
            #Actual threshold Electricity
            #Companion standard, eg Kamstrup, Xemex
            #eg. 0-0:17.0.0(999*A)
            #Iskraemeco
            #eg. 0-0:17.0.0(0999.00*kW)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->current_treshold = floatval($tmp[0]);
            $this->telegram->unit_current_treshold = $tmp[1];
        } elseif (substr($data, 4, 7) === '96.3.10') {
            #Actual switch position Electricity (in/out/enabled).
            #eg. 0-0:96.3.10(1)
            $this->telegram->current_switch_position = substr($data, 12,1);
        } elseif (substr($data, 4, 7) === '96.7.21') {
            #Number of powerfailures in any phase (DSMR4)
            #eg. 0-0:96.7.21(00004)
            $this->telegram->powerfailures = intval(substr($data, 12, strlen($data) - 13));
        } elseif (substr($data, 4, 6) === '96.7.9') {
            #Number of long powerfailures in any phase (DSMR4)
            #eg. 0-0:96.7.9(00002)
            $this->telegram->long_powerfailures = intval(substr($data, 11, strlen($data) - 12));
        } elseif (substr($data, 4, 7) === '99.97.0') {
            #Powerfailure eventlog (DSMR4)
            #eg. 1-0:99:97.0(2)(0:96.7.19)(101208152415W)(0000000240*s)(101208151004W)(00000000301*s)
            #    1-0:99.97.0(0)(0-0:96.7.19)
            $this->telegram->long_powerfailures_log = substr($data, strrpos($data, '0:96.7.19') + 10, strlen($data));
        } elseif (substr($data, 4,7) === '32.32.0') {
            #Number of Voltage sags L1 (DSMR4)
            #eg. 1-0:32.32.0(00002)
            $this->telegram->voltage_sags_l1 = intval(substr($data, 12, strlen($data) - 13));
        } elseif (substr($data, 4, 7) === '52.32.0') {
            #Number of Voltage sags L2 (DSMR4)
            #eg. 1-0:52.32.0(00002)
            $this->telegram->voltage_sags_l2 = intval(substr($data, 12, strlen($data) - 13));
        } elseif (substr($data, 4, 7) === '72.32.0') {
            #Number of Voltage sags L3 (DSMR4)
            #eg. 1-0:72.32.0(00002)
            $this->telegram->voltage_sags_l3 = intval(substr($data, 12, strlen($data) - 13));
        } elseif (substr($data, 4, 7) === '32.36.0') {
            #Number of Voltage swells L1 (DSMR4)
            #eg. 1-0:32.36.0(00002)
            $this->telegram->voltage_swells_l1 = intval(substr($data, 12, strlen($data) - 13));
        } elseif (substr($data, 4, 7) === '52.36.0') {
            #Number of Voltage swells L2 (DSMR4)
            #eg. 1-0:52.36.0(00002)
            $this->telegram->voltage_swells_l1 = intval(substr($data, 12, strlen($data) - 13));
        } elseif (substr($data, 4, 7) === '72.36.0') {
            #Number of Voltage swells L3 (DSMR4)
            #eg. 1-0:72.36.0(00002)
            $this->telegram->voltage_swells_l1 = intval(substr($data, 12, strlen($data) - 13));
        } elseif (substr($data, 4, 6) === '31.7.0') {
            #Instantaneous current L1 in A (DSMR4)
            #eg. 1-0:31.7.0.255(001*A)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->instantaneous_current_l1 = floatval($tmp[0]);
            $this->telegram->unit_instantaneous_current_l1 = $tmp[1];
        } elseif (substr($data, 4, 6) === '51.7.0') {
            #Instantaneous current L2 in A (DSMR4)
            #eg. 1-0:51.7.0.255(002*A)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->instantaneous_current_l2 = floatval($tmp[0]);
            $this->telegram->unit_instantaneous_current_l2 = $tmp[1];
        } elseif (substr($data, 4, 6) === '71.7.0') {
            #Instantaneous current L3 in A (DSMR4)
            #eg. 1-0:71.7.0.255(003*A)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->instantaneous_current_l3 = floatval($tmp[0]);
            $this->telegram->unit_instantaneous_current_l3 = $tmp[1];
        } elseif (substr($data, 4, 6) === '21.7.0') {
            #Instantaneous active power L1 (+P) in W (DSMR4)
            #eg 1-0:21.7.0.255(01.111*kW)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->instantaneous_active_power_in_l1 = floatval($tmp[0]);
            $this->telegram->unit_instantaneous_active_power_in_l1 = $tmp[1];
        } elseif (substr($data, 4, 6) === '41.7.0') {
            #Instantaneous active power L2 (+P) in W (DSMR4)
            #eg 1-0:41.7.0.255(02.222*kW)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->instantaneous_active_power_in_l2 = floatval($tmp[0]);
            $this->telegram->unit_instantaneous_active_power_in_l2 = $tmp[1];
        } elseif (substr($data, 4, 6) === '61.7.0') {
            #Instantaneous active power L3 (+P) in W (DSMR4)
            #eg 1-0:61.7.0.255(03.333*kW)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->instantaneous_active_power_in_l3 = floatval($tmp[0]);
            $this->telegram->unit_instantaneous_active_power_in_l3 = $tmp[1];
        } elseif (substr($data, 4, 6) === '22.7.0') {
            #Instantaneous active power L1 (+P) in W  (DSMR4)
            #eg 1-0:22.7.0.255(04.444*kW)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->instantaneous_active_power_out_l1 = floatval($tmp[0]);
            $this->telegram->unit_instantaneous_active_power_out_l1 = $tmp[1];
        } elseif (substr($data, 4, 6) === '42.7.0') {
            #Instantaneous active power L2 (+P) in W  (DSMR4)
            #eg 1-0:42.7.0.255(05.555*kW)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->instantaneous_active_power_out_l2 = floatval($tmp[0]);
            $this->telegram->unit_instantaneous_active_power_out_l2 = $tmp[1];
        } elseif (substr($data, 4, 6) === '62.7.0') {
            #Instantaneous active power L3 (+P) in W (DSMR4)
            #eg 1-0:62.7.0.255(06.666*kW)
            $value = substr($data, 11, strlen($data) - 12);
            $tmp = explode('*', $value);
            $this->telegram->instantaneous_active_power_out_l3 = floatval($tmp[0]);
            $this->telegram->unit_instantaneous_active_power_out_l3 = $tmp[1];
        } elseif (substr($data, 4, 7) === '96.13.1') {
            #Text message code: numeric 8 digits
            #eg. 0-0:96.13.1()
            $this->telegram->message_code = substr($data, 12, strlen($data) - 13);
        } elseif (substr($data, 4, 7) === '96.13.0') {
            #Text message max 1024 characters.
            #eg. 0-0:96.13.0()
            $this->telegram->message_text = substr($data, 12, strlen($data) - 13);
        } elseif (substr($data, 4, 6) === '24.1.0') {  #Channels 1/2/3/4: MBus connected meters
            #Device-Type
            #eg. 0-1:24.1.0(3)
            #or 0-1:24.1.0(03) 3=Gas;5=Heat;6=Cooling
            #or 0-1:24.1.0(03) 3/7=Gas;5=Heat;6=Cooling (Standard OBIS: 1-Electricity / 4-HeatCostAllocation / 5-Cooling / 6-Heat / 7-Gas / 8-ColdWater / 9-HotWater)
            $channelId = $this->getChannelId($data);
            $typeId = intval(substr($data, 11, strlen($data) - 12));
            switch ($typeId) {
                case 3:
                case 7:
                    $typeDescription = 'Gas';
                    break;
                case 4:
                    $typeDescription = 'HeatCost';
                    break;
                case 5:
                    $typeDescription = 'Heat';
                    break;
                case 6:
                    $typeDescription = 'Cold';
                    break;
                case 8:
                    $typeDescription = 'Cold water';
                    break;
                case 9:
                    $typeDescription = 'Hot water';
                    break;
                default:
                    $typeDescription = 'Unknown';
            }
            $this->channel[$channelId]->id = $channelId;
            $this->channel[$channelId]->typeId = $typeId;
            $this->channel[$channelId]->typeDescription = $typeDescription;
        } elseif (substr($data, 4, 6) === '96.1.0') {
            #Equipment identifier
            #eg. 0-1:96.1.0(3238303039303031303434303132303130)
            $channelId = $this->getChannelId($data);
            $this->channel[$channelId]->equipmentId = substr($data, 11, strlen($data) - 12);
        } elseif (substr($data, 4, 6) === '24.3.0') {
            #Last hourly value delivered to client (DSMR < V4)
            #eg. Kamstrup/Iskraemeco:
            #0-1:24.3.0(110403140000)(000008)(60)(1)(0-1:24.2.1)(m3)
            #(00437.631)
            #eg. Companion Standard:
            #0-1:24.3.0(110403140000)(000008)(60)(1)(0-1:24.2.1)(m3)(00437.631)
            $channelId = $this->getChannelId($data);
        } elseif (substr($data, 4, 6) === '24.2.1') {
            #Last hourly value delivered to client (DSMR v4)
            #eg. 0-1:24.2.1(101209110000W)(12785.123*m3)
            $channelId = $this->getChannelId($data);
            $meterData = substr($data, 26, strlen($data) - 27);
            $tmp = explode('*', $meterData);
            $meterReading = floatval($tmp[0]);
            $unit = $tmp[1];
            $timestamp = '20' . substr($data, 11, 12);
            $this->channel[$channelId]->meterReading = $meterReading;
            $this->channel[$channelId]->unit = $unit;
            $this->channel[$channelId]->timestamp = new \DateTime($timestamp);
        } elseif (substr($data, 4, 6) === '24.4.0') {
            #Valve position (on/off/released)
            #eg. 0-1:24.4.0()
            #eg. 0-1:24.4.0(1)
            #Valveposition defaults to '1'(=Open) if invalid value
            $channelId = $this->getChannelId($data);
            $this->channel[$channelId]->valvePosition = substr($data, 11, strlen($data) - 12);
        } elseif (substr($data, 0, 1) == '' || substr($data, 0, 1) == ' ') {
            // Empty line
        } elseif (substr($data, 0, 1) === '!') {
            #in DSMR 4 telegrams there might be a checksum following the "!".
            #eg. !141B
            #CRC16 value calculated over the preceding characters in the data message (from “/” to “!” using the polynomial: x16+x15+x2+1).
            #the checksum is discarded
            $this->telegram->crc = substr($data, 1, strlen($data) -1);
        } else {
            print 'Unknown line: ' . $data . PHP_EOL;
            print substr($data, 4,6) . PHP_EOL;
        }
    }

    protected function getChannelId($data)
    {
        $channelId = intval(substr($data, 2,1));
        return $channelId;
    }


    protected function crc16citt($value)
    {
        $crc_table = array(
            0x0,  0x1021,  0x2042,  0x3063,  0x4084,  0x50a5,  0x60c6,  0x70e7,
            0x8108,  0x9129,  0xa14a,  0xb16b,  0xc18c,  0xd1ad,  0xe1ce,  0xf1ef,
            0x1231,  0x210,  0x3273,  0x2252,  0x52b5,  0x4294,  0x72f7,  0x62d6,
            0x9339,  0x8318,  0xb37b,  0xa35a,  0xd3bd,  0xc39c,  0xf3ff,  0xe3de,
            0x2462,  0x3443,  0x420,  0x1401,  0x64e6,  0x74c7,  0x44a4,  0x5485,
            0xa56a,  0xb54b,  0x8528,  0x9509,  0xe5ee,  0xf5cf,  0xc5ac,  0xd58d,
            0x3653,  0x2672,  0x1611,  0x630,  0x76d7,  0x66f6,  0x5695,  0x46b4,
            0xb75b,  0xa77a,  0x9719,  0x8738,  0xf7df,  0xe7fe,  0xd79d,  0xc7bc,
            0x48c4,  0x58e5,  0x6886,  0x78a7,  0x840,  0x1861,  0x2802,  0x3823,
            0xc9cc,  0xd9ed,  0xe98e,  0xf9af,  0x8948,  0x9969,  0xa90a,  0xb92b,
            0x5af5,  0x4ad4,  0x7ab7,  0x6a96,  0x1a71,  0xa50,  0x3a33,  0x2a12,
            0xdbfd,  0xcbdc,  0xfbbf,  0xeb9e,  0x9b79,  0x8b58,  0xbb3b,  0xab1a,
            0x6ca6,  0x7c87,  0x4ce4,  0x5cc5,  0x2c22,  0x3c03,  0xc60,  0x1c41,
            0xedae,  0xfd8f,  0xcdec,  0xddcd,  0xad2a,  0xbd0b,  0x8d68,  0x9d49,
            0x7e97,  0x6eb6,  0x5ed5,  0x4ef4,  0x3e13,  0x2e32,  0x1e51,  0xe70,
            0xff9f,  0xefbe,  0xdfdd,  0xcffc,  0xbf1b,  0xaf3a,  0x9f59,  0x8f78,
            0x9188,  0x81a9,  0xb1ca,  0xa1eb,  0xd10c,  0xc12d,  0xf14e,  0xe16f,
            0x1080,  0xa1,  0x30c2,  0x20e3,  0x5004,  0x4025,  0x7046,  0x6067,
            0x83b9,  0x9398,  0xa3fb,  0xb3da,  0xc33d,  0xd31c,  0xe37f,  0xf35e,
            0x2b1,  0x1290,  0x22f3,  0x32d2,  0x4235,  0x5214,  0x6277,  0x7256,
            0xb5ea,  0xa5cb,  0x95a8,  0x8589,  0xf56e,  0xe54f,  0xd52c,  0xc50d,
            0x34e2,  0x24c3,  0x14a0,  0x481,  0x7466,  0x6447,  0x5424,  0x4405,
            0xa7db,  0xb7fa,  0x8799,  0x97b8,  0xe75f,  0xf77e,  0xc71d,  0xd73c,
            0x26d3,  0x36f2,  0x691,  0x16b0,  0x6657,  0x7676,  0x4615,  0x5634,
            0xd94c,  0xc96d,  0xf90e,  0xe92f,  0x99c8,  0x89e9,  0xb98a,  0xa9ab,
            0x5844,  0x4865,  0x7806,  0x6827,  0x18c0,  0x8e1,  0x3882,  0x28a3,
            0xcb7d,  0xdb5c,  0xeb3f,  0xfb1e,  0x8bf9,  0x9bd8,  0xabbb,  0xbb9a,
            0x4a75,  0x5a54,  0x6a37,  0x7a16,  0xaf1,  0x1ad0,  0x2ab3,  0x3a92,
            0xfd2e,  0xed0f,  0xdd6c,  0xcd4d,  0xbdaa,  0xad8b,  0x9de8,  0x8dc9,
            0x7c26,  0x6c07,  0x5c64,  0x4c45,  0x3ca2,  0x2c83,  0x1ce0,  0xcc1,
            0xef1f,  0xff3e,  0xcf5d,  0xdf7c,  0xaf9b,  0xbfba,  0x8fd9,  0x9ff8,
            0x6e17,  0x7e36,  0x4e55,  0x5e74,  0x2e93,  0x3eb2,  0xed1,  0x1ef0);

        $crc = 0x0000;

        for ($i = 0; $i < strlen($value); $i++)
            $crc =  $crc_table[(($crc>>8) ^ ord($value[$i]))] ^ (($crc<<8) & 0x00FFFF);
        return $crc;
    }
}
