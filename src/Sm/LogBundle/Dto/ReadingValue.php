<?php

namespace Sm\LogBundle\Dto;

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
class ReadingValue
{
    /**
     * @var float
     */
    protected $meterReading = 0;
    /**
     * @var string
     */
    protected $unit = '';

    /**
     * @param float $meterReading
     * @param string $unit
     */
    public function __construct($meterReading, $unit)
    {
        $this->meterReading = (float)$meterReading;
        $this->unit = $unit;
    }

    /**
     * @return float
     */
    public function getMeterReading()
    {
        return $this->meterReading;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }
}
