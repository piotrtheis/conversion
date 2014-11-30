<?php
/**
 * ITable
 * 
 * @author piotr
 */
interface ITable {
    
    
    /**
     * parse data to array
     * 
     * 
     * array[table][]
     *          [th]=> array of header cells
     *          [td]=> 1:n array of data cells
     * 
     * @return array with parse data (See above)
     */
    public function toArray();
    
    
    /**
     * This method detects file or string.
     * In case HTML add id attribute and return DOM Object
     * 
     * @param string $doc parsed file or string
     */
    public function getTableData($doc);
}
