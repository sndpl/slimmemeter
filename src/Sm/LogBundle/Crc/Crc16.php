<?php
namespace Sm\LogBundle\Crc;

class Crc16
{
    CONST CRC16_INIT = 0;
    CONST CRC16_POLY = 0xA001; // Reversed

    /**
     * @param string $string
     * @param int $crc
     *
     * @return int
     */
    public static function hash($string, $crc = self::CRC16_INIT)
    {
        $len = strlen($string);
        for ($x = 0; $x < $len; $x++)
        {
            $crc ^= ord($string[$x]);
            for ($y = 0; $y < 8; $y++)
            {
                if (($crc & 0x0001) == 0x0001)
                {
                    $crc = ($crc >> 1) ^ self::CRC16_POLY;
                }
                else
                {
                    $crc = $crc >> 1;
                }
            }
        }
        return $crc;
    }
}
