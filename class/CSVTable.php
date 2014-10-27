<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CSVTable
 *
 * @author piotr
 */
include_once 'Table.php';
include_once 'ITable.php';

class CSVTable extends Table implements ITable{

    public $from_file;
    
    
    public function __construct($doc) {
        $this->table_input=  $this->getTableData($doc);
        parent::__construct();
    }

    public function getTableData($doc) {
        $ext=array('csv');
        
        if(file_exists($doc)){
            if(in_array(strtolower(pathinfo($doc,PATHINFO_EXTENSION)),$ext)){
                if(is_writable($doc)){
                    return file_get_contents($doc);
                }
            }else{
                throw new Exception('nie to rozszezenie');
            }
        }  else {
            $this->from_file=false;
            return $doc;
        }
        
    }

    public function toArray() {
        $i = 0;
        $result=array();
            $lines=explode(PHP_EOL, $this->table_input);
            foreach ($lines as $line){
                $i++;
                $row=array();
                if($i==1){
                    $row['th']=explode(';', $line);
                }else{
                    $row['td']=explode(';', $line);
                }
                $result[]=$row;
                
            }
            return $result;
        
    }
}
