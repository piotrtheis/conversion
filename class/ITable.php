<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author piotr
 */
interface ITable {
    
    
    public function toArray();
    
    
    
    public function getTableData($doc);
}
