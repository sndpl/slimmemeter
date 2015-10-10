<?php
namespace Sm\LogBundle\Dto;

use Sm\LogBundle\Dto\ReadingValue;
use Sm\LogBundle\Dto\Channel;

/**
 * Class Telegram
 *
 * @package Sm\LogBundle\Dto
 */
class Telegram
{
    /**
     * @var bool
     */
    protected $complete;
    /**
     * @var string
     */
    protected $header;
    /**
     * @var string
     */
    protected $meterSupplier;
    /**
     * @var \DateTime
     */
    protected $timestamp;
    /**
     * @var string
     */
    protected $dsmrVersion = "30";
    /**
     * @var int
     */
    protected $powerFailures = 0;
    /**
     * @var int
     */
    protected $longPowerFailures = 0;
    /**
     * @var string
     */
    protected $longPowerFailuresLog = "";
    /**
     * @var int
     */
    protected $voltageSagsL1 = 0;
    /**
     * @var int
     */
    protected $voltageSagsL2 = 0;
    /**
     * @var int
     */
    protected $voltageSagsL3 = 0;
    /**
     * @var int
     */
    protected $voltageSwellsL1 = 0;
    /**
     * @var int
     */
    protected $voltageSwellsL2 = 0;
    /**
     * @var int
     */
    protected $voltageSwellsL3 = 0;
    /**
     * @var ReadingValue
     */
    protected $instantaneousCurrentL1;
    /**
     * @var ReadingValue
     */
    protected $instantaneousCurrentL2;
    /**
     * @var ReadingValue
     */
    protected $instantaneousCurrentL3;
    /**
     * @var ReadingValue
     */
    protected $instantaneousActivePowerInL1;
    /**
     * @var ReadingValue
     */
    protected $instantaneousActivePowerInL2;
    /**
     * @var ReadingValue
     */
    protected $instantaneousActivePowerInL3;
    /**
     * @var ReadingValue
     */
    protected $instantaneousActivePowerOutL1;
    /**
     * @var ReadingValue
     */
    protected $instantaneousActivePowerOutL2;
    /**
     * @var ReadingValue
     */
    protected $instantaneousActivePowerOutL3;
    /**
     * @var ReadingValue
     */
    protected $previousMeterreadingOut1 = 0;
    /**
     * @var ReadingValue
     */
    protected $previousMeterreadingOut2 = 0;
    /**
     * @var ReadingValue
     */
    protected $previousMeterreadingIn1 = 0;
    /**
     * @var ReadingValue
     */
    protected $previousMeterreadingIn2 = 0;
    /**
     * @var string
     */
    protected $equipmentId = '';
    /**
     * @var ReadingValue
     */
    protected $meterreadingIn1;
    /**
     * @var ReadingValue
     */
    protected $meterreadingIn2;
    /**
     * @var ReadingValue
     */
    protected $meterreadingOut1;
    /**
     * @var ReadingValue
     */
    protected $meterreadingOut2;
    /**
     * @var int
     */
    protected $currentTariff;
    /**
     * @var ReadingValue
     */
    protected $currentPowerIn;
    /**
     * @var ReadingValue
     */
    protected $currentPowerOut;
    /**
     * @var ReadingValue
     */
    protected $currentTreshold;
    /**
     * @var int
     */
    protected $currentSwitchPosition;
    /**
     * @var string
     */
    protected $messageCode;
    /**
     * @var string
     */
    protected $messageText;
    /**
     * @var string
     */
    protected $crc;
    /**
     * @var Channel[]
     */
    protected $channels;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->channels = [];
        $this->complete = false;
    }

    /**
     * @param \Sm\LogBundle\Dto\Channel $channel
     */
    public function setChannel(Channel $channel)
    {
        $this->channels[$channel->getId()] = $channel;
    }

    /**
     * @param $id
     *
     * @return bool|Channel
     */
    public function getChannel($id)
    {
        if (array_key_exists($id, $this->channels)) {
            return $this->channels[$id];
        }

        return null;
    }

    /**
     * @return boolean
     */
    public function isComplete()
    {
        return $this->complete;
    }

    /**
     * @param boolean $complete
     */
    public function setComplete($complete = true)
    {
        $this->complete = $complete;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getMeterSupplier()
    {
        return $this->meterSupplier;
    }

    /**
     * @param string $meterSupplier
     */
    public function setMeterSupplier($meterSupplier)
    {
        $this->meterSupplier = $meterSupplier;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getDsmrVersion()
    {
        return $this->dsmrVersion;
    }

    /**
     * @param string $dsmrVersion
     */
    public function setDsmrVersion($dsmrVersion)
    {
        $this->dsmrVersion = $dsmrVersion;
    }

    /**
     * @return int
     */
    public function getPowerFailures()
    {
        return $this->powerFailures;
    }

    /**
     * @param int $powerFailures
     */
    public function setPowerFailures($powerFailures)
    {
        $this->powerFailures = $powerFailures;
    }

    /**
     * @return int
     */
    public function getLongPowerFailures()
    {
        return $this->longPowerFailures;
    }

    /**
     * @param int $longPowerFailures
     */
    public function setLongPowerFailures($longPowerFailures)
    {
        $this->longPowerFailures = $longPowerFailures;
    }

    /**
     * @return string
     */
    public function getLongPowerFailuresLog()
    {
        return $this->longPowerFailuresLog;
    }

    /**
     * @param string $longPowerFailuresLog
     */
    public function setLongPowerFailuresLog($longPowerFailuresLog)
    {
        $this->longPowerFailuresLog = $longPowerFailuresLog;
    }

    /**
     * @return int
     */
    public function getVoltageSagsL1()
    {
        return $this->voltageSagsL1;
    }

    /**
     * @param int $voltageSagsL1
     */
    public function setVoltageSagsL1($voltageSagsL1)
    {
        $this->voltageSagsL1 = $voltageSagsL1;
    }

    /**
     * @return int
     */
    public function getVoltageSagsL2()
    {
        return $this->voltageSagsL2;
    }

    /**
     * @param int $voltageSagsL2
     */
    public function setVoltageSagsL2($voltageSagsL2)
    {
        $this->voltageSagsL2 = $voltageSagsL2;
    }

    /**
     * @return int
     */
    public function getVoltageSagsL3()
    {
        return $this->voltageSagsL3;
    }

    /**
     * @param int $voltageSagsL3
     */
    public function setVoltageSagsL3($voltageSagsL3)
    {
        $this->voltageSagsL3 = $voltageSagsL3;
    }

    /**
     * @return int
     */
    public function getVoltageSwellsL1()
    {
        return $this->voltageSwellsL1;
    }

    /**
     * @param int $voltageSwellsL1
     */
    public function setVoltageSwellsL1($voltageSwellsL1)
    {
        $this->voltageSwellsL1 = $voltageSwellsL1;
    }

    /**
     * @return int
     */
    public function getVoltageSwellsL2()
    {
        return $this->voltageSwellsL2;
    }

    /**
     * @param int $voltageSwellsL2
     */
    public function setVoltageSwellsL2($voltageSwellsL2)
    {
        $this->voltageSwellsL2 = $voltageSwellsL2;
    }

    /**
     * @return int
     */
    public function getVoltageSwellsL3()
    {
        return $this->voltageSwellsL3;
    }

    /**
     * @param int $voltageSwellsL3
     */
    public function setVoltageSwellsL3($voltageSwellsL3)
    {
        $this->voltageSwellsL3 = $voltageSwellsL3;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getInstantaneousCurrentL1()
    {
        return $this->instantaneousCurrentL1;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $instantaneousCurrentL1
     */
    public function setInstantaneousCurrentL1(ReadingValue $instantaneousCurrentL1)
    {
        $this->instantaneousCurrentL1 = $instantaneousCurrentL1;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getInstantaneousCurrentL2()
    {
        return $this->instantaneousCurrentL2;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $instantaneousCurrentL2
     */
    public function setInstantaneousCurrentL2(ReadingValue $instantaneousCurrentL2)
    {
        $this->instantaneousCurrentL2 = $instantaneousCurrentL2;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getInstantaneousCurrentL3()
    {
        return $this->instantaneousCurrentL3;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $instantaneousCurrentL3
     */
    public function setInstantaneousCurrentL3(ReadingValue $instantaneousCurrentL3)
    {
        $this->instantaneousCurrentL3 = $instantaneousCurrentL3;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getInstantaneousActivePowerInL1()
    {
        return $this->instantaneousActivePowerInL1;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $instantaneousActivePowerInL1
     */
    public function setInstantaneousActivePowerInL1(
      ReadingValue $instantaneousActivePowerInL1
    ) {
        $this->instantaneousActivePowerInL1 = $instantaneousActivePowerInL1;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getInstantaneousActivePowerInL2()
    {
        return $this->instantaneousActivePowerInL2;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $instantaneousActivePowerInL2
     */
    public function setInstantaneousActivePowerInL2(
      ReadingValue $instantaneousActivePowerInL2
    ) {
        $this->instantaneousActivePowerInL2 = $instantaneousActivePowerInL2;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getInstantaneousActivePowerInL3()
    {
        return $this->instantaneousActivePowerInL3;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $instantaneousActivePowerInL3
     */
    public function setInstantaneousActivePowerInL3(
      ReadingValue $instantaneousActivePowerInL3
    ) {
        $this->instantaneousActivePowerInL3 = $instantaneousActivePowerInL3;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getInstantaneousActivePowerOutL1()
    {
        return $this->instantaneousActivePowerOutL1;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $instantaneousActivePowerOutL1
     */
    public function setInstantaneousActivePowerOutL1(
      ReadingValue $instantaneousActivePowerOutL1
    ) {
        $this->instantaneousActivePowerOutL1 = $instantaneousActivePowerOutL1;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getInstantaneousActivePowerOutL2()
    {
        return $this->instantaneousActivePowerOutL2;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $instantaneousActivePowerOutL2
     */
    public function setInstantaneousActivePowerOutL2(
      ReadingValue $instantaneousActivePowerOutL2
    ) {
        $this->instantaneousActivePowerOutL2 = $instantaneousActivePowerOutL2;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getInstantaneousActivePowerOutL3()
    {
        return $this->instantaneousActivePowerOutL3;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $instantaneousActivePowerOutL3
     */
    public function setInstantaneousActivePowerOutL3(
      ReadingValue $instantaneousActivePowerOutL3
    ) {
        $this->instantaneousActivePowerOutL3 = $instantaneousActivePowerOutL3;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getPreviousMeterreadingOut1()
    {
        return $this->previousMeterreadingOut1;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $previousMeterreadingOut1
     */
    public function setPreviousMeterreadingOut1(ReadingValue $previousMeterreadingOut1)
    {
        $this->previousMeterreadingOut1 = $previousMeterreadingOut1;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getPreviousMeterreadingOut2()
    {
        return $this->previousMeterreadingOut2;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $previousMeterreadingOut2
     */
    public function setPreviousMeterreadingOut2(ReadingValue $previousMeterreadingOut2)
    {
        $this->previousMeterreadingOut2 = $previousMeterreadingOut2;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getPreviousMeterreadingIn1()
    {
        return $this->previousMeterreadingIn1;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $previousMeterreadingIn1
     */
    public function setPreviousMeterreadingIn1(ReadingValue $previousMeterreadingIn1)
    {
        $this->previousMeterreadingIn1 = $previousMeterreadingIn1;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getPreviousMeterreadingIn2()
    {
        return $this->previousMeterreadingIn2;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $previousMeterreadingIn2
     */
    public function setPreviousMeterreadingIn2(ReadingValue $previousMeterreadingIn2)
    {
        $this->previousMeterreadingIn2 = $previousMeterreadingIn2;
    }

    /**
     * @return string
     */
    public function getEquipmentId()
    {
        return $this->equipmentId;
    }

    /**
     * @param string $equipmentId
     */
    public function setEquipmentId($equipmentId)
    {
        $this->equipmentId = $equipmentId;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getMeterreadingIn1()
    {
        return $this->meterreadingIn1;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $meterreadingIn1
     */
    public function setMeterreadingIn1(ReadingValue $meterreadingIn1)
    {
        $this->meterreadingIn1 = $meterreadingIn1;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getMeterreadingIn2()
    {
        return $this->meterreadingIn2;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $meterreadingIn2
     */
    public function setMeterreadingIn2(ReadingValue $meterreadingIn2)
    {
        $this->meterreadingIn2 = $meterreadingIn2;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getMeterreadingOut1()
    {
        return $this->meterreadingOut1;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $meterreadingOut1
     */
    public function setMeterreadingOut1(ReadingValue $meterreadingOut1)
    {
        $this->meterreadingOut1 = $meterreadingOut1;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getMeterreadingOut2()
    {
        return $this->meterreadingOut2;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $meterreadingOut2
     */
    public function setMeterreadingOut2(ReadingValue $meterreadingOut2)
    {
        $this->meterreadingOut2 = $meterreadingOut2;
    }

    /**
     * @return int
     */
    public function getCurrentTariff()
    {
        return $this->currentTariff;
    }

    /**
     * @param int $currentTariff
     */
    public function setCurrentTariff($currentTariff)
    {
        $this->currentTariff = $currentTariff;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getCurrentPowerIn()
    {
        return $this->currentPowerIn;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $currentPowerIn
     */
    public function setCurrentPowerIn(ReadingValue $currentPowerIn)
    {
        $this->currentPowerIn = $currentPowerIn;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getCurrentPowerOut()
    {
        return $this->currentPowerOut;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $currentPowerOut
     */
    public function setCurrentPowerOut(ReadingValue $currentPowerOut)
    {
        $this->currentPowerOut = $currentPowerOut;
    }

    /**
     * @return \Sm\LogBundle\Dto\ReadingValue
     */
    public function getCurrentTreshold()
    {
        return $this->currentTreshold;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $currentTreshold
     */
    public function setCurrentTreshold(ReadingValue $currentTreshold)
    {
        $this->currentTreshold = $currentTreshold;
    }

    /**
     * @return int
     */
    public function getCurrentSwitchPosition()
    {
        return $this->currentSwitchPosition;
    }

    /**
     * @param int $currentSwitchPosition
     */
    public function setCurrentSwitchPosition($currentSwitchPosition)
    {
        $this->currentSwitchPosition = $currentSwitchPosition;
    }

    /**
     * @return string
     */
    public function getMessageCode()
    {
        return $this->messageCode;
    }

    /**
     * @param string $messageCode
     */
    public function setMessageCode($messageCode)
    {
        $this->messageCode = $messageCode;
    }

    /**
     * @return string
     */
    public function getMessageText()
    {
        return $this->messageText;
    }

    /**
     * @param string $messageText
     */
    public function setMessageText($messageText)
    {
        $this->messageText = $messageText;
    }

    /**
     * @return string
     */
    public function getCrc()
    {
        return $this->crc;
    }

    /**
     * @param string $crc
     */
    public function setCrc($crc)
    {
        $this->crc = $crc;
    }
}
