<?php
/*
 * Class Errors
 * Check values and generate an error message
 */
class Errors
{
    private $_errorMessage;

    public function __construct() {
        $this->_errorMessage="";
    }
    
    // Getters
    public function getError()
    {
        return "<div class='error'>".$this->_errorMessage."</div>\n";
    }
    
    // Setters
    
    public function setError($error)
    {
        $this->_errorMessage = $error;
    }
    
    public function addError($error)
    {
        $this->_errorMessage .= "$error<br />";
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
                $array=array('Goalkeeper','Defender','Midfielder','Forward');
                if(in_array($val, $array)) return $val;
                else {
                    $this->addError($title_errorPosition);
                    return null;
                }
                break;
            case "Result":
                $array=array('','1','D','2');
                if(in_array($val, $array)) return $val;
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
