<?php
/**
 * @author Nils Biesalski
 * @date 04-29-2013
 */
class DBObject {
    
    protected $table = null;
    
    public $id = null;
    
    public function __construct(){
        
    }
    
    protected function load($keys=null, $values=null){
        if($this->table){
            if($values){
                $this->values = '';
                foreach($values as $value){
                    $this->values .= DB::escape($value).',';
                }
                $this->values = trim($this->values,',');
            }else{
                $this->values = '*';
            }
            if($keys){
                $this->keys = '';
                foreach($keys as $key => $value){
                    if(is_numeric($value)){
                        $this->keys = DB::escape($key).'='.DB::escape($value).' AND ';
                    }else{
                        $this->keys = DB::escape($key).'=\''.DB::escape($value).'\' AND ';
                    }
                }
                $this->keys = preg_replace('/AND $/','',$this->keys);
                $sql = "SELECT $this->values FROM $this->table WHERE $this->keys";
            }else{
                $sql = "SELECT $this->values FROM $this->table";
            }
            $result = DB::get()->fetchAsObjects($sql);
            if(count($result) === 1){
                return $result[0];
            }elseif(count($result > 1)){
                return $result;
            }
        }
        return null;
    }
    
    protected function save($data){
        $this->cols = '';
        $this->values = '';
        foreach($data as $col => $value){
            if($value !== null){
                $this->cols .= DB::escape($col).',';
                if(is_numeric($value)){
                    $this->values .= DB::escape($value).',';
                }else{
                    $this->values .= '\''.DB::escape($value).'\',';
                }
            }
        }
        $this->cols = trim($this->cols,',');
        $this->values = trim($this->values,',');
        $sql = "INSERT INTO $this->table ($this->cols) VALUES ($this->values)";
        $result = DB::get()->query($sql);
        $this->id = DB::get()->getInsertId();
        return $result;
    }
    
    protected function update($id, $data){
        if($data && count($data) > 0){
            $sql = "UPDATE $this->table SET ";
            foreach($data as $col => $value){
                $sql .= DB::escape($col)."=";
                if(is_numeric($value)){
                    $sql .= DB::escape($value).",";
                }else{
                    $sql .= "'".DB::escape($value)."',";
                }
            }
            $sql = trim($sql, ',');
            $sql .= " WHERE id=".DB::escape($id);
            return DB::get()->query($sql);
        }
        return false;
    }
    
    protected function checkData($values,$data){
        foreach($values as $value => $option){
            if($option && (!isset($data->{$value}) || $data->{$value} === null)){
                return false;
            }
            if(!$option && !isset($data->{$value})){
                $data->{$value} = null;
            }
            $this->{$value} = $data->{$value};
        }
        return true;
    }
    
    public function getTable(){
        return $this->table;
    }
}

?>
