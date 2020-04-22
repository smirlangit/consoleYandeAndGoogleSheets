<?php
namespace Tds\yandex;
/*
 * Асбтракция вызова к АПИ яндекс директа
 */

/**
 * Description of DirectAPI
 *
 * @author mirlan
 */


class DirectAPI {
    
    
    protected $token = "AgAAAAA--6voAAZAo4agGRE7eE-CvObYJrdy8n8";
    protected $endpointV5 = "https://api.direct.yandex.com/json/v5/";
    protected $endpointV4 = "https://api.direct.yandex.ru/live/v4/json/";

    public $hasEror = false;
    public $errorMessage = "";
    public $errorMessageFull = "";

  

    public function clients(){
       $resource = 'agencyclients';
        $req = '{
              "method": "get",
                "params": { 
                  "SelectionCriteria": {  
                    "Logins": [],
                    "Archived": "NO"
                  },  
                  "FieldNames": ["ClientInfo","Login", "Archived", "CreatedAt","OverdraftSumAvailable", "Currency"] 
                        
                }
            }';
        $res = $this->callApi_V5($resource, $req);
        $res = json_decode($res, true); 
        $res = $res["result"]["Clients"];
    
        return $res;
    }
    
    
    public function clientFinance($login){
        $req = '{
            "method": "AccountManagement",
            "token": "'.$this->token.'",
            "param": {
               "Action": "Get",
               "SelectionCriteria": {
                  "Logins": ["tds-unipharm"],
                  "AccountIDS": []
               }
            }
         }';
        
        //!!! можно вводить много логинов за раз
        
        
        $res = $this->callApi_V4($req);  
        $res = json_decode($res, true);
        
        
        $res = $res["data"]["Accounts"][0]; 
        
       
        
        return $res;
    }
    
    public function campaings($login){
        $resource = 'campaigns';
        $req = '{
            "method": "get",
            "params": {
              "SelectionCriteria": {},
              "FieldNames": ["Id", "Name", "StartDate", "EndDate","TimeZone", "DailyBudget", "ClientInfo", "Statistics", "State", "Status", "Type"]
            }
          }';
        
        $res = $this->callApi_V5($resource, $req, $login);
        $res = json_decode($res, true);
        $res = $res["result"]["Campaigns"]; 
        
     
        
        return $res;
    }
    
    
        public function campaingDailyBudget($login, $id){
        $resource = 'campaigns';
        $req = '{
            "method": "get",
            "params": {

              "SelectionCriteria": {
                   "Ids":[44420788]
              },
              "FieldNames": ["Id", "DailyBudget"]
            }
          }';

        $res = $this->callApi_V5($resource, $req, $login);
        $res = json_decode($res, true);
        $res = $res["result"]["Campaigns"]; 
        
        return $res;
    }
    
    /**
     * 
     * @param type $login
     * @param type $campaignID
     * @param type $starDate
     * @return type
     */
    public function companyKPI($login, $campaignID, $starDate = ''){
        //за прошедший период       
        $yesterday  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
        
        
        //если дата начала не указана, то берется дата за вчерашний
        if($starDate == ''){
            $starDate = $yesterday;
        }
        
        $endDate = $yesterday;

        //данные за прошедший период до конца вчерашнего дня
        $before_peroid = $this->companyKPI_tool($login, $campaignID, "CUSTOM_DATE", $starDate, $endDate);
        $before_peroid = $this->reportConvert($before_peroid);
        
          
        //за вчерашний день
        $yesterday_period = $this->companyKPI_tool($login, $campaignID, "YESTERDAY");
        $yesterday_period = $this->reportConvert($yesterday_period);
        
        //за предыдущее время
        $objBeforePeriod = new ObjKPI($before_peroid);
        $yesterday_period = new ObjKPI($yesterday_period);
        
        //объект kpi за предыдущий и вчераний период
        $campKPI = new ObjCampaingnKPI();
        $campKPI->beforePeriod = $objBeforePeriod;
        $campKPI->yesterdayPeriod = $yesterday_period;
        $campKPI->dateBefore = $starDate;
        $campKPI->dateYesterday = $yesterday;
        
        $ret = $campKPI;
        
        return $ret;
    }
    
    
    public function campaingsKPI_yesterday($login, $campaignID){
        $res = $this->companyKPI_tool($login, $campaignID, "YESTERDAY");
        $res = $this->reportConvert($res);
        return $res;
    }
    
    public function campaingsKPI_alltime($login, $campaignID, $starDate){
        $yesterday  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
        
        $res = $this->companyKPI_tool($login, $campaignID, "CUSTOM_DATE",  $starDate, $yesterday);
        
        $res = $this->reportConvert($res);
        
        return $res;
    }
    



    protected function companyKPI_tool($login, $campaignID, $timePeriod = "YESTERDAY", $startDate = null, $endDate = null) {

        $resource = 'reports';

        $customPeriod = "";
        if($startDate != null  && $endDate != null){
            $customPeriod = '"DateFrom":"'.$startDate.'", "DateTo":"'.$endDate.'",  ';
        } 
        
        
        //обработка можножественных значений ID кампаний
        $reportName = '';
        $values = '';
        if(is_array($campaignID)){
            $reportName = implode("_", $campaignID);
            $values = implode(", ", $campaignID);
  
        } else {
            $reportName = $campaignID;
            $values = $campaignID;
        }
        
        

        $req = '{
            "method": "get",
            "params": {

              "SelectionCriteria": {
                   '.$customPeriod.'
                   "Filter":[{
                          "Field":"CampaignId",
                          "Operator":"IN",
                          "Values":['.$values.']
                   }]

              },
              "FieldNames": ["Cost", "Clicks", "Impressions"],
              "ReportName":"'.$login."_".$reportName.'",
              "ReportType":"CUSTOM_REPORT",
              "Format": "TSV",
              "IncludeVAT":"NO",

              "DateRangeType":"'.$timePeriod.'"
            }
          }';

        //var_dump($req); exit();
        
        $res = $this->callApi_V5($resource, $req, $login);
        
       
        
        return $res;
    }


    protected function callApi_V5($urlResource, $bodyRequest, $login = "", $methodPost = false) {
        $url = $this->endpointV5.$urlResource;
        $headers = [              
            "Authorization: Bearer $this->token",   
            "Accept-Language: ru",                            
            "Content-Type: application/json; charset=utf-8",           
            "processingMode: auto",           
        ];
        
        $ch = curl_init($url);
        
        if($login != ""){
            $headers []="Client-Login: $login";
        }
        
        if($methodPost){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        }
        
        //настройка запроса
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyRequest);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
       
        $res = curl_exec($ch); 
        //var_dump($res); die();
        
        if(curl_error($ch)){
            
            $this->error("ошибка запроса в АПИ яднекса");
            
            return false;
        }
        
        //проверка наличия ошибок от апи
        $error = json_decode($res, true);
        
        
        if( isset($error["error"]) ){
             $this->error($error["error"]["error_string"], $error["error"]["error_detail"]);
            return false;
        }
        
        return $res;
    }
    
    
    protected function callApi_V4($bodyRequest) {
        $url = $this->endpointV4;
        
        $ch = curl_init($url);


        //настройка запроса        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyRequest);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        
       
        $res = curl_exec($ch); 
        
        
        if(curl_error($ch)){
            $this->error("ошибка запроса в АПИ яднекса");
            return false;
        }
        
        
        //проверка наличия ошибок от апи
        $error = json_decode($res, true);
        
        if( isset($error["error"]) ){
             $this->error($error["error"]["error_string"], $error["error"]["error_detail"]);
            return false;
        }
        
        return $res;
    }
    
    
    
    /**
     * 
     * @param string $str - ответ от апи report в виде текстовой строки
     * @return array - ассоц массив
     */
    protected function reportConvert($str) {

        if($str == false){
            $this->error("ошибка ответа от сервера");
            return false;
        }
        
         $string = explode("\n", $str);
        //удаляем последний элемент, так как он не нужен
        array_pop($string);
        array_pop($string);
        
        //если загаловки не сформированы
        if(isset($string[1]) == false){
            $this->error("ответ от сервера еще не получен");
            return false;
        }
            
        $headers = $string[1];
        
        
        
        
        //удаляем техническую информацию
        unset($string[0]);
        unset($string[1]);
        
        //делаем заголовки
        $headers = explode("\t", $headers);
        
        //dd($headers);
        

        $fields = [];
            
        
        //сопоставляем заголовки значениям   
        if(count($string) > 0){
            foreach ($string as $key){
               $key = explode("\t", $key);
               $i = 0;
               $data =[];
               foreach($headers as $head){
                  //яндекс отдает стоимость умноженную на 1 млн
                   if($head == "Cost"){$key[$i] = $key[$i] / 1000000;}
                    $data[$head] = $key[$i];
                    $i++;
               }

               $fields=$data;         

            }
        } else {            
            foreach($headers as $head){
                $data[$head] = 0;
            }
            $fields=$data;       
        }

        return $fields;
    }
    
    protected function error($msg="", $msgFull = null) {
     $this->hasEror = true;
     $this->errorMessage = $msg;        
     $this->errorMessageFull = $msgFull;   
    }
    
    
}
