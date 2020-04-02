<?php
/*
 * Class Forms
 * Generate forms elements
 */
class Forms
{
    private $data;
    public $surround = "p";

    public function __construct($data = array()) {
        $this->data = $data;
    }

    public function surround($html){
        return "<{$this->surround}>$html</{$this->surround}>";
    }
    
    private function getValue($index){
        return isset($this->data[$index]) ? $this->data[$index] : null;
    }
    
    public function setValue($index,$val){
        $this->data[$index] = $val;
    }
    public function setValues($data){
        foreach($data as $k => $v) $this->data[$k] = $v;
    }
    
    /* Forms elements */
    
    public function input($label,$name){
         $val = "<label>$label : </label>";
         $val.= "<input type='text' name='$name' value='".$this->getValue($name)."'>";
         $val = $this->surround($val);
         return $val;
    }
    
    public function inputAction($name){
        return "<input type='hidden' name='$name' value='1'>";
    }
    
    public function submit($title) {
        return "<button type='submit'>$title</button>";
        ;
    }
}
?>
