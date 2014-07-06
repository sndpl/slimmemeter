<?php

namespace Sm\SiteBundle\Controller;

use Sm\LogBundle\Dto\Telegram;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CurrentPowerController extends Controller
{
    /**
     * @Route("/current-power")
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/current-power-costs")
     * @Template
     */
    public function costsAction()
    {
        $now = mktime(23,59,50, date('m'), date('d')-1, date('Y'));
        $start = strtotime('-30d', $now);
        $db = $file = __DIR__ . '/../../../../data/current_power.rrd';
        $options = ['-s', $start,
            '-e', $now,
            '--step', '86400',
            'DEF:a=' . $db . ':powerin_t1:AVERAGE',
            'DEF:b=' . $db . ':powerin_t2:AVERAGE',
            'DEF:c=' . $db . ':powerout_t1:AVERAGE',
            'DEF:d=' . $db . ':powerout_t2:AVERAGE',
            'XPORT:a:"Current power in T1"',
            'XPORT:b:"Current power in T1"',
            'XPORT:b:"Current power out T2"',
            'XPORT:b:"Current power out T2"'
        ];


        $data = rrd_xport($options);

        //$data = array('start' => 1402099200, 'end' => 1404691200, 'step' => 86400, 'data' => array(0 => array('legend' => '"Current power in T1"', 'data' => array(1402099200 => 'NAN', 1402185600 => 'NAN', 1402272000 => 'NAN', 1402358400 => 'NAN', 1402444800 => 'NAN', 1402531200 => 'NAN', 1402617600 => 'NAN', 1402704000 => 'NAN', 1402790400 => 'NAN', 1402876800 => 'NAN', 1402963200 => 'NAN', 1403049600 => 'NAN', 1403136000 => 'NAN', 1403222400 => 'NAN', 1403308800 => 'NAN', 1403395200 => 'NAN', 1403481600 => 'NAN', 1403568000 => 'NAN', 1403654400 => 'NAN', 1403740800 => 'NAN', 1403827200 => 'NAN', 1403913600 => 'NAN', 1404000000 => 'NAN', 1404086400 => 'NAN', 1404172800 => 'NAN', 1404259200 => 'NAN', 1404345600 => 'NAN', 1404432000 => 131.07056913116, 1404518400 => 71.557013888889, 1404604800 => 462.19447692652, 1404691200 => 'NAN',),), 1 => array('legend' => '"Current power in T1"', 'data' => array(1402099200 => 'NAN', 1402185600 => 'NAN', 1402272000 => 'NAN', 1402358400 => 'NAN', 1402444800 => 'NAN', 1402531200 => 'NAN', 1402617600 => 'NAN', 1402704000 => 'NAN', 1402790400 => 'NAN', 1402876800 => 'NAN', 1402963200 => 'NAN', 1403049600 => 'NAN', 1403136000 => 'NAN', 1403222400 => 'NAN', 1403308800 => 'NAN', 1403395200 => 'NAN', 1403481600 => 'NAN', 1403568000 => 'NAN', 1403654400 => 'NAN', 1403740800 => 'NAN', 1403827200 => 'NAN', 1403913600 => 'NAN', 1404000000 => 'NAN', 1404086400 => 'NAN', 1404172800 => 'NAN', 1404259200 => 'NAN', 1404345600 => 'NAN', 1404432000 => 147.82343636231, 1404518400 => 266.63650462963, 1404604800 => 0, 1404691200 => 'NAN',),), 2 => array('legend' => '"Current power out T2"', 'data' => array(1402099200 => 'NAN', 1402185600 => 'NAN', 1402272000 => 'NAN', 1402358400 => 'NAN', 1402444800 => 'NAN', 1402531200 => 'NAN', 1402617600 => 'NAN', 1402704000 => 'NAN', 1402790400 => 'NAN', 1402876800 => 'NAN', 1402963200 => 'NAN', 1403049600 => 'NAN', 1403136000 => 'NAN', 1403222400 => 'NAN', 1403308800 => 'NAN', 1403395200 => 'NAN', 1403481600 => 'NAN', 1403568000 => 'NAN', 1403654400 => 'NAN', 1403740800 => 'NAN', 1403827200 => 'NAN', 1403913600 => 'NAN', 1404000000 => 'NAN', 1404086400 => 'NAN', 1404172800 => 'NAN', 1404259200 => 'NAN', 1404345600 => 'NAN', 1404432000 => 147.82343636231, 1404518400 => 266.63650462963, 1404604800 => 0, 1404691200 => 'NAN',),), 3 => array('legend' => '"Current power out T2"', 'data' => array(1402099200 => 'NAN', 1402185600 => 'NAN', 1402272000 => 'NAN', 1402358400 => 'NAN', 1402444800 => 'NAN', 1402531200 => 'NAN', 1402617600 => 'NAN', 1402704000 => 'NAN', 1402790400 => 'NAN', 1402876800 => 'NAN', 1402963200 => 'NAN', 1403049600 => 'NAN', 1403136000 => 'NAN', 1403222400 => 'NAN', 1403308800 => 'NAN', 1403395200 => 'NAN', 1403481600 => 'NAN', 1403568000 => 'NAN', 1403654400 => 'NAN', 1403740800 => 'NAN', 1403827200 => 'NAN', 1403913600 => 'NAN', 1404000000 => 'NAN', 1404086400 => 'NAN', 1404172800 => 'NAN', 1404259200 => 'NAN', 1404345600 => 'NAN', 1404432000 => 147.82343636231, 1404518400 => 266.63650462963, 1404604800 => 0, 1404691200 => 'NAN',),),),);

        $startDate = $data['start'];
        $endDate = $data['end'];

        $t2Costs = 0.2266;
        $t1Costs = 0.2069;
        $cpData = [];
        for ($i = 0; $i<4 ; $i++) {
            $rowData = $data['data'][$i]['data'];
            switch ($i) {
                case 0:
                    $columnName = 'powerin_t1';
                    break;
                case 1:
                    $columnName = 'powerin_t2';
                    break;
                case 2:
                    $columnName = 'powerout_t1';
                    break;
                case 3:
                    $columnName = 'powerout_t2';
                    break;
            }
            foreach ($rowData as $timestamp => $value) {
                if ($value != 'NAN') {
                    $cpData[$timestamp][$columnName] = $value/1000;
                } else {
                    $cpData[$timestamp][$columnName] = 0;
                }
                if ($i == 0 || $i == 2) {
                    $cpData[$timestamp][$columnName . '_costs'] = $cpData[$timestamp][$columnName]*$t1Costs;
                } else {
                    $cpData[$timestamp][$columnName . '_costs'] = $cpData[$timestamp][$columnName]*$t2Costs;
                }
            }
        }

        foreach($cpData as $timestamp => $data) {
            $total = $data['powerin_t1_costs']+$data['powerin_t2_costs'] - $data['powerout_t1_costs'] - $data['powerout_t2_costs'];
            $cpData[$timestamp]['total_costs'] = $total;
        }

        return array(
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data' => $cpData
        );
    }
}
