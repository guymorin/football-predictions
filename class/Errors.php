<?php
/**
 * 
 * Class Errors
 * Check values and generate an error message
 */
namespace FootballPredictions;

class Errors
{
    /**
     * 
     * @var string
     */
    private $errorMessage;

    /**
     * 
     */
    public function __construct(){
        $this->errorMessage="";
    }
    
    // Getters
    /**
     * 
     * @return string
     */
    public function getError()
    {
        $val = '';
        if($this->errorMessage!="") $val = "<div class='error'>".$this->errorMessage."</div>\n";
        return $val;
    }
    
    // Setters
    /**
     * 
     * @param string $error Error message to set
     */
    public function setError($error)
    {
        $this->errorMessage = $error;
    }
    
    /**
     * 
     * @param string $title Name of the field
     * @param string $error Error message to add
     */
    public function addError($name, $error = null)
    {
        if($error == null) $this->errorMessage .= "$name";
        else $this->errorMessage .= "$name : $error";
        $this->errorMessage .= "<br />";
    }
    
    // Check function
    /**
     * 
     * @param string $check Type of check (Action, Alnum, Digit, Position, Result)
     * @param int|string $val Value to check
     * @return int|string|NULL Value
     */
    public function check($check, $val, $title=null)
    {
        require '../lang/fr.php';
        switch($check){
            case "Action":
                if($val==1) return $val;
                else return null;
                break;
            case "ActionDelete":
                if($val==1 || $val==2) return $val;
                else return null;
                break;
            case "Alnum":
                if(ctype_alnum(str_replace('-','',str_replace(' ','',($val))))) return $val;
                else {
                    $this->addError($title, $title_errorAlnum);
                    return null;
                }
                break;
            case "Date":
                $test = explode('-', $val);
                if(checkdate($test[1],$test[2],$test[0])) return $val;  
                else {
                    $this->addError($title, $title_errorDate);
                    return null;
                }
                break;
            case "Digit":
                if(ctype_digit($val)) return $val;
                else {
                    $this->addError($title, $title_errorDigit);
                    return null;
                }
                break;
            case "Position":
                $array=array('Goalkeeper','Defender','Midfielder','Forward');
                if(in_array($val, $array)) return $val;
                else {
                    $this->addError($title, $title_errorPosition);
                    return null;
                }
                break;
            case "Result":
                $array=array('','1','D','2');
                if(in_array($val, $array)) return $val;
                else {
                    $this->addError($title, $title_errorResult);
                    return null;
                }
                break;
            default:
                break;
        }
    }
}
?>
