<?php

namespace Sm\SiteBundle\Controller;

use Sm\LogBundle\Dto\Telegram;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $telegram = $this->getLastTelegram();
        return array(
            'channel_one_installed' => is_null($telegram->channel1)?false:true,
            'channel_two_installed' => is_null($telegram->channel2)?false:true,
            'channel_three_installed' => is_null($telegram->channel3)?false:true,
            'channel_four_installed' => is_null($telegram->channel4)?false:true,
        );
    }

    /**
     * @Route("/current-power-fase")
     * @Template
     */
    public function currentPowerFaseAction()
    {
        return array();
    }

    /**
     * @Route("/channel-one")
     * @Template
     */
    public function channelOneAction()
    {
        $telegram = $this->getLastTelegram();
        return array('installed' => is_null($telegram->channel1)?false:true);
    }
    /**
     * @Route("/channel-two")
     * @Template
     */
    public function channelTwoAction()
    {
        $telegram = $this->getLastTelegram();
        return array('installed' => is_null($telegram->channel2)?false:true);
    }
    /**
     * @Route("/channel-three")
     * @Template
     */
    public function channelThreeAction()
    {
        $telegram = $this->getLastTelegram();
        return array('installed' => is_null($telegram->channel3)?false:true);
    }
    /**
     * @Route("/channel-four")
     * @Template
     */
    public function channelFourAction()
    {
        $telegram = $this->getLastTelegram();
        return array('installed' => is_null($telegram->channel4)?false:true);
    }

    /**
     * @Route("/last-telegram")
     * @Template
     */
    public function lastTelegramAction()
    {
        $telegram = $this->getLastTelegram();
        return array('telegram' => $telegram);
    }

    /**
     * @return Telegram
     */
    protected function getLastTelegram()
    {
        $file = __DIR__ . '/../../../../data/lastTelegram';
        $data = file_get_contents($file);
        if ($data !== false) {
            $telegram = unserialize($data);
        } else {
            $telegram = new Telegram();
        }
        return $telegram;
    }
}
