<?php


class DataFileProcessor {
    protected $dataFlePath = "../sheets/DATA.json";
    
    /**
     * читает json, конвертит в массив
     * @return Array
     */
    public function readDataFile() {
        $dataFile = $this->dataFlePath;
        $json = file_get_contents($dataFile);
        $data = json_decode($json, true);
        
        echo "start processing file..", "\n";
        return $data;
        
    }
    
    
    /**
     * Запрос к сервису рекл площадки, по циклу в массиве
     * @param type $data
     * @return type
     */
    public function callServices($data) {
        $service = null;
        $kpiArray = [];
        
        foreach($data as $item){
            
            //var_dump($item); die();
            
            $platform = $item["platform"];
            $client = $item["client"];
            $campaings = $item["campaings"];
            $paymodel = $item["paymodel"];
            $startdata = $item["startdata"];
            
            //разбиение айди кампаний
            $campaings = explode(",", $campaings);
                  

            echo "client: ", $client, ", platform: ", $platform;
            
            //TODO передалать в массив значений для создания класса нужного сервиса
            if($platform == "Яндекс kz"){
                //конвертация даты под нужный формат, в зависимости от платформы
                $startdata= explode(".", $startdata);
                $startdata = $startdata[2]. "-" .$startdata[1]. "-" .$startdata[0];                              
                $service = new YandexResponce();
            }
            
            $service->getKPI($client, $campaings, $startdata);
            
            $kpiArray[] = [
                "platform"=>$platform,
                "client"=>$client,
                "campaings"=>$campaings,
                "paymodel"=>$paymodel,
                "budget"=>$service->budget,
                "dailybudget"=>$service->dailybudget,
                "currency"=>$service->currency,
                "allClicks"=>$service->allClicks,
                "allCost"=>$service->allCost,
                "allViews"=>$service->allViews,
                "yesterdayClicks"=>$service->yesterdayClicks,
                "yesterdayCost"=>$service->yesterdayCost,
                "yesterdayViews"=>$service->yesterdayViews,
            ];
            echo " - ok \n";
            
        }
       
        return $kpiArray;

    }
    
    

    
 


    public function saveResponce($data, $fileName = "KPI"){
        $fileName .= ".json";
        
        //запись в файл
        $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($fileName, $data);
        echo "responce saved to ", $fileName, "\n";
        return true;
    }
       
    
}


