<?php
namespace Tds\yandex;
use Tds\yandex\DirectAPI;
use Tds\yandex\ObjKPI;
use Tds\yandex\ObjFinance;

Class YandexResponce {
    
    
    public $budget = null;
    public $dailybudget = null;
    public $currency = null;
    
    public $yesterdayClicks = null;
    public $yesterdayViews = null;
    public $yesterdayCost= null;
    
    public $allClicks= null;
    public $allViews= null;
    public $allCost= null;


    
    /**
     * 
     * @param string $login логин клиента
     * @param array $idArray массив айди кампаний
     * @param string $idArray дата начала отчета yyyy-mm-dd
     * @return $this
     * 
     */
    public function getKPI($login, $idArray, $startData) {
        $yandex = new DirectAPI();

        //KPI за все время
        $alltime = $yandex->campaingsKPI_alltime($login, $idArray, $startData);

        
        $alltime = new ObjKPI($alltime); 
        
        
        
        $this->allClicks = $alltime->Clicks;
        $this->allCost = $alltime->Cost;
        $this->allViews = $alltime->Impressions;

        //KPI за вчера
        $yesterday = $yandex->campaingsKPI_yesterday($login, $idArray);
        $yesterday = new ObjKPI($yesterday);   
        
        $this->yesterdayClicks = $yesterday->Clicks;
        $this->yesterdayCost = $yesterday->Cost;
        $this->yesterdayViews = $yesterday->Impressions;

        //финансы
        $finance = $yandex->clientFinance($login);
         
         
        $finance = new ObjFinance($finance);
        
        $this->budget = $finance->AmountAvailableForTransfer;
        $this->dailybudget = $finance->DailyBudgetAmount;
        $this->currency = $finance->Currency;
        
       
        
        
        return $this;
    }
    
}
