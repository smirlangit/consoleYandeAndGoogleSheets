<?php

namespace Tds\processing;
use Tds\processing\DataFileProcessor;

/**
 * Description of Processing
 *
 * @author mirlan
 */
class Processing {
    public function processData() {
        $pr = new DataFileProcessor();
        $data = $pr->readDataFile();
        $responce = $pr->callServices($data);
        $pr->saveResponce($responce, "KPI");

    }
}
