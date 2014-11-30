<?php 
/**
 * ASCIITable
 *
 * @author piotr
 */

include_once 'Table.php';
include_once 'ITable.php';

class ASCIITable extends Table implements ITable{
    
    public $from_file;


    public function __construct($doc) {
        $this->table_input=  $this->getTableData($doc);
        parent::__construct();
    }
    
    
    public function getTableData($doc) {
        $ext=array('txt');
        
        if(file_exists($doc)){
            if(in_array(strtolower(pathinfo($doc,PATHINFO_EXTENSION)),$ext)){
                $this->from_file=true;
                return $doc;
            }else{
                throw new Exception("File type ".pathinfo($doc,PATHINFO_EXTENSION)." does not match expected ".implode(',', $ext)."");
            }
        }  else {
            $this->from_file=false;
            return $doc;
        }
        
    }

    public function toArray() {
        $result = array();
        
        $index=0;
        if($this->from_file){
            foreach (file( $this->table_input) as $line){

                if(preg_match('/^\|\.*/', $line,$xx)){
                    $line=  trim($line);
                    $line=  substr($line, 1,-1);
                    if($index==1){
                        $result[]=array('th'=>explode('|', $line));
                    }else{
                        $result[]=array('td'=>explode('|', $line));
                    }
                }
                $index++;
            }
            return array('table'=>$result);
        }else {
            $lines=explode(PHP_EOL, $this->table_input);
            foreach ($lines as $line){

                if(preg_match('/^\|\.*/', $line,$xx)){
                    $line=  trim($line);
                    $line=  substr($line, 1,-1);
                    if($index==1){
                        $result[]=array('th'=>explode('|', $line));
                    }else{
                        $result[]=array('td'=>explode('|', $line));
                    }
                }
                $index++;
            }
            return array('table'=>$result);
        }
    }


}
                    
                    
                    
                    
                    
                    
                    
