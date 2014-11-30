<?php
/**
 * JSONTable 
 *
 * @author piotr
 */
include_once 'Table.php';
include_once 'ITable.php';

class JSONTable extends Table implements ITable{
    

    public function __construct($doc) {
        $this->table_input=  $this->getTableData($doc);
        parent::__construct();
        
        
    }
    
    public function getTableData($doc){
        $ext=array('json');
        
        if(file_exists($doc)){
            if(in_array(strtolower(pathinfo($doc,PATHINFO_EXTENSION)),$ext)){
                return file_get_contents($doc);
            }else{
                throw new Exception("File type ".pathinfo($doc,PATHINFO_EXTENSION)." does not match expected ".implode(',', $ext)."");
            }
        }elseif(is_object(json_decode($doc))){
            return $doc;
        }else{
            throw new Exception('cos nie tak');
        }
        
    }

        public function toArray() {
            return json_decode($this->table_input,TRUE);
        }

}
