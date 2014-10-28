<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FirstClass
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
     * Parse class name 
     * 
     * @var string 
     */
    private $class;


    
    public function __construct() {
        $this->class=get_class($this);
        $this->data_to_print=  $this->toArray();
    }
    
    
    
    /**
     * Return the method if class is other data type  
     * otherwise returns Exception
     *  
     * @param string $method called method
     * @param string $args called method arguments
     * @return type mixed
     * @throws Exception
     */
    public function __call($method, $args){
        if(method_exists($this, $method)){         
            $method_name=explode('_',$method);
            if($this->class!=end($method_name)){
                return call_user_func_array(array($this, $method), $args);
            }else{
                throw new Exception("You can't call this method for this class, you already have this data");
            }
        }
    }    
    
    /**
     * 
     */
    protected function draw_HTMLList(){
        
    }

    protected function drawDom_HTMLLList(){
        
    }


    protected function drawDom_HTMLTable(){
        
    }


    /**
     * Draw html table or put into file
     * 
     * 
     * @param string $file path to file
     * @return mixed
     */
    protected function draw_HTMLTable($file=null){
        $data= $this->data_to_print;
        if(array_key_exists('table', $data)){
            $body=$data['table'];
        }else {
            $body=$data;
        }
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
    
    
    protected function drawDom_XMLTable(){
        
    }

    
    /**
     * Draw xml result as string or put into file
     * 
     * @param string $file path to file
     * @return mixed
     */
    protected function draw_XMLTable($file=null){
        $data= $this->data_to_print;
        if(array_key_exists('table', $data)){
            $body=$data['table'];
        }else{
            $body=$data;
        }
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
     * Draw json result as string or put into file
     * 
     * @param type $file path to file
     * @return type
     */
    protected function draw_JSONTable($file=null){
        if($file!=null){
            $this->filePutContent($file,json_encode($this->data_to_print,JSON_FORCE_OBJECT));
        }else {
            return json_encode($this->data_to_print,JSON_FORCE_OBJECT);
        }
    }

    
    
    /**
     * Save csv in $file
     * @param type $file path to file
     */
    protected function draw_CSVTable($file){
        $data= $this->data_to_print;
        if(array_key_exists('table', $data)){
            $body=$data['table'];
        }else{
            $body=$data;
        }

        foreach ($body as $lines){
            foreach ($lines as $line){
                $new_line[]=implode(';',$line).PHP_EOL;
                $this->filePutContent($file, $new_line);
            }
        }
    }
    
    /**
     * 
     * @param type $file
     * @return type
     */
    protected function draw_ASCITable($file=null){
        $data= $this->data_to_print;
        if(array_key_exists('table', $data)){
            $body=$data['table'];
        }else{
            $body=$data;
        }
        
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
                throw new Exception();
            }
        }else{
            throw new Exception();
        }
            
        
    }
    

    
}
