<?php

namespace Sm\LogBundle\Writer;

class WriterFactory
{
    const SCREEN = 'screen';
    const RRD = 'rrd';


    public function create($type, $output)
    {
        switch ($type) {
            case self::SCREEN:
                return new Screen($output);
                break;
            case self::RRD:
                return new Rrd();
                break;
            default:
                throw new \InvalidArgumentException("$type is not a valid vehicle");
        }
    }
}
