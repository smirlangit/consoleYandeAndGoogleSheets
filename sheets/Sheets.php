<?php
namespace Tds\sheets;
use Tds\sheets\KPISheet;
use Tds\sheets\UploadKPI;



Class Sheets{
    
    public function downloadData() {
        $kpiSheet = new KPISheet();
        $data = $kpiSheet->parseData();
        $kpiSheet->saveToFile($data, "DATA");
    }
    
    public function uploadData() {
        $up = new UploadKPI();
        $up->start();
    }
    
    
}