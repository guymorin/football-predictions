<?php
/**
 * 
 * Class Forms
 * Generate forms elements
 */
class Forms
{
    /**
     * 
     * @var array Form or other data 
     */
    private $data;
    
    /**
     * 
     * @var string HTML Tag to surround each form element
     */
    public $surround = "p";

    /**
     * 
     * @param array $data Form or database data 
     */
    public function __construct($data = array()) {
        $this->data = $data;
    }
    
    /**
     * 
     * @param string $html HTML code of the form elements
     * @return string
     */
    private function surround($html){
        return "<{$this->surround}>$html</{$this->surround}>";
    }
    
    /**
     * 
     * @param array $index Data index
     * @return NULL
     */
    private function getValue($index){
        return isset($this->data[$index]) ? $this->data[$index] : null;
    }
    
    /**
     * 
     * @param string $index Data index
     * @param string $val Data value
     */
    public function setValue($index,$val){
        $this->data[$index] = $val;
    }
    
    /**
     * 
     * @param array $data Data array
     */
    public function setValues($data){
        foreach($data as $k => $v) $this->data[$k] = $v;
    }
    
    /* Forms elements */
    
    /**
     * 
     * @param string $label
     * @param string $name
     * @return string HTML code of the input element
     */
    public function input($label,$name){
         $val = "<label>$label : </label>";
         $val.= "<input type='text' name='$name' value='".$this->getValue($name)."'>";
         $val = $this->surround($val);
         return $val;
    }
    
    /**
     * 
     * @param string $name
     * @return string HTML code of the input boolean element
     */
    public function inputAction($name){
        return "<input type='hidden' name='$name' value='1'>";
    }
    
    /**
     * 
     * @param string $title Value
     * @return string HTML code of the submit element
     */
    public function submit($title) {
        return "<button type='submit'>$title</button>";
        ;
    }
}
?>
