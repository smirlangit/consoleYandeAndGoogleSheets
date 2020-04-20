<?php
require_once './imports.php';

/**
 * выгружает файл с KPI в таблицу
 */
class UploadKPI {
    
    public $filePath = "../yandex/KPI.json";


    public function readKPIFile() {
        $file = file_get_contents($this->filePath);
        
        $data = json_decode($file, true);

        return $data;
    }
    
    public function formatForTable($data) {
        
       $budget; 
       $currency; 
       $yesterday_kpi; 
       $alltime_kpi; 
       $alltime_cost; 
       $yesterday_cost; 
       
       
       $kpiType = [
           "CPC" => ["all"=>"allClicks", "yest"=>"yesterdayClicks"],
           "CPV"=> ["all"=>"allViews", "yest"=>"yesterdayViews"],
           "% от бюджета"=> ["all"=>"allCost", "yest"=>"yesterdayCost"]
       ];
       
       echo "prepare data for upload \n";
       
       foreach($data as $item){
           echo "formating: ", $item["client"], "\n";
           
           //тип KPI
           $kpiNameAlltime = $kpiType[$item["paymodel"]]["all"];
           $kpiNameYester = $kpiType[$item["paymodel"]]["yest"];
     
           $budget[]=[$item["budget"]];
           $currency[]=[$item["currency"]];
           $yesterday_kpi[]=[$item[$kpiNameYester]];
           $alltime_kpi[]=[$item[$kpiNameAlltime]];
           $alltime_cost[]=[$item["allCost"]];
           $yesterday_cost[]=[$item["yesterdayCost"]];
           
       }
       
       //print_r($budget);
       
       //$budget = [[45000], [45000], [45000]];
       //print_r($budget); die();
       
       
        $data = [
            "budget"=>$budget,            
            "currency"=>$currency,            
            "yesterday_kpi" => $yesterday_kpi,
            "alltime_kpi" => $alltime_kpi,            
            "alltime_cost" => $alltime_cost,          
            "yesterday_cost" => $yesterday_cost,            
        ];
                
        
       //var_dump($data); die();
        
        return $data;
    }



    public function start() {
        $sheet = new KPISheet();
        
        $data = $this->readKPIFile();
        $data = $this->formatForTable($data);
        echo "prepare data for upload \n";
        
//        $data = [
//            "budget" => [[45000], [67600], [120000]],
//            "currency"=>[['KZT'], ['KZT'], ['RUB']],
//            
//            "yesterday_kpi" => [[4440], [366], [200]],
//            "alltime_kpi" => [[56000], [55566], [45200]],
//            
//            "alltime_cost" => [[56000], [55566], [45200]],          
//            "yesterday_cost" => [[1212], [7757], [75757]],
//            
//        ];
        
        $result = $sheet->saveMultipleDataToSheet($data);
        
        echo "выгрузка завершена \n";
        
    }
    
    
    
}



$up = new UploadKPI();
$up->start();


