<?php
namespace Tds\yandex;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of ObjFinance
 *
 * @author mirlan
 */
class ObjFinance extends Convert{
    public $Currency = null;
    public $AmountAvailableForTransfer = null;
    public $Amount = null;
    public $AgencyName = null;
    public $DailyBudgetAmount = null;
    
    public function __construct($array) {  
        parent::__construct($array);
    }

}
