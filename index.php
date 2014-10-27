<?php
ini_set('memory_limit', '128M');
include './class/XMLTable.php';
include './class/HTMLTable.php';
include './class/JSONTable.php';
include './class/ASCIITable.php';
include './class/CSVTable.php';




$tyson=new HTMLTable('tabela.html');
$tyson->drawCSV('tabela.csv');
?>