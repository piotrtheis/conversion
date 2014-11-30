<?php
/**
 * HTMLList
 *
 * @author piotr
 */

include_once 'Table.php';
include_once 'ITable.php';


class HTMLList extends Table implements ITable{
      
    
    public function __construct($doc,$list_id=null) {
        $this->dom=new DOMDocument();
        $this->dom->preserveWhiteSpace = false;
        $this->table_input=$this->getTableData($doc,$list_id);
        parent::__construct();
       
    }

    
    public function getTableData($doc,$list_id=null){
        
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
        
        
        if($this->dom->getElementById($list_id)!=null){
            $list=$this->dom->getElementById($list_id);
        }elseif ($this->dom->getElementsByTagName('ul')->item(0)!=null) {
            $list=$this->dom->getElementsByTagName('ul')->item(0);
        }else {
            throw new Exception('nie ma tablei');
        }
        return $list;
    }
    
    
    public function toArray() {
        $length_row=$this->table_input->childNodes->item(0)->childNodes->item(2)->childNodes->length;
        $length_col=$this->table_input->childNodes->item(0)->childNodes->length;
        $result=array();
        $head=array();
        for($j=0;$j<$length_row;$j+=2){
            $row=array();
            for($i=0;$i<$length_col*2;$i++){
                if($this->table_input->childNodes->item($i) instanceof DOMElement){
                    if(!in_array($this->table_input->childNodes->item($i)->childNodes->item(0)->nodeValue, $head))
                        $head[]=$this->table_input->childNodes->item($i)->childNodes->item(0)->nodeValue;
                    $row[]=$this->table_input->childNodes->item($i)->childNodes->item(2)->childNodes->item($j)->nodeValue;
                }
            }
            $result[0]['th']=$head;
            $result[]['td']=$row;
        }  
        return array('table'=>$result);
    }

//    public function hasAttribute(DOMElement $element){
//        $attributes=array();
//        foreach ($element->attributes as $attr){
//            if($attr->value){
//                $attributes[$attr->name]=$attr->value;
//            }
//        }
//        if(!empty($attributes))
//            return $attributes;
//        return false;
//    }    
    
    public function __destruct() {
        $this->dom->saveHTML();
    }
}
