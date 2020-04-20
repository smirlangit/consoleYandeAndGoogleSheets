<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of ObjKPI
 *
 * @author mirlan
 */
class ObjKPI extends Convert{
    
    
    public $Cost = null;
    public $Clicks = null;
    public $Impressions = null;
    
    public function __construct($array) {  
     parent::__construct($array);
    }
}
