<?
class Mail {
    
    private $require = array('email', 'name', 'body');
    
    private $mail = null;
    
    public function __construct($data){
        include_once HOME.DS.'lib/PHPMailer/class.phpmailer.php';
        foreach($this->require as $req){
            if(isset($data[$req])){
                $this->{$req} = $data[$req];
            }else{
                return false;
            }
        }
        
        $this->setAltBody();
        $this->mail = new PHPMailer();
        $this->mail->IsSMTP();
        $this->mail->Host = Config::get()->smtpHost;
        $this->mail->Port = Config::get()->smtpPort;
        $this->mail->SMTPAuth = Config::get()->smtpAuth;
        $this->mail->SMTPSecure = Config::get()->smtpSecure;
        $this->mail->Username = Config::get()->smtpUsername;
        $this->mail->Password = Config::get()->smtpPassword;
        $this->mail->SetFrom(Config::get()->smtpUsername, Config::get()->smtpFullname);
        $this->mail->AddReplyTo(Config::get()->smtpUsername, Config::get()->smtpFullname);
        $this->mail->AddAddress($this->email, $this->name);
        $this->mail->Subject = Config::get()->mailSubject;
        $this->mail->MsgHTML($this->body);
        $this->mail->AltBody = $this->altBody;
    }
    
    public function send(){
        if(!$this->mail->Send()){
            unset($this->mail);
            return (object)array('status' => false, 'message' => Errors::get()->mailSendError);
        }else{
            unset($this->mail);
            return (object)array('status' => true,'message' =>"Mail wurde versendet an $this->email");
        }
    }
    
    private function setAltBody(){
        $this->altBody = strip_tags(str_replace(array('<br />','<br/>','<br>'),'',$this->body));
    }
    
    public static function validate($email){
        $isValid = true;
        $atIndex = strrpos($email, "@");
        if(is_bool($atIndex) && !$atIndex){
            $isValid = false;
        }else{
            $domain = substr($email, $atIndex+1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if($localLen < 1 || $localLen > 64){
                $isValid = false;
            }elseif($domainLen < 1 || $domainLen > 255){
                $isValid = false;
            }elseif($local[0] == '.' || $local[$localLen-1] == '.'){
                $isValid = false;
            }elseif(preg_match('/\\.\\./', $local)){
                $isValid = false;
            }elseif(!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)){
                $isValid = false;
            }elseif(preg_match('/\\.\\./', $domain)){
                $isValid = false;
            }elseif(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local))){
                if(!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local))){
                    $isValid = false;
                }
            }
            if($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))){
                $isValid = false;
            }
        }
        return $isValid;
    }
}

?>