<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TableFromHtml
 *
 * @author piotr
 */
include_once  'Table.php';
include_once 'ITable.php';

class HTMLTable  extends Table implements ITable{
    
    
    private $dom;

    public function __construct($doc,$table_id=null) {        
        $this->table_input=$this->getTableData($doc,$table_id);
        parent::__construct();
    }
    
    
    
    public function getTableData($doc,$table_id=null){
        $this->dom=new DOMDocument();
        $ext=array('html','phtml');
        
        if(file_exists($doc)){
            if(in_array(strtolower(pathinfo($doc,PATHINFO_EXTENSION)),$ext)){
                $this->dom->loadHTMLFile($doc);
            }else{
                throw new Exception('nie to rozszezenie');
            }
        }else{
            if(!(strcmp( $doc, strip_tags($doc) ) == 0))
                $this->dom->loadHTML($doc);    
        }
        
        
        if($this->dom->getElementById($table_id)!=null){
            $table=$this->dom->getElementById($table_id);
        }elseif ($this->dom->getElementsByTagName('table')->item(0)!=null) {
            $table=$this->dom->getElementsByTagName('table')->item(0);
        }else {
            die('cos nie tak');
            throw new Exception('nie ma tablei');
        }
        return $table;
        
    }

    public function toArray(){
        $result = array();

        foreach($this->table_input->childNodes as $row){
           if(strtolower($row->nodeName) != 'tr') continue;
           $rowdata = array();
           foreach($row->childNodes as $cell){
               if((strtolower($cell->nodeName) != 'td')&&(strtolower($cell->nodeName) != 'th')) continue;
               $rowdata[$cell->nodeName][] = $cell->textContent;
           }
           $result[] = $rowdata;
        }
        return $result;
    }
    
    
    public function __destruct() {
        $this->dom->saveHTML();
    }
    
}
