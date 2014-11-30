<?php

/**
 * Table
 *
 * @author piotr
 */
include_once 'ITable.php';

abstract class Table implements ITable{
    //put your code here
    
    /**
     * Input data to parse,from file or string
     * 
     * @var mixed  
     */
    protected $table_input;
    
    
    
    /**
     * Parse result 
     * 
     * @var array 
     */
    protected $data_to_print;
    
    /**
     * Each element's attributes
     * 
     * @var array 
     */
    public $attributes;
    /**
     * DOMDocument
     * 
     * @var Object 
     */
    protected $dom;


    /**
     * Parse class name 
     * 
     * @var string 
     */
    private $class;
    
    /**
     * Current called method
     * 
     * @var string 
     */
    private $metod;






    public function __construct() {
        $this->dom=new DOMDocument();
        $this->class=get_class($this);
        
        $this->data_to_print=array_key_exists('table',$this->toArray())?$this->toArray()['table']:$this->toArray();
        $this->attributes=array_key_exists('attributes',$this->toArray())?$this->toArray()['attributes']:null;
        
    }
    
    
    
    /**
     * Return the method if class is other data type  
     * otherwise returns Exception
     *  
     * @param string $method called method
     * @param string $args called method arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $args){
        if(method_exists($this, $method)){
            $this->metod=$method;
            $method_name=explode('_',$method);
            if($this->class!=end($method_name)){
                $output=call_user_func_array(array($this, $method), $args);                
//                if(is_object($output)){
//                    $this->setAttributes($output);
//                }
                return $output;
            }else{
                throw new Exception("You can't call this method for this class, you already have this data");
            }
        }
    }    
    
    /**
     * Draw html list or put into $file
     * 
     * 
     * @param string $file path to file
     * @return mixed
     */
    protected function draw_HTMLList($file=null){
        $body= $this->data_to_print;

        ob_start();
        $out=array();
        $index=1;
        for($i=0;$i<$index;$i++){
            $new=array();
            foreach($body as $rows=>$row){
                foreach ($row as $ix=>$cell){
                    $index=  count($cell);
                    $new[]=$cell[$i];       
                }
            }
            $out[]=$new;
        }
        
        ob_start();
        echo '<ul>';
        foreach ($out as $list){
            echo '<li>';
            foreach ($list as $key=>$val){
                if($key==0){
                    echo $val.'<ul>';
                }else{
                    echo '<li>'.$val.'</li>';
                }                
            }
            echo '</ul></li>';
        }
        echo '</ul>';
        $output=  ob_get_clean();
        ob_end_clean();
        
        if($file!=null){
            $this->filePutContent($file, $output);
        }else {
            return $output;
        }      
    }

    
    /**
     * Return list as dom element
     * 
     * @return object DOMElement
     */    
    protected function drawDom_HTMLLList(){
        $body= $this->data_to_print;

        ob_start();
        $out=array();
        $index=1;
        for($i=0;$i<$index;$i++){
            $new=array();
            foreach($body as $rows=>$row){
                foreach ($row as $ix=>$cell){
                    $index=  count($cell);
                    $new[]=$cell[$i];       
                }
            }
            $out[]=$new;
        }
        
        $ul_list=  $this->dom->createElement('ul');
        foreach ($out as $list){
            $item=  $this->dom->createElement('li');
            foreach ($list as $key=>$val){
                if($key==0){
                    $paragraf=  $this->dom->createElement('p');
                    $paragraf->appendChild($this->dom->appendChild($this->dom->createTextNode($val)));
                    $item->appendChild($paragraf);
                    $sub_list=$this->dom->createElement('ul');
                }else{
                    $item->appendChild($sub_list);
                    $sub_list_item=$this->dom->createElement('li');
                    $sub_list_item->appendChild($this->dom->createTextNode($val));
                    $sub_list->appendChild($sub_list_item);
                }                
                
            }
            
            $ul_list->appendChild($item);
        }
        return $ul_list;    
    }

    /**
     * Return table as dom element
     * 
     * @return object DOMElement
     */
    protected function drawDom_HTMLTable(){
        $body= $this->data_to_print;
        
        $table=  $this->dom->createElement('table');
        $table->setAttribute('border','1');
        foreach($body as $rows){
            $row=  $this->dom->createElement('tr');
            for($i=0;$i<count(current($rows));$i++){
                if(array_key_exists('th',$rows)){
                    $tab_head=  $this->dom->createElement('th');
                    $tab_head->appendChild($this->dom->createTextNode($rows['th'][$i]));
                    $row->appendChild($tab_head);
                }elseif (array_key_exists('td',$rows)) {
                    $tab_data=  $this->dom->createElement('td');
                    $tab_data->appendChild($this->dom->createTextNode($rows['td'][$i]));
                    $row->appendChild($tab_data);
                }
            }
            $table->appendChild($row);
        }
        return $table;
    }


    /**
     * Draw html table or put into $file
     * 
     * 
     * @param string $file path to file
     * @return mixed
     */
    protected function draw_HTMLTable($file=null){
        $body= $this->data_to_print;
        
        ob_start();
        echo '<table border="1">';
        foreach($body as $rows){
            echo '<tr>';
            for($i=0;$i<count(current($rows));$i++){
                if(array_key_exists('th',$rows)){
                    echo '<th>';
                    echo $rows['th'][$i];
                    echo '</th>';
                }elseif (array_key_exists('td',$rows)) {
                    echo '<td>';
                    echo $rows['td'][$i];
                    echo '</td>';
                }
            }
            echo '</tr>';
        }
        echo '</table>';
        
        $output= ob_get_contents();
        ob_end_clean();
        
        if($file!=null){
            $this->filePutContent($file, $output);
        }else {
            return $output;
        }       
    }
    
