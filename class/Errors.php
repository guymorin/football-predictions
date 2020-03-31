<?php
class Errors
{
    private $_error;

    public function __construct() {
        $this->_error="";
    }
    
    // Getters
    public function getError()
    {
        return $this->_error;
    }
    
    // Setters
    
    public function setError($error)
    {
        $this->_error = $error;
    }
    
    public function addError($error)
    {
        $this->_error .= "$error<br />";
    }
    
    // Check function
    
    public function check($check,$val)
    {
        require("lang/fr.php");
        switch($check) {
            case "Action":
                if($val==1) return $val;
                else return null;
                break;
            case "Alnum":
                if(ctype_alnum(str_replace('-','',str_replace(' ','',($val))))) return $val;
                else {
                    $this->addError($title_errorAlnum);
                    return null;
                }
                break;
            case "Digit":
                if(ctype_digit($val)) return $val;
                else {
                    $this->addError($title_errorDigit);
                    return null;
                }
                break;
            case "Position":
                $array=array('Gaolkeeper','Defender','Midfielder','Forward');
                if(in_array($array, $val)) return $val;
                else {
                    $this->addError($title_errorPosition);
                    return null;
                }
                break;
            case "Result":
                $array=array('','1','D','2');
                if(in_array($array, $val)) return $val;
                else {
                    $this->addError($title_errorResult);
                    return null;
                }
                break;
            default:
                break;
        }
    }
}
?>