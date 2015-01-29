<?php
include './class/XMLTable.php';
include './class/HTMLTable.php';
include './class/JSONTable.php';
include './class/ASCIITable.php';
include './class/CSVTable.php';
include './class/HTMLList.php';

//to poszlo z clona

/**
 * example 1
 * draw html table from ul list
 */

echo  (new HTMLList('list.html'))->draw_HTMLTable();


/**
 * example 2
 * draw html list from html table id=tableId, if id is undefined get first table from tabela.html 
 */

echo (new HTMLTable('tabela.html','tableId'))->draw_HTMLList();


/**
 * example 3
 * save table in file 
 */
(new HTMLList('list.html'))->draw_HTMLTable('new_tab.html');




/**
 * example 4
 * append html table
 */
$doc = new DOMDocument();
$doc->appendChild($doc->importNode((new HTMLList('list.html'))->drawDom_HTMLTable(),true));
echo $doc->saveHTML();


/**
 * example 5
 * append html list
 */
$doc = new DOMDocument();
$doc->appendChild($doc->importNode((new HTMLTable('tabela.html'))->drawDom_HTMLLList(),true));
echo $doc->saveHTML();
//


/**
 * example 5
 * append html list
 */
//$doc = new DOMDocument();
//$doc->appendChild($doc->importNode((new HTMLList('list.html'))->drawDom_HTMLTable(),true));
//echo $doc->saveHTML();



?>
