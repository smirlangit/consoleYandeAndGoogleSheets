<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/**
 * Description of ObjClient
 *
 * @author mirlan
 */
class ObjClient extends Convert{
    public $ClientInfo = null;
    public $Login = null;
    public $Currency = null;
    public $CreatedAt = null;
    public $Archived = null;
    
    public function __construct($array) {  
        parent::__construct($array);
    }
    
}
