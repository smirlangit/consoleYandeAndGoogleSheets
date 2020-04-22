<?php
namespace Tds\yandex;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/**
 * Description of ObjCampaing
 *
 * @author mirlan
 */
class ObjCampaing extends Convert{
    
    public $Type = null;
    public $DailyBudgetAmount = null;
    public $DailyBudgetMode = null;
    public $StartDate = null;
    public $Name = null;
    public $StatisticsClicks = null;
    public $StatisticsImpressions = null;    
    public $Status = null;
    public $ClientInfo = null;
    public $State = null;
    public $Id = null;
    public $EndDate = null;
    public $TimeZone = null;


    
    public function __construct($array) {  
        parent::__construct($array);
    }
}
