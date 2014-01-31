<?
class DB{

    static $connection = NULL;

    private $result = NULL;

    public function __construct(){
        $this->connect();
    }

    public static function connect(){
        if(self::$connection == NULL){
            if(!self::$connection){
                self::$connection = mysqli_connect(Config::get()->dbHost, Config::get()->dbUser, Config::get()->dbPass, Config::get()->dbName);
            }
            if(!self::$connection){
                throw new Exception("could not connect to mysql db");
            }
            mysqli_set_charset(self::$connection, "utf8");
        }
        return NULL;
    }

    public static function get(){
        return new self();
    }

    public static function escape($string){
        self::connect();
        $connection = self::$connection;
        return mysqli_real_escape_string($connection, $string);
    }

    public function query($query){
        $connection = self::$connection;
        $this->result = mysqli_query($connection, $query);
        if(!$this->result){
            throw new Exception("error during query: $query\nmysql errorcode: ".mysqli_error($connection));
        }
        return $this->result;
    }
    
    public function fetchAsObjects($query){
        $this->query($query);
        $objects = array();
        while($object = mysqli_fetch_object($this->result)){
            $objects[] = $object;
        }
        return $objects;
    }

    public function fetchAsObject($query){
        $this->query($query);
        return mysqli_fetch_object($this->result);
    }
}
