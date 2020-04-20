<?php
require 'DataFileProcessor.php';

$pr = new DataFileProcessor();
$data = $pr->readDataFile();

$responce = $pr->callServices($data);

$pr->saveResponce($responce, "KPI");