    /**
     * Draw xml result as string or put into $file
     * 
     * @param string $file path to file
     * @return mixed
     */
    protected function draw_XMLTable($file=null){
        $body= $this->data_to_print;
        
        $header=array();
        $output='<?xml version="1.0" encoding="UTF-8"?><tabela>';
       
        $header=  array_shift ($body);
        $header=$header['th'];
        
        foreach($body as $rows=>$row){
            $output.='<row>'; 
            for($i=0;$i<count(current($row));$i++){
                $output.= '<'.$header[$i].'>';
                $output.= $row['td'][$i];
                $output.= '</'.$header[$i].'>';
            }
            $output.= '</row>';
        }
        $output.= '</tabela>';
        if($file!=null){
            $this->filePutContent($file, $output);
        }else {
            return htmlentities($output);
        }  
    }
    
    
    
    /**
     * Draw json result as string or put into $file
     * 
     * @param type $file path to file
     * @return type
     */
    protected function draw_JSONTable($file=null){
        if($file!=null){
            if($this->attributes){
                $this->filePutContent($file,json_encode(array('table'=>$this->data_to_print,'attributes'=>  $this->attributes),JSON_FORCE_OBJECT));
            }else{
                $this->filePutContent($file,json_encode(array('table'=>$this->data_to_print),JSON_FORCE_OBJECT));          
            }
        }else{
            return $this->attributes?json_encode(array('table'=>$this->data_to_print,'attributes'=>  $this->attributes),JSON_FORCE_OBJECT):json_encode(array('table'=>$this->data_to_print),JSON_FORCE_OBJECT);
        }
    }

    
    
    /**
     * Save csv in $file
     * @param type $file path to file
     */
    protected function draw_CSVTable($file){
        $body= $this->data_to_print;
        
        foreach ($body as $lines){
            foreach ($lines as $line){
                $new_line[]=implode(';',$line).PHP_EOL;
                $this->filePutContent($file, $new_line);
            }
        }
    }
    
    /**
     * Draw ascii table or put into $file
     * @param type $file
     * @return type
     */
    protected function draw_ASCITable($file=null){
        $body= $this->data_to_print;
        
        $out=array();
        $index=1;
        for($i=0;$i<$index;$i++){
            $new=array();
            foreach($body as $rows=>$row){
                foreach ($row as $cell){
                    $index=  count($cell);
                    $new[]=$cell[$i];

                }
            }
            $str_lengths[]=max(array_map('strlen', $new));    
            $row_separator[$i] = str_repeat('-', $str_lengths[$i]);
            $col_separator[$i] ="%-{$str_lengths[$i]}s"; 
            $out[]=$new;

        }


        $row_separator='+-'.implode('-+-',$row_separator).'-+';
        $col_separator='| '. implode(' | ', $col_separator) .' |'; 



        foreach($body as $rows=>$row){
            foreach ($row as $cell){
                $bufor[]=$row_separator;
                $bufor[]=  vsprintf($col_separator, $cell);    
            }
        }
        $bufor[]=$row_separator;
        ob_start();
        echo '<pre>';
        echo implode("\n", $bufor); 
        echo '</pre>';
        $ret_str=  ob_get_contents();
        ob_end_clean();
        
        
        
        if($file!=null){
            $this->filePutContent($file, implode("\n", $bufor));
        }else {
            return $ret_str;
        } 
    }
    
    
    /**
     * under construction
     * 
     * @param DOMElement $object 
     * @return boolean
     * @throws Exception
     */
//    public function setAttributes(DOMElement $object){
//        if($this->attributes){
//            foreach($this->attributes as $tag=>$attributes){
//                foreach ($attributes as $attribut=>$value){
//                    if(!is_array($value)){ 
//                        $object->setAttribute($attribut,$value);                          
//                    }else{
//                        foreach ($value as $attr=>$item_value){
//                            if($object->hasChildNodes()){
//                                if($object->tagName=='ul'){
//                                    for($i=0;$i<$object->childNodes->length;$i++)
//                                        if($attribut==0){
//                                            $object->childNodes->item($i)->firstChild->setAttribute($attr,$item_value);
//                                        }else{
//                                            for ($j=0;$j<$object->childNodes->item($i)->childNodes->item(1)->childNodes->length;$j++)
//                                                $object->childNodes->item($i)->childNodes->item(1)->childNodes->item($attribut-1)->setAttribute($attr,$item_value);
//                                        }   
//                                }else{
//                                    $object->childNodes->item($attribut)->setAttribute($attr,$item_value);                                    
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//        }else{
//            return false;
//        }
//        return true;
//    }

    
    /**
     * 
     * @param type $file
     * @param type $output
     * @throws Exception
     */
    private function filePutContent($file,$output){
        if(file_exists($file)){
            if(is_writable($file)){
                file_put_contents($file, $output);
            }else{
                throw new Exception('File '.$file.' is not writeable');
            }
        }else{
            throw new Exception('File '.$file.' not exist');
        } 
    }
    

    
}
