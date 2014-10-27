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
    
    protected $table_input;
    protected $data_to_print;
    
    
    public function __construct() {
        $this->data_to_print=  $this->toArray();
    }
    
    public function drawHTMLDom(){
        
    }


    
    public function drawHTML($file=null){
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
    
    
    public function drawXMLDom(){
        
    }

    

    public function drawXML($file=null){
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
    
    public  function drawJSON($file=null){
        if($file!=null){
            $this->filePutContent($file,json_encode($this->data_to_print,JSON_FORCE_OBJECT));
        }else {
            return json_encode($this->data_to_print,JSON_FORCE_OBJECT);
        }
    }

    
    public function drawCSV($file){
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
    
    
    public function drawASCI($file=null){
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
    
    
    public function drawPDF(){
        
    }
    
    
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
