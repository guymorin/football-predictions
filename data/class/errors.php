<?php
class Error
{
    private $_error;
    
    public function __construct($param) {
        include("lang/fr.php");
        $this->_error="";
    }
    
    // Getters
    public function error()
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
        $this->_error .= $error;
    }
}


?>