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
            $data->timestamp->format('U') . ':' .
            $data->current_power_in
        ]);
        var_dump(rrd_error());

        rrd_graph(dirname($this->rrdFile) . '/test.png', [
            "--width", "800",
            "--height", "200",
            "--vertical-label", "kW",
            "--lower", "0.3",
            "--start", "-10 minutes",
            "DEF:realpower={$this->rrdFile}:powerin:AVERAGE",
            //            "CDEF:realpower=bla,1000,*",
            //"LINE1:realpower#FF0000:powerin\\r",
            "LINE1:realpower#0000FF:powerin\\r",
            "GPRINT:realpower:MIN:Min kW\: %4.2lf",
            "GPRINT:realpower:AVERAGE:Avg kW\: %4.2lf",
            "GPRINT:realpower:MAX:Max kW\: %4.2lf",
        ]);
        var_dump(rrd_error());
    }

    protected function createRrdFile()
    {
        rrd_create($this->rrdFile, [
            "--step", "10",
            "DS:powerin:GAUGE:60:0:1",
            "RRA:AVERAGE:0.5:1:360",
            "RRA:MIN:0.5:1:360",
            "RRA:MAX:0.5:1:360",
        ]);
        var_dump(rrd_error());
    }
}
