<?php
/**
 * 
 * Class Forms
 * Generate forms elements
 */
namespace FootballPredictions;
use \PDO;

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
    public function __construct($data = array()){
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
     * @return string HTML code
     */
    public function input($label='',$name){
         $val = "";
         if($label!='') $val.= $this->label($label);
         $val.= "   <input type='text' name='$name' value='".$this->getValue($name)."'>\n";
         $val = $this->surround($val);
         return $val;
    }
    
    /**
     * 
     * @param string $name
     * @return string HTML code
     */
    public function inputAction($name){
        return "    <input type='hidden' name='$name' value='1'>\n";
    }
    
    /**
     * 
     * @param string $name
     * @param string $value
     * @return string HTMLcode
     */
    public function inputDate($name, $value){
        $val = "   <input type='date' name='$name' value='".$value."'>\n";
        return $val;
    }
    
    
    /**
     * 
     * @param string $name
     * @param string $value
     * @return string HTML code
     */
    public function inputHidden($name, $value){
        return "    <input type='hidden' name='$name' value='$value'>\n";
    }
    
    /**
     * 
     * @param string $name
     * @param int $value
     * @param int $step Interval when using up and down
     * @return string HTML code
     */
    public function inputNumber($name, $value, $step){
        $val = "   <input type='number' step='".$step."' name='$name' value='".$value."'>\n";
        return $val;
    }
    
    /**
     * 
     * @param string $title
     * @return string HTML code
     */
    public function label($title){
        return "    <label>$title : </label>\n";
    }
    
    /**
     *
     * @param string $title
     * @return string HTML code
     */
    public function labelBr($title){
        return $this->label($title)."<br />\n";
    }

    /**
     *
     * @param int $id
     * @param string $name
     * @param int $value
     * @param boolean $checked
     * @return string HTML code
     */
    public function inputRadio($id, $name, $value, $checked=false){
        $val = "   <input type='radio' ";
        $val.= "id='$id' name='$name' value='$value'";
        $checked ? $val.= " checked" : null;
        $val.= ">\n";
        return $val;
    }
    
    /**
     * 
     * @param string $name
     * @param array $response Result of a query
     * @return string HTML code
     */
    public function selectSubmit($name, $response){
        require '../lang/fr.php';
        $val = "    <select name='$name' onchange='submit()'>\n";
        $val.= "        <option value='0'>...</option>\n";
        while ($data = $response->fetch(PDO::FETCH_NUM)){
            $val.= "  		<option value='".$data[0].",".$data[1]."'>";
            $name == "matchdaySelect" ? $val.= $title_MD : null;
            $val.= $data[1]."</option>\n";
        }
        $val.= "    </select>\n";
        $val.="<br /><noscript>".$this->submit($title_select)."</noscript>\n";
        return $val;
    }
    
    /**
     * 
     * @param string $title Value
     * @return string HTML code
     */
    public function submit($title){
        return "    <button type='submit'>$title</button>\n";
        ;
    }
}
?>
