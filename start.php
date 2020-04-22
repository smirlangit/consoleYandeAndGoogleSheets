<?php
require __DIR__.'/vendor/autoload.php';

use Tds\sheets\Sheets;
use Tds\processing\Processing;



//выгрузка данных из аблицы
$sh = new Sheets();
$sh->downloadData();

//обработка рекламными сервисами
$pr = new Processing();
$pr->processData();

//выгрузка результата в таблицу
$sh->uploadData();
