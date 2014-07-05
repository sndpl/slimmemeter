<?php
namespace Sm\LogBundle\Dto;

/**
 * Class Telegram
 * @package Sm\LogBundle\Dto
 */
class Telegram
{
    public $complete = false;
    public $header;
    public $meter_supplier="";
    public $timestamp="";
    public $dsmr_version="30";
    public $powerfailures=0;
    public $long_powerfailures=0;
    public $long_powerfailures_log="";
    public $voltage_sags_l1=0;
    public $voltage_sags_l2=0;
    public $voltage_sags_l3=0;
    public $voltage_swells_l1=0;
    public $voltage_swells_l2=0;
    public $voltage_swells_l3=0;
    public $instantaneous_current_l1=0;
    public $unit_instantaneous_current_l1="";
    public $instantaneous_current_l2=0;
    public $unit_instantaneous_current_l2="";
    public $instantaneous_current_l3=0;
    public $unit_instantaneous_current_l3="";
    public $instantaneous_active_power_in_l1=0;
    public $unit_instantaneous_active_power_in_l1="";
    public $instantaneous_active_power_in_l2=0;
    public $unit_instantaneous_active_power_in_l2="";
    public $instantaneous_active_power_in_l3=0;
    public $unit_instantaneous_active_power_in_l3="";
    public $instantaneous_active_power_out_l1=0;
    public $unit_instantaneous_active_power_out_l1="";
    public $instantaneous_active_power_out_l2=0;
    public $unit_instantaneous_active_power_out_l2="";
    public $instantaneous_active_power_out_l3=0;
    public $unit_instantaneous_active_power_out_l3="";
    public $prev_meterreading_out_1 = 0;
    public $prev_meterreading_out_2 = 0;
    public $prev_meterreading_in_1 = 0;
    public $prev_meterreading_in_2 = 0;

    public $equipment_id = '';
    public $meterreading_in_1 = '';
    public $meterreading_in_2 = '';
    public $meterreading_out_1 = '';
    public $meterreading_out_2 = '';
    public $unit_meterreading_in_1 = '';
    public $unit_meterreading_in_2 = '';
    public $unit_meterreading_out_1 = '';
    public $unit_meterreading_out_2 = '';
    public $current_tariff = '';
    public $current_power_in = '';
    public $current_power_out = '';
    public $unit_current_power_in = '';
    public $unit_current_power_out = '';
    public $current_treshold = '';
    public $unit_current_treshold = '';
    public $current_switch_position = 0;
    public $message_code;
    public $message_text;
    public $crc;

    public $channel1 = null;
    public $channel2 = null;
    public $channel3 = null;
    public $channel4 = null;

}
