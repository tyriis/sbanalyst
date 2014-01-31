<?
class hIT extends DBObject{
    
    protected $table = 'hits';
    
    public function loadAll(){
        $result = parent::load();
        if(!is_array($result)) return array($result);
        return $result;
    }
    
    public function getById($id){
        return parent::load(array('id' => (int)$id));
    }
    
    public function getByChecksum($checksum){
        return parent::load(array('checksum' => $checksum));
    }
    
    public function create($data){
        $values = array(
            'checksum' => true
        );
        if($this->checkData($values, $data)){
            $data = array(
                'checksum' => $this->checksum
            );
            return parent::save($data);
        }
        return false;
    }
}
?>
