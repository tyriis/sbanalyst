<?
class Config{

    private static $instance = NULL;

    private function __construct(){

    }

    private function __clone(){

    }

    public static function get(){
        if(!self::$instance){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public $dbHost = '127.0.0.1';

    public $dbUser = 'root';

    public $dbPass = '******';

    public $dbName = 'mydb';

    public $smtpHost = 'smtp.gmail.com';

    public $smtpPort = 465;

    public $smtpAuth = true;

    public $smtpSecure = 'ssl';

    public $smtpUsername = 'john.doe@gmail.com';

    public $smtpFullname = 'John Doe';

    public $smtpPassword = '123456';

    public $mailSubject = 'Neue Wohnung gefunden!';

}
