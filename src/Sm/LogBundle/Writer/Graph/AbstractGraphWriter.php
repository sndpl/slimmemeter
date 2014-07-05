<?php

namespace Sm\LogBundle\Writer\Graph;


use Psr\Log\LoggerInterface;

abstract class AbstractGraphWriter
{
    /**
     * @var string
     */
    protected $dbFilename = '';

    /**
     * @var string
     */
    protected $graphFilename = '';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @return mixed
     */
    abstract protected function getGraphOptions();

    /**
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $filename = $this->getDbFilename();
        if (!file_exists($filename)) {
            $this->createRrdFile();
        }
    }

    /**
     * Update graphs
     */
    public function updateGraph()
    {
        $graphs = ['hourly' => '-1h', 'daily' => '-1d', 'weekly' => '-1w', 'monthly' => '-1m', 'yearly' => '-1y'];

        foreach ($graphs as $name => $start) {
            $filename = $this->getGraphFilename($name);
            $options = $this->getGraphOptions($start, $name);
            if ($options !== false) {
                $this->logger->debug('Create Graph - ' . $filename);
                rrd_graph(
                    $filename,
                    $options
                );
                $error = rrd_error();
                if ($error !== false) {
                    throw new \Exception($error);
                }
            }
        }
    }

    /**
     * @param $name
     * @return string
     */
    protected function getGraphFilename($name)
    {
        $dir = __DIR__ . '/../../../../../web/graph/';
        return $dir . $this->graphFilename . '_' . $name . '.png';
    }

    /**
     * @return string
     */
    protected function getDbFilename()
    {
        $dir = __DIR__ . '/../../../../../data/';
        return $dir . $this->dbFilename . '.rrd';
    }
}
