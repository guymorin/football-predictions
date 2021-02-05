<?php
/**
 *
 * Class Predictions
 * Generate predictions elements
 */
namespace FootballPredictions;
use \PDO;
use FootballPredictions\Section\Matchday;
use FootballPredictions\Plugin\MeteoConcept;

class Predictions
{
    private static $domBonus = array();
    private static $domMalus = array();
    private static $extBonus = array();
    private static $extMalus = array();
    private $bestPlayers1;
    private $bestPlayers2;
    private $cloud;
    private $cloudText;
    private $currentFormTeam1;
    private $currentFormTeam2;
    private $dom;
    private $ext;
    private $historyHome;
    private $historyDraw;
    private $historyAway;
    private $id;
    private $motivC1;
    private $motivC2;
    private $mv1;
    private $mv2;
    private $physicalC1;
    private $physicalC2;
    public $playedOdds;
    public $prediction;
    private $prob1;
    private $probD;
    private $prob2;
    public $probOdds1;
    public $probOddsD;
    public $probOdds2;
    public $sum1;
    public $sumD;
    public $sum2;
    private $team1Weather;
    private $team2Weather;
    private $trend1;
    private $trend2;
    private $v1;
    private $v2;
    
    /**
     *
     * @param array $data Form or database data
     */
    public function __construct(){
        $this->initValues();
    }
    
    private function initValues(){
        $this->cloud = $this->cloudText = $this->prediction = "";
        $this->currentFormTeam1 = $this->currentFormTeam2
        = $this->dom = $this->ext
        = $this->historyHome = $this->historyDraw = $this->historyAway
        = $this->id
        = $this->motivC1 = $this->motivC2
        = $this->bestPlayers1 = $this->bestPlayers2 
        = $this->mv1 = $this->mv2
        = $this->physicalC1 = $this->physicalC2
        = $this->playedOdds
        = $this->prob1 = $this->probD = $this->prob2
        = $this->probOdds1 = $this->probOddsD = $this->probOdds2
        = $this->sum1 = $this->sumD = $this->sum2
        = $this->team1Weather = $this->team2Weather
        = $this->trend1 = $this->trend2
        = $this->v1 = $this->v2 = 0;
    }
    
    private function setBestPlayers($d){
        // Best players
        $this->bestPlayers1 = intval($d->bestPlayers1);
        $this->bestPlayers2 = intval($d->bestPlayers2);
    }
    
    public function setCriteria($d,$pdo,$result,$manual=false){
        $this->initValues();
        $this->setMotivation($pdo,$d,$result);
        $this->setCurrentForm($pdo,$d,$result);
        $this->setPhysicalForm($pdo,$d,$result);
        $this->setBestPlayers($d);
        $this->setMarketValue($pdo,$d,$result,$manual);
        $this->setHomeAway($pdo,$d,$result);
        $this->setTrend($pdo,$d,$result,$manual);
        $this->setHistory($pdo,$d,$result,$manual);
        $this->setWeather($pdo, $d,$result,$manual);
    }
    
    private function setCurrentForm($pdo,$d,$result=false){
        // Current form
        if($result){
            $this->currentFormTeam1 = $d->currentForm1;
            $this->currentFormTeam2 = $d->currentForm2;
        } else {
            $this->currentFormTeam1=criterion("serieC1",$d,$pdo);
            $this->currentFormTeam2=criterion("serieC2",$d,$pdo);
        }
        $this->currentFormTeam1 = intval($this->currentFormTeam1);
        $this->currentFormTeam2 = intval($this->currentFormTeam2);
        
    }
    
    private function setHistory($pdo,$d,$result=false,$manual=false){
        // Predictions history
        $this->historyHome=$this->historyDraw=$this->historyAway=0;
        if($result or $manual){
            $this->historyHome = $d->histo1;
            $this->historyDraw = $d->histoD;
            $this->historyAway = $d->histo2;
        } else {
            $r = result('history',$pdo,$d,$this->team1Weather,$this->team2Weather);
            $this->historyHome=criterion("predictionsHistoryHome",$r,$pdo);
            $this->historyDraw=criterion("predictionsHistoryDraw",$r,$pdo);
            $this->historyAway=criterion("predictionsHistoryAway",$r,$pdo);
        }
    }
    
