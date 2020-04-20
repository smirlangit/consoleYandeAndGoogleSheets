<?php

/**
 * Логическое взаимодействие с таблицей KPI
 */
class KPISheet {

    protected $sheetID = "1nrz_CICK2zrHFwGzuX6U0pmyyLRxoDcnhzMKeQyKwP8";
    protected $client;


    function __construct() {
       $client = new GoogleServiceSheets();
       $this->client = $client->getClient();
    }

    public function saveDataToSheet($data) {
        $range = 'kpi!Z3:Z';
        $values = [
           [123],
           [567]
        ];
        
        $service = new Google_Service_Sheets($this->client);
        $spreadsheetId = $this->sheetID;        

        
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => 'RAW'
        ];
        $result = $service->spreadsheets_values->update($spreadsheetId, $range,
        $body, $params);
        printf("%d cells updated.", $result->getUpdatedCells());
    }
    
    
    
    
    public function saveMultipleDataToSheet($kpidata) {
        $data = [];
        $spreadsheetId = $this->sheetID;  
        $service = new Google_Service_Sheets($this->client);

        
        //Выполнение KPI за прошедший период
        $data[] = new Google_Service_Sheets_ValueRange([
            'range' => "kpi!Z3:Z",        
            'values' => $kpidata['alltime_kpi']
        ]);
        
     
           
         //Стоимость выполненых KPI в валюте аккаунта за прошедший период
        $data[] = new Google_Service_Sheets_ValueRange([
            'range' => "kpi!AA3:AA",        
            'values' => $kpidata["alltime_cost"]
        ]);
    
        
        //Валюта аккаунта
        $data[] = new Google_Service_Sheets_ValueRange([
            'range' => "kpi!AB3:AB",        
            'values' => $kpidata["currency"]
        ]);
        
        
        //Выполнение KPI за вчерашний день
        $data[] = new Google_Service_Sheets_ValueRange([
            'range' => "kpi!AC3:AC",        
            'values' => $kpidata["yesterday_kpi"]
        ]);
        
        
        //Стоимость выполненных KPI за вчерашний день
        $data[] = new Google_Service_Sheets_ValueRange([
            'range' => "kpi!AD3:AD",        
            'values' => $kpidata["yesterday_cost"]
        ]);
        
        //Остаток средств на аккаунте в валюте акаунта
        $data[] = new Google_Service_Sheets_ValueRange([
            'range' => "kpi!AE3:AE",        
            'values' => $kpidata["budget"]
        ]);
        
        
        
        

        // Additional ranges to update ...
        $body = new Google_Service_Sheets_BatchUpdateValuesRequest([
            'valueInputOption' => 'RAW',
            'data' => $data
        ]);
        $result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
        return $result;
    }
    
    
    //получает значения из прописанных адресов ячеек
    public function getKpiParam(){
        $ranges = [
            'kpi!N3:N', // аккаунты платформ
            'kpi!O3:O', // айди клиента
            'kpi!P3:P', // айди кампаний клиента
            'kpi!G3:G', // модель оплаты
            'kpi!H3:H', // дата начала
        ];
        
        $ret = $this->getCells($ranges);
        return $ret;
    }
    

    //просто получает данные в виде массива из страницы с KPI            
    protected function getCells($ranges){
        $service = new Google_Service_Sheets($this->client);

        $spreadsheetId = $this->sheetID;
        $params = array(
            'ranges' => $ranges
        );
        $result = $service->spreadsheets_values->batchGet($spreadsheetId, $params);
        return $result->getValueRanges();
    }
    

    //анализирует полученные данные из я чеек и создает объект со свойствами
    public function parseData(){
        $res = $this->getKpiParam(); 
        
        //var_dump($res); die();
        
        $platformsArray = $res[0]['values'];
        $ClientIDArray = $res[1]['values'];
        $companiesIDArray = $res[2]['values'];
        $paymentModel = $res[3]['values'];
        $startData = $res[4]['values'];
        
        
        //очистка данных, форматирование под нужды параметры в таблицу KPI
        $dataAray = [];        
        $count = 0;
        foreach($platformsArray as $item){
            $dataAray[]=[
              "platform"=>  $platformsArray[$count][0],
              "client"=>   $ClientIDArray[$count][0],
              "campaings"=>   $companiesIDArray[$count][0],
              "paymodel"=>   $paymentModel[$count][0],
              "startdata"=>   $startData[$count][0],
            ];
            
            $count++;
        }
        
        return $dataAray;      
    }
    
    
    /**
     * Запись результата в файл в виде JSON
     * @param type $data
     * @return boolean
     */
    public function saveToFile($data, $fileName="DATA"){
        $fileName .= ".json";
        
        $data = json_encode($data,   JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($fileName, $data);
        echo "data extracted to ", $fileName, "\n";
        return true;
        
    }
    
    
    
    
    
}
