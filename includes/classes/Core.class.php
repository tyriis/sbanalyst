<?
class Core{

    static $classes = array();
    
    public function __construct(){
        $this->defineGlobalConstants();
        $this->initClasses();
        $this->loadConfig();
        $this->setHttpHeader();
        $this->initSession();
    }

    private function defineGlobalConstants(){
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        defined('PS') or define('PS', PATH_SEPARATOR);
        defined('HOME') or define('HOME', realpath(dirname(__FILE__)."/../../"));
        defined('CONFIG_DIR') or define('CONFIG_DIR', HOME.DS.'config');
        defined('CONFIG_FILE') or define('CONFIG_FILE', 'Config.php');
        defined('INCLUDE_DIR') or define('INCLUDE_DIR', HOME.DS.'includes');
        defined('CLASS_DIR') or define('CLASS_DIR', INCLUDE_DIR.DS.'classes');
        defined('FUNCTION_DIR') or define('FUNCTION_DIR', INCLUDE_DIR.DS.'functions');
        defined('STYLE_DIR') or define('STYLE_DIR', HOME.DS.'style');
        defined('SCRIPT_DIR') or define('SCRIPT_DIR', HOME.DS.'js');
        defined('CACHE_DIR') or define('CACHE_DIR', HOME.DS.'cache');
        defined('E_DEPRECATED') or define('E_DEPRECATED', 8192);
    }

    private function loadConfig(){
        include_once(CONFIG_DIR.DS.CONFIG_FILE);
    }

    private function initClasses(){
        $files = scandir(CLASS_DIR);
        foreach($files as $file){
            if(substr($file, 0, 1) != "." && substr($file, -9) == "class.php"){
                $className = basename($file);
                $class = substr($className, 0, strpos($className, "."));
                self::$classes[$class] = CLASS_DIR.DS.$file;
            }
        }
    }

    private function setHttpHeader(){
        header("content-type: text/html");
        header("content-encoding: utf-8");
        header("Cache-Control: no-cache, must-revalidate");
    }

    private function initSession(){
        session_start();
    }
    
}

function __autoload($className){
    if(!isset(Core::$classes[$className])){
        throw new Exception("Class <b>$className</b> not found");
    }
    include_once(Core::$classes[$className]);
}

?>