    private function setHomeAway($pdo,$d,$result=false){
        // Home / Away
        if($result){
            $this->dom = $d->home_away1;
            $this->ext = $d->home_away2;
        } else {
            $this->dom = 0;
            if(is_array(self::$domBonus)){
                if(in_array($d->eq1,self::$domBonus)) $this->dom=1;
            }
            if(is_array(self::$domMalus)){
                if(in_array($d->eq1,self::$domMalus)) $this->dom=(-1);
            }
            $this->ext = 0;
            if(is_array(self::$extBonus)){
                if(in_array($d->eq2,self::$extBonus)) $this->ext=1;
            }
            if(is_array(self::$extMalus)){
                if(in_array($d->eq2,self::$extMalus)) $this->ext=(-1);
            }
        }
        $this->dom = intval($this->dom);
        $this->ext = intval($this->ext);
    }
    
    private function setMarketValue($pdo,$d,$result=false,$manual=false){
        // Market value
        if($result or $manual){
            $this->mv1 = $d->marketValue1;
            $this->mv2 = $d->marketValue2;
        } else {
           $this->v1=intval(criterion("v1",$d,$pdo));
           $this->v2=intval(criterion("v2",$d,$pdo));
           if( ($this->v1 != 0) && ($this->v2 != 0) ){
               $this->mv1 = round(sqrt($this->v1/$this->v2));
               $this->mv2 = round(sqrt($this->v2/$this->v1));
           } else {
               $this->mv1 = $this->mv2 = 0;
           }
        }
        $this->mv1 = intval($this->mv1);
        $this->mv2 = intval($this->mv2);
    }
    
    private function setMotivation($pdo,$d,$result=false) {
        // Motivation
        if($result){
            $this->motivC1 = $d->motivation1;
            $this->motivC2 = $d->motivation2;
        } else {
            $this->motivC1=criterion("motivC1",$d,$pdo);
            $this->motivC2=criterion("motivC2",$d,$pdo);
        }
        $this->motivC1 = intval($this->motivC1);
        $this->motivC2 = intval($this->motivC2);
    }
    
    private function setPhysicalForm($pdo,$d,$result=false){
        // Physical form
        if($result){
            $this->physicalC1 = $d->physicalForm1;
            $this->physicalC2 = $d->physicalForm2;
        } else {
            $this->physicalC1=criterion("physicalC1",$d,$pdo);
            $this->physicalC2=criterion("physicalC2",$d,$pdo);
        }
        $this->physicalC1 = intval($this->physicalC1);
        $this->physicalC2 = intval($this->physicalC2);
    }
    
    public function setPlayedOdds($d){
        $this->playedOdds=0;
        switch($this->prediction){
            case "1":
                $this->playedOdds = $d->odds1;
                break;
            case ("D"):
                $this->playedOdds = $d->oddsD;
                break;
            case "2":
                $this->playedOdds = $d->odds2;
                break;
        }
    }
    
    public function setProb(){
        $this->prob1 = setProbSum('1',$this->sum1, $this->sumD, $this->sum2);
        $this->probD = setProbSum('D',$this->sum1, $this->sumD, $this->sum2);
        $this->prob2 = setProbSum('2',$this->sum1, $this->sumD, $this->sum2);
        $this->probOdds1 = $this->setProbOdds($this->prob1);
        $this->probOddsD = $this->setProbOdds($this->probD);
        $this->probOdds2 = $this->setProbOdds($this->prob2);
        if($this->probOdds1==99) $this->probOdds1 = $this->probOddsD * 2;
        if($this->probOdds2==99) $this->probOdds2 = $this->probOddsD * 2;
    }
    
    private function setProbOdds($v){
        $v = intval($v);
        if($v<1) $v=99;
        else $v = round(100/$v,2);
        return $v;
    }
    
