<?php

require __DIR__.'/imports.php';

Class Sheets {
    
    public function extractFromTable() {
        $kpiSheet = new KPISheet();
        $data = $kpiSheet->parseData();
        
        return $data;
        
    }
    
    public function saveResult($data, $filename = "DATA") {
        $kpiSheet = new KPISheet();
        $kpiSheet->saveToFile($data);
        return true;
    }
    


}
