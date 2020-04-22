<?php
namespace Tds\yandex;

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
class Convert {    

    //конверитирует массив в свойства
    public function __construct($array) {
        
        if(is_array($array) == false){
            return;
        }
        foreach ($array as $key => $value){
            
            //если вложенный массив, то имя свойства складывается из родителя и потомка
            if(is_array($value)){  
                foreach($value as $child => $val){
                    $name = $key.$child;
                    $this->{$name} = $val;
                }
                
            }
            $this->{$key} = $value;
            
        }
       
    }
}