    private function setTrend($pdo,$d,$result=false,$manual=false){
        // Trend
        $this->trend1= $this->trend2 = 0;
        if($manual or $result){
            $this->trend1 = $d->trend1;
            $this->trend2 = $d->trend2;
        } else {
            if(isset($_SESSION['matchdayNum'])) $matchdayNum = $_SESSION['matchdayNum'];
            else $matchdayNum = $d->number;
            if($matchdayNum > 3){
                $trendTeam1 = criterion('trendTeam1', $d, $pdo);
                $trendTeam2 = criterion('trendTeam2', $d, $pdo);
                if($trendTeam1>6){
                    $this->trend1 = 1;
                }
                if($trendTeam2<2){
                    $this->trend2 = -1;
                }
            }
        }
        $this->trend1 = intval($this->trend1);
        $this->trend2 = intval($this->trend2);
    }
    
    private function setWeather($pdo,$d,$result=false,$manual=false){
        if($manual or $result){
            if(isset($d->weather1)) $this->team1Weather = $d->weather1;
            if(isset($d->weather1)) $this->team2Weather = $d->weather2;
        } else {
            // Weather
            
            if($d->date!=""){
                $date1 = new \DateTime($d->date);
                $date2 = new \DateTime(date('Y-m-d'));
                $diff = $date2->diff($date1)->format("%a");
                
                if($diff>=0 && $diff<14){
                    
                    $req = "SELECT plugin_name
                    FROM plugin
                    WHERE activate=1
                    AND plugin_name='meteo-concept';";
                    $data = $pdo->prepare($req,[],true);
                    $counter = $pdo->rowCount();
                    if($counter==1){
                        MeteoConcept::setWeather($pdo, $diff, $this->mv1, $this->mv2, $d->weather_code);
                        $this->team1Weather = MeteoConcept::getTeamWeather(1);
                        $this->team2Weather = MeteoConcept::getTeamWeather(2);
                    }
                }
            }
        }
        $this->team1Weather = intval($this->team1Weather);
        $this->team2Weather = intval($this->team2Weather);
    }
    
    public function displayCriteria($d, $form, $manual=false){
        // Display table
        if($d->result=="") echo $form->inputHidden('id_match[]',$d->id_matchgame);
        echo "	 <table class='prediction";
        if($manual) echo " manual";
        echo "'>\n";
        echo "  		<tr>\n";
        echo "  		  <th>".Theme::icon('matchday')." ".Language::title('MD').$_SESSION['matchdayNum'];
        echo ", ".$d->date."<br />";
        echo Theme::icon('team')."&nbsp;".$d->name1."<br />";
        echo Theme::icon('team')."&nbsp;".$d->name2;
        echo "</th>\n";
        echo "            <th>1</th>\n";
        echo "            <th>". Language::title('draw');
        if($manual) echo "<input type='hidden' name='id_match[]' value='".$d->id_matchgame."'>";
        echo "</th>\n";
        echo "            <th>2</th>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('motivation')) . "</td>\n";
        if($d->result!="") echo "<td>".$this->motivC1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='motivation1[$this->id]' value='".$this->motivC1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$this->motivC2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='motivation2[$this->id]' value='".$this->motivC2."' placeholder='0'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . Language::title('physicalForm') . "</td>\n";
        if($d->result!="") echo "<td>".$this->physicalC1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='physicalForm1[$this->id]' value='".$this->physicalC1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$this->physicalC2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='physicalForm2[$this->id]' value='".$this->physicalC2."' placeholder='0'></td>\n";
        echo "          </tr>\n";

        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('bestPlayers')) . "</td>\n";
        if($d->result!="") echo "<td>".$d->bestPlayers1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='bestPlayers1[$this->id]' value='".$d->bestPlayers1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$d->bestPlayers2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='bestPlayers2[$this->id]' value='".$d->bestPlayers2."' placeholder='0'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>";
        if(($d->result=="") and ($manual==false)){
            echo "<a href='#' class='tooltip'><big>".$this->cloud."</big><span>".$this->cloudText."</span></a> ";
        }
        echo Language::title('weather');
        echo "</td>\n";
        if($d->result!="") echo "<td>".$this->team1Weather."</td>\n";
        else {
            echo "  		  <td><input size='1' type='number' placeholder='0' ";
            echo "name='weather1[$this->id]' value='".$this->team1Weather."'></td>\n";
        }
        echo "  		  <td></td>\n";
        
        if($d->result!="") echo "<td>".$this->team2Weather."</td>\n";
        else {
            echo "  		  <td><input size='1' type='number' placeholder='0' ";
            echo "name='weather2[$this->id]' value='".$this->team2Weather."'></td>\n";
        }
        echo "          </tr>\n";
         
