<?php
namespace Sm\LogBundle\Entity;

class Channel
{
    /**
     * Channel Id
     * @var int
     */
    public $id = 0;

    /**
     * Channel Type
     * @var int
     */
    public $typeId = 0;

    /**
     * Channel Type description
     * @var string
     */
    public $typeDescription = '';

    /**
     * Equipment Id
     * @var string
     */
    public $equipmentId = '';

    /**
     * Timestamp
     * @var null
     */
    public $timestamp = null;

    /**
     * Meter reading
     * @var int
     */
    public $meterReading = 0;

    /**
     * Meter reading unit
     * @var string
     */
    public $unit = '';

    /**
     * Valve position (on/off/released)
     * @var int
     */
    public $valvePosition = 0;
}
