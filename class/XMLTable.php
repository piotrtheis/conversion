<?php


/**
 * XMLTable
 *
 * @author piotr
 */
include_once 'Table.php';
include_once 'ITable.php';

class XMLTable extends Table implements ITable{

    public function __construct($doc) {

        $this->table_input=  $this->getTableData($doc);
        parent::__construct();
    }


    public function getTableData($doc){
        $ext=array('xml','xhtml');
        
        if(file_exists($doc)){
            if(in_array(strtolower(pathinfo($doc,PATHINFO_EXTENSION)),$ext)){
                return simplexml_load_file($doc);
            }else{
                throw new Exception("File type ".pathinfo($doc,PATHINFO_EXTENSION)." does not match expected ".implode(',', $ext)."");
            }
        }elseif(!(strcmp( $doc, strip_tags($doc) ) == 0)){
            return simplexml_load_string($doc);
        }else{
            throw new Exception('cos nie tak');
        }
    }

    

    public function toArray(){
        $result=array();
        
        foreach ($this->table_input->row as $element) {
            $rowdata=array();
            $heads=array();
            foreach($element as $key) {
                $heads['th'][]=$key->getName();
                $rowdata['td'][]=$key.'';
            }
            $result[]=$rowdata;
        }
        array_unshift($result,$heads);
        
        return array('table'=>$result);
    }
}