        echo "  		<tr>\n";
        echo "  		  <td>";
        echo "<a href='#' class='tooltip'><big>".Theme::icon('team')."</big>";
        echo "<span>".Language::title('marketValueText')."</span></a>";
        echo " " . Language::title('marketValue');
        echo "</td>";
        if($d->result!="") echo "<td>".$this->mv1."</td>\n";
        else {
            echo "  		  <td><input size='1' ";
            if($manual==false) echo "type='type='text' readonly ";
            else echo "type='number' placeholder='0' ";
            echo "name='marketValue1[$this->id]' value='".$this->mv1."'></td>\n";
        }
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$this->mv2."</td>\n";
        else {
            echo "  		  <td><input size='1' ";
            if($manual==false) echo "type='type='text' readonly ";
            else echo "type='number' placeholder='0' ";
            echo "name='marketValue2[$this->id]' value='".$this->mv2."'></td>\n";
        }
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>";
        echo "<a href='#' class='tooltip'><big>".Theme::icon('championship')."</big>";
        echo "<span>".Language::title('homeAwayText')."</span></a>";
        echo " " . Language::title('home') . " / " . Language::title('away');
        echo "</td>";
        if($d->result!="") echo "<td>".$this->dom."</td>\n";
        else {
            echo "  		  <td><input size='1' ";
            if($manual==false) echo "type='type='text' readonly ";
            else echo "type='number' placeholder='0' ";
            echo "name='home_away1[$this->id]' value='".$this->dom."'></td>\n";
        }
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$this->ext."</td>\n";
        else {
            echo "  		  <td><input size='1' ";
            if($manual==false) echo "type='type='text' readonly ";
            else echo "type='number' placeholder='0' ";
            echo "name='home_away2[$this->id]' value='".$this->ext."'></td>\n";
        }
        echo "          </tr>\n";
        
        echo "          <tr>\n";
        echo "  		  <td>";
        echo "<a href='#' class='tooltip'><big>".Theme::icon('trend')."</big>";
        echo "<span>".Language::title('trendText')."</span></a>";
        echo " " . Language::title('trend');
        echo "</td>";

        if($d->result!="") echo "<td>".$this->trend1."</td>\n";
        else {
            echo "  		  <td><input size='1' ";
            if($manual==false) echo "type='type='text' readonly ";
            else echo "type='number' placeholder='0' ";
            echo "name='trend1[$this->id]' value='";
            echo $this->trend1;
            echo "'></td>\n";
        }
        
        echo "  		  <td></td>\n";
        
        if($d->result!="") echo "<td>".$this->trend2."</td>\n";
        else {
            echo "  		  <td><input size='1' ";
            if($manual==false) echo "type='type='text' readonly ";
            else echo "type='number' placeholder='0' ";
            echo "name='trend2[$this->id]' value='";
            echo $this->trend2;
            echo "'></td>\n";
        }
        
        echo "          </tr>\n";
        
        echo "          <tr>\n";
        echo "  		  <td>";
        echo "<a href='#' class='tooltip'><big>".Theme::icon('predictionsHistory')."</big>";
        echo "<span>".Language::title('predictionsHistoryText')."</span></a>";
        echo " " . Language::title('predictionsHistory');
        echo "</td>";
        
        if($d->result!="") echo "<td>$this->historyHome</td>\n";
        else {
            echo "  		  <td><input size='1' ";
            if($manual==false) echo "type='type='text' readonly ";
            else echo "type='number' placeholder='0' ";
            echo "name='histo1[$this->id]' value='";
            echo $this->historyHome;
            echo "'></td>\n";
        }
        
        if($d->result!="") echo "<td>$this->historyDraw</td>\n";
        else {
            echo "  		  <td><input size='1' ";
            if($manual==false) echo "type='type='text' readonly ";
            else echo "type='number' placeholder='0' ";
            echo "name='histoD[$this->id]' value='";
            echo $this->historyDraw;
            echo "'></td>\n";
        }
        
