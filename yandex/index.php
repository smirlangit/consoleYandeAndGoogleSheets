<?php

require_once 'imports.php';

$pr = new DataFileProcessor();
$data = $pr->readDataFile();

$responce = $pr->callServices($data);

$pr->saveResponce($responce, "KPI");
