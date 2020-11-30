<?php
/**
 *
 * Class Predictions
 * Generate predictions elements
 */
namespace FootballPredictions;
use \PDO;
use FootballPredictions\Section\Matchday;

class Predictions
{
    private static $domBonus = array();
    private static $domMalus = array();
    private static $extBonus = array();
    private static $extMalus = array();
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
    private $sum1;
    private $sumD;
    private $sum2;
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
        $this->currentFormTeam1 = $this->currentFormTeam2 = $this->dom = $this->ext = $this->historyHome = $this->historyDraw = $this->historyAway = $this->id = $this->motivC1 = $this->motivC2 = $this->mv1 = $this->mv2 = $this->physicalC1 = $this->physicalC2 = $this->sum1 = $this->sumD = $this->sum2 = $this->team1Weather = $this->team2Weather = $this->trend1 = $this->trend2 = $this->v1 = $this->v2 = 0;
    }

    public function setCriteria($d,$pdo,$result){
        $this->cloud = $this->cloudText = "";
        // If results
        if($result==true){
            $this->motivC1 = $d->motivation1;
            $this->motivC2 = $d->motivation2;
            $this->currentFormTeam1 = $d->currentForm1;
            $this->currentFormTeam2 = $d->currentForm2;
            $this->physicalC1 = $d->physicalForm1;
            $this->physicalC2 = $d->physicalForm1;
            if(isset($d->weather1)) $this->team1Weather = $d->weather1;
            if(isset($d->weather1)) $this->team2Weather = $d->weather2;
            $this->mv1 = $d->marketValue1;
            $this->mv2 = $d->marketValue2;
            $this->dom = $d->home_away1;
            $this->ext = $d->home_away2;
        // Else is predictions
        } else {
            // Motivation
            $this->motivC1=criterion("motivC1",$d,$pdo);
            $this->motivC2=criterion("motivC2",$d,$pdo);
            
            // Current form
            $this->currentFormTeam1=criterion("serieC1",$d,$pdo);
            $this->currentFormTeam2=criterion("serieC2",$d,$pdo);
            
            // Physical form
            $this->physicalC1=criterion("physicalC1",$d,$pdo);
            $this->physicalC2=criterion("physicalC2",$d,$pdo);
            
            // Market value
            $this->v1=criterion("v1",$d,$pdo);
            $this->v2=criterion("v2",$d,$pdo);
            if( ($this->v1 != 0) && ($this->v2 != 0) ){
                $this->mv1 = round(sqrt($this->v1/$this->v2));
                $this->mv2 = round(sqrt($this->v2/$this->v1));
            } else {
                $this->mv1 = $this->mv2 = 0;
            }
            
            // Home / Away
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
            
            // Weather
            if($d->date!=""){
                $date1 = new \DateTime($d->date);
                $date2 = new \DateTime(date('Y-m-d'));
                $diff = $date2->diff($date1)->format("%a");
                
                if($diff>=0 && $diff<14){
                    $api="https://api.meteo-concept.com/api/forecast/daily/".$diff."?token=1aca29e38eb644104b41975b55a6842fc4fb2bfd2f79f85682baecb1c5291a3e&insee=".$d->weather_code;
                    $weatherData = file_get_contents($api);
                    $rain=0;
                    
                    if ($weatherData !== false){
                        $decoded = json_decode($weatherData);
                        $city = $decoded->city;
                        $forecast = $decoded->forecast;
                        $rain=$forecast->rr1;
                    }
                    switch($rain){
                        case ($rain==0):
                            $this->cloud="&#x1F323;";// Sun
                            $this->cloudText=Language::title('weatherSun');
                            break;
                        case ($rain>=0 && $rain<1):
                            $this->cloud="&#x1F324;";// Low rain
                            $this->cloudText=Language::title('weatherLowRain');
                            $weather=0;
                            // if market value of team 2 is higher then point for team 2 (low rain)
                            if(round($this->v2/10)>round($this->v1/10)){
                                $this->team2Weather=$weather;
                                $this->team2Weather=0;
                            }
                            // else if market value is equal then point for both team
                            elseif(round($this->v2/10)==round($this->v1/10)){
                                $this->team1Weather=$weather;
                                $this->team2Weather=$weather;
                            }
                            // else it means market value of team 1 is higher then point for team 1
                            else {
                                $this->team1Weather=$weather;
                                $this->team2Weather=0;
                            }
                            break;
                        case ($rain>=1&&$rain<3):
                            $this->cloud="&#x1F326;";// Middle rain
                            $this->cloudText=Language::title('weatherMiddleRain');
                            $weather=1;
                            // if market value of team 2 is higher then points for team 1
                            if(round($this->v2/10)>round($this->v1/10)){
                                $this->team1Weather=$weather;
                                $this->team2Weather=0;
                            }
                            // else if market value is equal then points for both team
                            elseif(round($this->v2/10)==round($this->v1/10)){
                                $this->team1Weather=$weather;
                                $this->team2Weather=$weather;
                            }
                            // else it means market value of team 1 is higher then points for team 2
                            else {
                                $this->team1Weather=0;
                                $this->team2Weather=$weather;
                            }
                            break;
                        case ($rain>=3):
                            $this->cloud="&#x1F327;";//High rain
                            $this->cloudText=Language::title('weatherHighRain');
                            $weather=2;
                            // if market value of team 2 is higher then points for team 1
                            if(round($this->v2/10)>round($this->v1/10)){
                                $this->team1Weather=$weather;
                                $this->team2Weather=0;
                            }
                            // else if market value is equal then points for both team
                            elseif(round($this->v2/10)==round($this->v1/10)){
                                $this->team1Weather=$weather;
                                $this->team2Weather=$weather;
                            }
                            // else it means market value of team 1 is higher then points for team 2
                            else {
                                $this->team1Weather=0;
                                $this->team2Weather=$weather;
                            }
                            break;
                    }
                    $this->team1Weather=intval($this->team1Weather);
                    $this->team2Weather=intval($this->team2Weather);
                }
            }
        }
        $this->setTrend($pdo,$d);
        $this->setHistory($pdo,$d);
    }
    
    public function setHistory($pdo,$d){
        // Predictions history
        $this->historyHome=$this->historyDraw=$this->historyAway=0;
        $r = result('history',$pdo,$d,$this->team1Weather,$this->team2Weather);
        $this->historyHome=criterion("predictionsHistoryHome",$r,$pdo);
        $this->historyDraw=criterion("predictionsHistoryDraw",$r,$pdo);
        $this->historyAway=criterion("predictionsHistoryAway",$r,$pdo);
    }
    
    public function setTrend($pdo,$d){
        // Trend
        $this->trend1= $this->trend2 = 0;
        if($_SESSION['matchdayNum']>3) {
            $trendTeam1 = criterion('trendTeam1', $d, $pdo);
            $trendTeam2 = criterion('trendTeam2', $d, $pdo);
            if($trendTeam1>4 and $trendTeam2<2){
                $this->trend1 = 1;
                $this->trend2 = -1;
            }
        }
    }
    
    public function displayCriteria($d, $form, $manual=false){
        // Display table
        if($d->result=="") echo $form->inputHidden('id_match[]',$d->id_matchgame);
        if(isset($history[0])) echo $history[0];
        
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
        echo "  		  <td>";
        echo "<a href='#' class='tooltip'><big>".Theme::icon('currentForm')."</big>";
        echo "<span>".Language::title('currentFormText')."</span></a>";
        echo " " . Language::title('currentForm');
        echo "</td>";
        if($d->result!="") echo "<td>".$this->currentFormTeam1."</td>\n";
        else echo "  		  <td><input size='1' type='text' name='currentForm1[$this->id]' readonly value='".$this->currentFormTeam1."'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$this->currentFormTeam2."</td>\n";
        else echo "  		  <td><input size='1' type='text' name='currentForm2[$this->id]' readonly value='".$this->currentFormTeam2."'></td>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . Language::title('physicalForm') . "</td>\n";
        if($d->result!="") echo "<td>".$this->physicalC1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='physicalForm1[$this->id]' value='".$this->physicalC1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$this->physicalC2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='physicalForm2[$this->id]' value='".$this->physicalC2."' placeholder='0'></td>\n";
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
            echo "  		  <td><input size='1' type='text' readonly name='weather1[$this->id]' value='".$this->team1Weather."'></td>\n";
        }
        echo "  		  <td></td>\n";
        
        if($d->result!="") echo "<td>".$this->team2Weather."</td>\n";
        else {
            echo "  		  <td><input size='1' type='text' readonly name='weather2[$this->id]' value='".$this->team2Weather."'></td>\n";
        }
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
        echo "<a href='#' class='tooltip'><big>".Theme::icon('team')."</big>";
        echo "<span>".Language::title('marketValueText')."</span></a>";
        echo " " . Language::title('marketValue');
        echo "</td>";
        if($d->result!="") echo "<td>".$this->mv1."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='marketValue1[$this->id]' value='".$this->mv1."'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$this->mv2."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='marketValue2[$this->id]' value='".$this->mv2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>";
        echo "<a href='#' class='tooltip'><big>".Theme::icon('championship')."</big>";
        echo "<span>".Language::title('homeAwayText')."</span></a>";
        echo " " . Language::title('home') . " / " . Language::title('away');
        echo "</td>";
        if($d->result!="") echo "<td>".$this->dom."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='home_away1[$this->id]' value='".$this->dom."'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$this->ext."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='home_away2[$this->id]' value='".$this->ext."'></td>\n";
        echo "          </tr>\n";
        
        echo "          <tr>\n";
        echo "  		  <td>";
        echo "<a href='#' class='tooltip'><big>".Theme::icon('predictionsHistory')."</big>";
        echo "<span>".Language::title('predictionsHistoryText')."</span></a>";
        echo " " . Language::title('predictionsHistory');
        echo "</td>";
        echo "            <td>$this->historyHome</td>\n";
        echo "            <td>$this->historyDraw</td>\n";
        echo "            <td>$this->historyAway</td>\n";
        echo "          </tr>\n";
        
        echo "          <tr>\n";
        echo "  		  <td>";
        echo "<a href='#' class='tooltip'><big>".Theme::icon('trend')."</big>";
        echo "<span>".Language::title('trendText')."</span></a>";
        echo " " . Language::title('trend');
        echo "</td>";
        echo "            <td>$this->trend1</td>\n";
        echo "            <td>0</td>\n";
        echo "            <td>$this->trend2</td>\n";
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
        //$this->win = "";
        $this->id = $d->id_matchgame;
        
        $this->sum1 =
        $this->motivC1
        +$this->currentFormTeam1
        +$this->physicalC1
        +$this->team1Weather
        +$d->bestPlayers1
        +$this->mv1
        +$this->dom
        +$this->historyHome
        +$this->trend1;
        $this->sum2 =
        $this->motivC2
        +$this->currentFormTeam2
        +$this->physicalC2
        +$this->team2Weather
        +$d->bestPlayers2
        +$this->mv2
        +$this->ext
        +$this->historyAway
        +$this->trend2;
        
        $this->sumD = setSumD($this->sum1,$this->sum2,$this->historyDraw);
        
        $this->prediction = setPrediction($this->sum1, $this->sumD, $this->sum2);
    }
}
?>
