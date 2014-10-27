<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of XMLTable
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
                throw new Exception('nie to rozszezenie');
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
        
        return $result;
    }
}
