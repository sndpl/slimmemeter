<?php

namespace Sm\LogBundle\Dto;

use DateTime;

/**
 * Class Channel
 * @package Sm\LogBundle\Dto
 *
 * Example:
 * 0-1:24.1.0(003)
 * 0-1:96.1.0(4730303136353631323037373133373134)
 * 0-1:24.2.1(140622160000S)(00003.800*m3)
 * 0-1:24.4.0(1)
 */
class Channel
{
    /**
     * Channel Id
     * @var int
     */
    protected $id = 0;
    /**
     * Channel Type
     * @var int
     */
    protected $typeId = 0;
    /**
     * Channel Type description
     * @var string
     */
    protected $typeDescription = '';
    /**
     * Equipment Id
     * @var string
     */
    protected $equipmentId = '';
    /**
     * Timestamp
     * @var \DateTime
     */
    protected $timestamp;
    /**
     * Meter reading
     * @var ReadingValue
     */
    protected $readingValue;
    /**
     * Valve position (on/off/released)
     * @var int
     */
    protected $valvePosition = 0;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $typeId
     * @param string $typeDescription
     */
    public function setType($typeId, $typeDescription)
    {
        $this->typeId = $typeId;
        $this->typeDescription = $typeDescription;
    }

    /**
     * @param string $equipmentId
     */
    public function setEquipmentId($equipmentId)
    {
        $this->equipmentId = $equipmentId;
    }

    /**
     * @param \Sm\LogBundle\Dto\ReadingValue $readingValue
     * @param \DateTime                      $timestamp
     */
    public function setReadingValue(ReadingValue $readingValue, DateTime $timestamp)
    {
        $this->readingValue = $readingValue;
        $this->timestamp = $timestamp;
    }

    /**
     * @param int $pos
     */
    public function setValvePosition($pos)
    {
        $this->valvePosition = $pos;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @return string
     */
    public function getTypeDescription()
    {
        return $this->typeDescription;
    }

    /**
     * @return string
     */
    public function getEquipmentId()
    {
        return $this->equipmentId;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return ReadingValue
     */
    public function getReadingValue()
    {
        return $this->readingValue;
    }

    /**
     * @return int
     */
    public function getValvePosition()
    {
        return $this->valvePosition;
    }
}
