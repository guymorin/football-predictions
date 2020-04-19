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
    private function surround($html, $class=null){
        $val = '';
        if($class!=null) $val = "<{$this->surround} class='" . $class . "'>" . $html . "</{$this->surround}>";
        else $val = "<{$this->surround}>$html</{$this->surround}>";
        return $val;
    }
    
    private function surroundFieldset($html, $class=null){
        $this->surround = "fieldset";
        $val = '';
        if($class!=null) $val = "<{$this->surround} class='" . $class . "'>" . $html . "</{$this->surround}>";
        else $val = "<{$this->surround}>$html</{$this->surround}>";
        $this->surround = "p";
        return $val;
    }
    
    /**
     * 
     * @param array $index Data index
     * @return NULL
     */
    private function getValue($index){
        $index = preg_replace("#\[.*\]#", '', $index);
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
    
    public function deleteForm($page, $name, $id, $confirm=false, $nameOther=null, $idOther=null){
        
        $val = "<form action='index.php?page=$page' method='POST'>\n";
        if($confirm==true) $val .= $this->inputHidden('delete',2);
        else $val .= $this->inputAction('delete');
        $val .= $this->inputHidden($name, $id);
        isset($nameOther) ? $val .= $this->inputHidden($nameOther, $idOther) : null;
        if($confirm==true) $val .= $this->submit(Language::title('yes'));
        else $val .= $this->submit("&#9888 " . (Language::title('delete')) . " &#9888");
        $val .= "</form>\n";
        return $val;
    }
    
    public function popupConfirm($page, $name, $id, $nameOther = null, $idOther = null){
        
        $val = "<div id='overlay'>\n";
        $val .= "  <div class='update confirm'>\n";
        $val .= "      <p class='close'><a href='index.php?page=$page'>&times;</a></p>\n";
        $val .= "      <p>" . (Language::title('delete')) . " ?</p>\n";
        $val .= "      <span>\n";
        $val .= $this->deleteForm($page, $name, $id, true, $nameOther, $idOther);
        $val .= "      </span>\n";
        $val .= "      <span>\n";
        $val .= "       <form action='index.php?page=$page' method='POST'>\n";
        $val .= $this->inputAction('modify');
        $val .= $this->inputHidden($name, $id);
        $val .= $this->submit(Language::title('no'));
        $val .= "       </form>\n";
        $val .= "      </span>\n";
        $val .= "  </div>\n";
        $val .= "</div>\n";
        return $val;
    }
    
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
         $val.= "<input maxlength='50' type='text' name='$name' value='".$this->getValue($name)."'>";
         $val = $this->surround($val);
         return $val;
    }
    
    /**
     * 
     * @param string $name
     * @return string HTML code
     */
    public function inputAction($name){
        return "<input type='hidden' name='$name' value='1'>";
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
        $val .= "<input type='date' name='$name' value='".$value."'>";
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
        if($label!='') $val .= $this->label($label, 'right');
        $val .= "<input type='number' step='".$step."' name='$name' value='".$value."'>";
        return $val;
    }
    
    public function inputNumberOdds($data = null){
        $val= '';
        $val .= "<legend>" . (Language::title('odds')) . "</legend>\n";
        $odds1 = $oddsD = $odds2 = 0;
        if(isset($data)){
            $odds1 = $data->odds1;
            $oddsD = $data->oddsD;
            $odds2 = $data->odds2;
        }
        $val .= $this->inputNumber('1', 'odds1', $odds1, '0.01');
        $val .= $this->inputNumber(Language::title('draw'), 'oddsD', $oddsD, '0.01');
        $val .= $this->inputNumber('2', 'odds2', $odds2, '0.01');
        $val .= "<br />";
        $val = $this->surroundFieldset($val, 'odds');
        return $val;
    }

    public function inputPassword($label='',$name){
        $val = '';
        if($label!='') $val.= $this->label($label);
        $val.= "<input type='password' minlength='8' autocomplete='current-password' required name='$name' value='".$this->getValue($name)."'>";
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
        $val = "<input type='radio' ";
        $val.= "id='$id' name='$name' value='$value'";
        $checked ? $val.= " checked='checked'" : null;
        $val.= ">";
        return $val;
    }
    
    /**
     * Radio buttons for positions: Goalkeeper, Defender, Midfielder and Forward
     * @return string
     */
    public function inputRadioPosition($data=null){
        $val = '<legend>' . (Language::title('position')). '</legend>';
        $gk = $df = $md = $fw = false;
        if (isset($data)){
            if($data->position=="Goalkeeper") $gk = true;
            if($data->position=="Defender") $df = true;
            if($data->position=="Midfielder") $md = true;
            if($data->position=="Forward") $fw = true;
        }
        $val .= $this->labelId('Goalkeeper', Language::title('goalkeeper'), 'right');
        $val .= $this->inputRadio('Goalkeeper', 'position', 'Goalkeeper', $gk);
        $val .= "<br />";
        $val .= $this->labelId('Defender', Language::title('defender'), 'right');
        $val .= $this->inputRadio('Defender', 'position', 'Defender', $df);
        $val .= "<br />";
        $val .= $this->labelId('Midfielder', Language::title('midfielder'), 'right');
        $val .= $this->inputRadio('Midfielder', 'position', 'Midfielder', $md);
        $val .= "<br />";
        $val .= $this->labelId('Forward', Language::title('forward'), 'right');
        $val .= $this->inputRadio('Forward', 'position', 'Forward', $fw);
        $val = $this->surroundFieldset($val, 'position');
        return $val;
    }

    public function inputRadioResult($data = null){
        $val = '<legend>' . (Language::title('result')). '</legend>';
        $result1 = $resultD = $result2 = false;
        if(isset($data)){
            if($data->result == "1") $result1 = true;
            if($data->result == "D") $resultD = true;
            if($data->result == "2") $result2 = true;
        }
        $val .= $this->labelId('1', '1', 'right');
        $val .= $this->inputRadio('1', 'result', '1', $result1);
        $val .= $this->labelId('D', Language::title('draw'), 'right');
        $val .= $this->inputRadio('D', 'result', 'D', $resultD);
        $val .= $this->labelId('2', '2', 'right');
        $val .= $this->inputRadio('2', 'result', '2', $result2);
        $val = $this->surroundFieldset($val, 'result');
        return $val;
    }
    
    /**
     * 
     * @param string $title
     * @return string HTML code
     */
    public function label($title, $class=null){
        if($class==null) $val = "<label>$title&nbsp;: </label>";
        else $val = "<label class='$class'>$title&nbsp;: </label>";
        return $val;
    }
 
    /**
     *
     * @param string $id "For" attribute to activate checkbox or radio ID
     * @param string $title
     * @return string HTML code
     */
    public function labelId($id, $title, $class=null){
        if($class==null) $val = "<label for='$id'>$title : </label>";
        else $val = "<label for='$id' class='$class'>$title : </label>";
        return $val;
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
     * @param string $name Name attribute
     * @param array $response Data to fetch
     * @param int or string $id Selected value
     * @return string
     */
    public function select($name, $response, $id){
        $val = "<fieldset>\n";
        $val .= "   <legend>" . (Language::title($name)) . "</legend>";
        $val .= "   <select name='" . $name . "'>\n";
        while ($data = $response->fetch(PDO::FETCH_NUM)){
            $val .= "       <option value='$data[0]'";
            if($data[0]==$id) $val .= " selected";
            $val .= ">";
            if($name == 'role') $val .= Language::title($data[1]); 
            else $val .= $data[1];
            $val .= "</option>\n";
        }
        $val .= "   </select>";
        $val .= "</fieldset>\n";
        return $val;
    }
    
    public function selectData($name, $data, $id){
        $val = "<fieldset>\n";
        $val .= "   <legend>" . (Language::title($name)) . "</legend>";
        $val .= "   <select name='" . $name . "'>\n";
        foreach($data as $d){
            $val .= "       <option value='$d'";
            if($d==$id) $val .= " selected";
            $val .= ">$d</option>\n";
        }
        $val .= "   </select>";
        $val .= "</fieldset>\n";
        return $val;
    }
    
    /**
     * 
     * @param string $name
     * @param array $response Result of a query
     * @return string HTML code
     */
    public function selectSubmit($name, $response, $autoSubmit = true){
        
        $championshipId = $seasonId = 0;
        isset($_SESSION['championshipId']) ? $championshipId = $_SESSION['championshipId'] : null;
        isset($_SESSION['seasonId']) ? $seasonId = $_SESSION['seasonId'] : null;
        isset($_SESSION['userId']) ? $userId = $_SESSION['userId'] : null;

        $val = "    <select name='$name'";
        if($autoSubmit) $val.= "onchange='submit()'";
        $val .= ">\n";
        $val .= "        <option value='0'>...</option>\n";
        while ($data = $response->fetch(PDO::FETCH_NUM)){
            
            switch($name){
                case "id_championship":
                case "id_season":
                case "id_team":
                    $val .= "  		<option value='" . $data[0] . "'";
                    if(
                    ($name == "id_championship" && $data[0] == $championshipId)
                        ||($name=="id_season" && $data[0] == $seasonId)
                    ){
                        $val .= " disabled";
                    }
                    $val .= ">".$data[1];
                    break;
                case "id_fp_user":
                    $val .= "  		<option value='" . $data[0] . "'";
                    if($name=="id_fp_user" && $data[0] == $userId) {
                            $val .= " disabled";
                    }
                    $val .= ">".ucfirst($data[1]);
                    break;
                case "id_matchgame":
                    $val .= "  		<option value='" . $data[0] . "'>";
                    $val .= $data[1] . " - " . $data[2];
                    break;
                case "id_player":
                    $val .= "  		<option value='" . $data[0] . "'>";
                    $val.= mb_strtoupper($data[1],'UTF-8') . " " . ucfirst($data[2]);
                    break;
                case "matchdayModify":
                case "matchdaySelect":
                    $val .= "  		<option value='" . $data[0] . "," . $data[1] . "'>";
                    $val.= Language::title('MD').$data[1];
                    break;
                default:
                    $val .= "  		<option value='" . $data[0] . "," . $data[1] . "'>";
                    $val .= $data[1];     
            }
            $val .= "</option>\n";
        }
        $val .= "    </select>\n";
        if($autoSubmit) $val .= "<noscript>".$this->submit(Language::title('select'))."</noscript>\n";
        return $val;
    }
    
    /**
     * 
     * @param object $pdo Database
     * @param string $name Name attribute
     * @param string $selected Selected value if needed
     * @param array $data Data to display
     * @return string
     */
    public function selectPlayer($pdo, $name='id_player', $selected=null, $data=null){
        
        if($data == null){
            $req = "SELECT j.id_player, j.name, j.firstname, c.name as team
            FROM player j
            LEFT JOIN season_team_player scj ON scj.id_player=j.id_player
            LEFT JOIN season_championship_team scc ON scc.id_team=scj.id_team
            LEFT JOIN team c ON c.id_team=scj.id_team
            WHERE scc.id_season = :id_season
            AND scc.id_championship = :id_championship
            ORDER BY j.name, j.firstname;";
            $data = $pdo->prepare($req,[
                'id_season' => $_SESSION['seasonId'],
                'id_championship' => $_SESSION['championshipId']
            ],true);
        }
        $val = "     <select name='";
        if($name == null) $val .= 'id_player[]';
        else $val .= $name;
        $val .= "'>\n";
        $val .= "        <option value='0'>...</option>\n";
        foreach ($data as $d)
        {
            $val .= "  		<option value='" . $d->id_player . "'";
            $d->id_player==$selected ? $val .= " selected" : null;
            $val .= ">" . mb_strtoupper($d->name,'UTF-8') . " " . ucfirst($d->firstname);
            $val .= " [" . $d->team . "]</option>\n";
        }
        $val .= "	   </select>\n";
        return $this->surround($val);
    }
    public function selectTeam($pdo, $name='id_team', $selected=null, $data=null){
           
        if($data == null){
            $req="SELECT c.id_team, c.name FROM team c
            LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team
            WHERE scc.id_season=:id_season AND scc.id_championship=:id_championship 
            ORDER BY c.name;";
            $data = $pdo->prepare($req,[
                'id_season' => $_SESSION['seasonId'],
                'id_championship' => $_SESSION['championshipId']
            ],true);
        }
               
        $val = '<legend>' . (Language::title('team')). '</legend>';
        $val .= "     <select name='";
        if($name == null) $val .= 'id_team';
        else $val .= $name;
        $val .= "'>\n";
        $val .= "        <option value='0'>...</option>\n";
        foreach ($data as $d)
        {
            $val .= "  		<option value='".$d->id_team."'";
            $d->id_team==$selected ? $val .= " selected" : null;
            $val .= ">".$d->name."</option>\n";
        }
        $val .= "	   </select>\n";
        $this->surround = "fieldset";
        $val = $this->surround($val);
        $this->surround = "p";
        return $val;
    }
    
    /**
     * 
     * @param string $title Value
     * @return string HTML code
     */
    public function submit($title){
        $val = "<button type='submit'>$title</button>";
        return $this->surround($val);
        ;
    }

}
?>
