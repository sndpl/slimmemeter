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
class Channel
{
    public $id = 0;
    public $typeId = 0;
    public $typeDescription = '';
    public $equipmentId = '';
    public $timestamp = null;
    public $meterReading = 0;
    public $unit = '';
    public $valvePosition = 0;
}
