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
    
    /* Table elements */
    
    public function addTr($code, $colspan=0){
        $val = "    <tr>\n";
        foreach($code as $v){
            $val .= "       <td";
            if($colspan>0) $val .= " colspan='$colspan'";
            $val .= ">$v</td>\n";
        }
        $val .= "   </tr>";
        return $val;
    }
    
    /* Forms elements */
    
    /**
     * 
     * @param string $label
     * @param string $name
     * @return string HTML code
     */
    public function input($label='',$name){
         $val = '';
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
    public function inputDate($label='', $name, $value){
        $val = '';
        if($label!='') $val .= $this->label($label);
        $val .= "   <input type='date' name='$name' value='".$value."'>\n";
        $val = $this->surround($val);
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
    public function inputNumber($label='', $name, $value, $step){
        $val= '';
        if($label!='') $val .= $this->label($label);
        $val .= "   <input type='number' step='".$step."' name='$name' value='".$value."'>\n";
        $val = $this->surround($val);
        return $val;
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
     * Radio buttons for positions: Goalkeeper, Defender, Midfielder and Forward
     * @return string
     */
    public function inputRadioPosition($data=null){
        require '../lang/fr.php';

        $val = $this->labelId('Goalkeeper', $title_goalkeeper);
        if ($data->position=="Goalkeeper"){
            $val .= $this->inputRadio('Goalkeeper', 'position', 'Goalkeeper', true);
        } else $val .= $this->inputRadio('Goalkeeper', 'position', 'Goalkeeper');

        $val .= $this->labelId('Defender', $title_defender);
        if ($data->position=="Defender"){
            $val .= $this->inputRadio('Defender', 'position', 'Defender', true);
        } else $val .= $this->inputRadio('Defender', 'position', 'Defender');

        $val .= $this->labelId('Midfielder', $title_midfielder);
        if ($data->position=="Midfielder"){
            $val .= $this->inputRadio('Midfielder', 'position', 'Midfielder', true);
        } else {
            $val .= $this->inputRadio('Midfielder', 'position', 'Midfielder');
        }
        
        $val .= $this->labelId('Forward', $title_forward);
        if ($data->position=="Forward"){
            $val .= $this->inputRadio('Forward', 'position', 'Forward', true);
        } else {
            $val .= $this->inputRadio('Forward', 'position', 'Forward');
        }
        $this->surround = "span";
        $val = $this->surround($val);
        $this->surround = "p";
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
     * @param string $id "For" attribute to activate checkbox or radio ID
     * @param string $title
     * @return string HTML code
     */
    public function labelId($id, $title){
        return "    <label for='$id'>$title : </label>\n";
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
        return $this->surround($val);
    }
    
    public function selectPlayer($data,$selected=null){
        require '../lang/fr.php';
        $val = $this->labelBr($title_selectThePlayer);
        $val .= "     <select multiple size='10' name='id_player'>\n";
        foreach ($data as $d)
        {
            $val .= "  		<option value='".$d->id_player."'";
            $d->id_player==$selected ? $val .= " selected" : null;
            $val .= ">".mb_strtoupper($d->name,'UTF-8')." ".ucfirst($d->firstname)."</option>\n";
        }
        $val .= "	   </select>\n";
        return $this->surround($val);
    }
    
    public function selectTeam($data,$selected=null){
        require '../lang/fr.php';
        $val = $this->labelBr($title_team);
        $val .= "     <select multiple size='10' name='id_team'>\n";
        foreach ($data as $d)
        {
            $val .= "  		<option value='".$d->id_team."'";
            $d->id_team==$selected ? $val .= " selected" : null;
            $val .= ">".$d->name."</option>\n";
        }
        $val .= "	   </select>\n";
        return $this->surround($val);
    }
    
    /**
     * 
     * @param string $title Value
     * @return string HTML code
     */
    public function submit($title){
        return "    <br /><button type='submit'>$title</button>\n";
        ;
    }
}
?>
