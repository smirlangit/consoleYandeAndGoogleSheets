<?php
require './imports.php';

$kpiSheet = new KPISheet();
$data = $kpiSheet->parseData();
$kpiSheet->saveToFile($data, "DATA");


