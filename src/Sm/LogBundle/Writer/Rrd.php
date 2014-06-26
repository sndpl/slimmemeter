<?php
namespace Sm\LogBundle\Writer;

use Sm\LogBundle\Dto\Telegram;
use Symfony\Component\Console\Output\OutputInterface;

class Rrd
{
    protected $rrdFile = '/tmp/test.rrd';

    public function write(Telegram $data)
    {
        if (!file_exists($this->rrdFile)) {
            $this->createRrdFile();
        }

        rrd_update($this->rrdFile, [
            '-t powerin',
            $data->timestamp->format('U') . ':' .
            round($data->current_power_in * 100)
        ]);
        var_dump(
            $data->timestamp->format('U') . ':' .
            round($data->current_power_in * 100)
        );
        rrd_graph(dirname($this->rrdFile) . '/test.png', [
            "--vertical-label", "kW",
            "--lower", "0",
            "--start", "-1 hour",
            "DEF:bla={$this->rrdFile}:powerin:AVERAGE",
            "CDEF:realpower=bla,1000,*",
            "LINE2:realpower#FF0000"
        ]);
        var_dump(rrd_error());
    }

    protected function createRrdFile()
    {
        rrd_create($this->rrdFile, [
            "--step", "10",
            "DS:powerin:ABSOLUTE:60:0:100",
            "RRA:AVERAGE:0.5:1:360",
        ]);
        var_dump(rrd_error());
    }
}
