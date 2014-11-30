<?php

include_once  'Table.php';
include_once 'ITable.php';

class HTMLTable  extends Table implements ITable{
    

    public function __construct($doc,$table_id=null) {
        $this->dom=new DOMDocument();
        $this->dom->preserveWhiteSpace = false;
        $this->table_input=$this->getTableData($doc,$table_id);
        parent::__construct();
    }
    
    
    
    public function getTableData($doc,$table_id=null){
        $ext=array('html','phtml');
        
        if(file_exists($doc)){
            if(in_array(strtolower(pathinfo($doc,PATHINFO_EXTENSION)),$ext)){
                $this->dom->loadHTMLFile($doc);
            }else{
                throw new Exception("File type ".pathinfo($doc,PATHINFO_EXTENSION)." does not match expected ".implode(',', $ext)."");
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
        $arrt = array();
        if($this->hasAttribute($this->table_input))
                $attr[$this->table_input->tagName]=$this->hasAttribute($this->table_input);    
        
        foreach($this->table_input->childNodes as $key=>$row){
           if(strtolower($row->nodeName) != 'tr') continue;
            if($this->hasAttribute($row))
                $attr[$row->tagName][$key]=$this->hasAttribute($row); 
           $rowdata = array();   
           foreach($row->childNodes as $i=>$cell){
               if((strtolower($cell->nodeName) != 'td')&&(strtolower($cell->nodeName) != 'th')) continue;
               $rowdata[$cell->nodeName][] = $cell->textContent;
           }
           $result[] = $rowdata;
        }
        return array('table'=>$result,'attributes'=>$attr);
    }
    
    
    public function hasAttribute(DOMElement $element){
        $attributes=array();
        foreach ($element->attributes as $attr){
            if($attr->value){
                $attributes[$attr->name]=$attr->value;
            }
        }
        if(!empty($attributes))
            return $attributes;
        return false;
    }

    

    public function __destruct() {
        $this->dom->saveHTML();
    }
    
}