        if($d->result!="") echo "<td>$this->historyAway</td>\n";
        else {
            echo "  		  <td><input size='1' ";
            if($manual==false) echo "type='type='text' readonly ";
            else echo "type='number' placeholder='0' ";
            echo "name='histo2[$this->id]' value='";
            echo $this->historyAway;
            echo "'></td>\n";
        }
        
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td><strong>" . (Language::title('criterionSum')) . "</strong></td>\n";
        echo "  		  <td><strong>$this->sum1</strong></td>\n";
        echo "  		  <td><strong>$this->sumD</strong></td>\n";
        echo "  		  <td><strong>$this->sum2</strong></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('prediction')) . "</td>\n";
        echo "  		  <td>";
        if($this->prediction == '1') echo Theme::icon('OK');
        else echo Theme::icon('KO');
        echo "</td>\n";
        echo "  		  <td>";
        if($this->prediction == 'D') echo Theme::icon('OK');
        else echo Theme::icon('KO');
        echo "</td>\n";
        echo "  		  <td>";
        if($this->prediction == '2') echo Theme::icon('OK');
        else echo Theme::icon('KO');
        echo "</td>\n";
        echo "          </tr>\n";
        
        if($d->result!=""){
            echo "  		<tr>\n";
            echo "  		  <td>" . (Language::title('result')) . "</td>\n";
            echo "  		  <td>";
            if($d->result == '1'){
                if($d->result == $this->prediction) echo Theme::icon('winOK');
                else echo Theme::icon('OK');
            } else echo Theme::icon('KO');
            echo "</td>\n";
            echo "  		  <td>";
            if($d->result == 'D'){
                if($d->result == $this->prediction) echo Theme::icon('winOK');
                else echo Theme::icon('OK');
            } else echo Theme::icon('KO');
            echo "</td>\n";
            echo "  		  <td>";
            if($d->result == '2'){
                if($d->result == $this->prediction) echo Theme::icon('winOK');
                else echo Theme::icon('OK');
            } else echo Theme::icon('KO');
            echo "</td>\n";
            echo "          </tr>\n";
        }
        echo "	 </table>\n";
    }
    
    static function teamsBonusMalus($pdo){
        $r = array();
        
        // Best teams home
        $r = result('bestHome',$pdo);
        foreach($r as $v) self::$domBonus[] = $v->id_team;
        
        // Worst teams home
        $r = result('worstHome',$pdo);
        foreach($r as $v) self::$domMalus[] = $v->id_team;
        
        // Best teams away
        $r = result('bestAway',$pdo);
        foreach($r as $v) self::$extBonus[] = $v->id_team;
        
        // Worst teams away
        $r = result('worstAway',$pdo);
        foreach($r as $v) self::$extMalus[] = $v->id_team;
    }
    
    static function switchButton($form,$type){
        // Switch form
        $val = "<form id='criterion' action='index.php?page=prediction' method='POST'>\n";
        $icon = Theme::icon('switch')." ";
        switch($type){
            case "toAuto":
                $val .= $form->inputHidden('manual','0');
                $val .= $form->submit($icon.Language::title('swithToAuto'));
                break;
            case "toManual":
                $val .= $form->inputHidden('modify','2');
                $val .= $form->inputHidden('manual','1');
                $val .= $form->submit($icon.Language::title('swithToManual'));
                break;
            default:
        }
        $val .= "</form>\n";
        $val .= "<br />\n";
        return $val;
    }
    
    public function sumCriterion($d){
        $this->id = $d->id_matchgame;
        
        $this->sum1 =
        intval($this->motivC1)
        +intval($this->currentFormTeam1)
        +intval($this->physicalC1)
        +intval($this->team1Weather)
        +intval($this->bestPlayers1)
        +intval($this->mv1)
        +intval($this->dom)
        +intval($this->historyHome)
        +intval($this->trend1);
        
        $this->sum2 =
        intval($this->motivC2)
        +intval($this->currentFormTeam2)
        +intval($this->physicalC2)
        +intval($this->team2Weather)
        +intval($this->bestPlayers2)
        +intval($this->mv2)
        +intval($this->ext)
        +intval($this->historyAway)
        +intval($this->trend2);
        
        $this->sumD = setSumD($this->sum1,$this->sum2,$this->historyDraw);
        $this->sumD = intval($this->sumD);
        
        $this->prediction = setPrediction($this->sum1, $this->sumD, $this->sum2);
    }
}
?>
