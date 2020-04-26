<?php
/**
 *
 * Class Statistics
 * Generate statistics elements
 */
namespace FootballPredictions;
use \PDO;

class Statistics
{
    private $averageOdds;
    private $away;
    private $betSum;
    private $earning;
    private $earningByBet;
    private $earningSum;
    private $graph;
    private $home;
    private $matchs;
    private $mv1;
    private $mv2;
    private $nbMatchdays;
    private $playedOdds;
    private $playedOddsSum;
    private $prediction;
    private $predictionsHistoryAway;
    private $predictionsHistoryHome;
    private $profit;
    private $profitSum;
    private $roi;
    private $success;
    private $successRate;
    private $successSum;
    private $sum1;
    private $sum2;
    private $table;
    private $totalPlayed;
    private $v1;
    private $v2;
    private $win;
    
    /**
     *
     * @param array $data Form or database data
     */
    public function __construct(){
        $this->matchs = $this->success = $this->earningSum = $this->totalPlayed = 0;
    }
    
    public function getStats($pdo, $page){
        $val = '';
        $req="SELECT m.id_matchgame,
        cr.motivation1,cr.motivation2,
        cr.currentForm1,cr.currentForm2,
        cr.physicalForm1,cr.physicalForm2,
        cr.weather1,cr.weather2,
        cr.bestPlayers1,cr.bestPlayers2,
        cr.marketValue1,cr.marketValue2,
        cr.home_away1,cr.home_away2,
        c1.name as name1,c2.name as name2,c1.id_team as eq1,c2.id_team as eq2,
        m.result, m.date, m.odds1, m.oddsD, m.odds2";
        if($page == 'dashboard') $req .= ", j.number";
        $req .= " FROM matchgame m
        LEFT JOIN team c1 ON m.team_1=c1.id_team
        LEFT JOIN team c2 ON m.team_2=c2.id_team
        LEFT JOIN criterion cr ON cr.id_matchgame=m.id_matchgame ";
        if($page == 'dashboard') {
            $req .= " LEFT JOIN matchday j ON j.id_matchday=m.id_matchday 
            WHERE m.result<>'' 
            AND j.id_season = :id_season 
            AND j.id_championship = :id_championship  
            ORDER BY j.number;";
        } elseif($page == 'matchday') {
            $req .= " WHERE m.id_matchday=:id_matchday ORDER BY m.date;";
        }

        if($page == 'dashboard') {
            $data = $pdo->prepare($req,[
                'id_season' => $_SESSION['seasonId'],
                'id_championship' => $_SESSION['championshipId'],                
            ],true);
        } elseif($page == 'matchday') {
            $data = $pdo->prepare($req,[
                'id_matchday' => $_SESSION['matchdayId']
            ],true);
        }
        $counter = $pdo->rowCount();
        if($counter > 0){
            $this->prepareTable($pdo, $data, $page);
            $this->summaryPrepare($page);
            $val .= $this->summaryTable($page);
            $val .=  $this->statsTable($page);
        } else $val .=  Language::title('noStatistic');
        return $val;
    }
    
    public function prepareTableDashboard(){
        $val = '';
        $val .= "<p>\n";
        $val .= "	 <table class='benef'>\n";
        $val .= "  		<tr>\n";
        $val .= "  		  <th>" . (Language::title('matchday')) . "</th>\n";
        $val .= "  		  <th>" . (Language::title('bet')) . "</th>\n";
        $val .= "           <th>" . (Language::title('success')) . "</th>\n";
        $val .= "           <th>" . (Language::title('oddsAveragePlayed')) . "</th>\n";
        $val .= "           <th>" . (Language::title('earning')) . "</th>\n";
        $val .= "           <th>" . (Language::title('profit')) . "</th>\n";
        $val .= "           <th>" . (Language::title('profit')) . "<br />total</th>\n";
        $val .= "         </tr>\n";
        return $val;
    }
    public function prepareTableMatchday(){
        $val = '';
        $val.= "<p>\n";
        $val.="<table class='stats'>\n";
        $val.="  		<tr>\n";
        $val.="  		  <th>" . (Language::title('matchgame')) . "</th>\n";
        $val.="           <th>" . (Language::title('prediction')) . "</th>\n";
        $val.="           <th>" . (Language::title('result')) . "</th>\n";
        $val.="           <th>" . (Language::title('odds')) . "</th>\n";
        $val.="           <th>" . (Language::title('success')) . "</th>\n";
        $val.="         </tr>\n";
        return $val;
    }
    
    public function prepareTable($pdo, $data, $page){
        $val = '';
        if($page == 'dashboard') {
            $val .= "<h3>" . (Language::title('profitByMatchday')) . "</h3>\n";
            $val.= $this->prepareTableDashboard();
        }
        elseif($page == 'matchday') $val.= $this->prepareTableMatchday();
        
        foreach ($data as $d)
        { 
            // Marketvalue
            if($page == 'dashboard'){
                $this->mv1 = $d->marketValue1;
                $this->mv2 = $d->marketValue2;
            } elseif($page == 'matchday') {
                $this->v1=criterion("v1",$d,$pdo);
                $this->v2=criterion("v2",$d,$pdo);
                $this->mv1 = round(sqrt($this->v1/$this->v2));
                $this->mv2 = round(sqrt($this->v2/$this->v1));
            }
            // Home Away
            $this->home = $d->home_away1;
            $this->away = $d->home_away2;
            
            // Predictions history
            $req="SELECT SUM(CASE WHEN m.result = '1' THEN 1 ELSE 0 END) AS Home,
                    SUM(CASE WHEN m.result = 'D' THEN 1 ELSE 0 END) AS Draw,
                    SUM(CASE WHEN m.result = '2' THEN 1 ELSE 0 END) AS Away
                    FROM matchgame m
                    LEFT JOIN criterion cr ON cr.id_matchgame=m.id_matchgame
                    WHERE cr.motivation1='".$d->motivation1."'
                    AND cr.motivation2='".$d->motivation2."'
                    AND cr.currentForm1='".$d->currentForm1."'
                    AND cr.currentForm2='".$d->currentForm2."'
                    AND cr.physicalForm1='".$d->physicalForm1."'
                    AND cr.physicalForm2='".$d->physicalForm2."'
                    AND cr.weather1='".$d->weather1."'
                    AND cr.weather2='".$d->weather2."'
                    AND cr.bestPlayers1='".$d->bestPlayers1."'
                    AND cr.bestPlayers2='".$d->bestPlayers2."'
                    AND cr.marketValue1='".$d->marketValue1."'
                    AND cr.marketValue2='".$d->marketValue2."'
                    AND cr.home_away1='".$d->home_away1."'
                    AND cr.home_away2='".$d->home_away2."'
                    AND m.date<'".$d->date."'";
            $r = $pdo->prepare($req,null,true);
            $this->predictionsHistoryHome = criterion("predictionsHistoryHome",$r,$pdo);
            $this->predictionsHistoryAway = criterion("predictionsHistoryAway",$r,$pdo);
            
            // Sum
            $this->win = 0;
            
            $this->sum1=
            $d->motivation1
            +$d->currentForm1
            +$d->physicalForm1
            +$d->weather1
            +$d->bestPlayers1
            +$this->mv1
            +$this->home
            +$this->predictionsHistoryHome;
            $this->sum2=
            $d->motivation2
            +$d->currentForm2
            +$d->physicalForm2
            +$d->weather2
            +$d->bestPlayers2
            +$this->mv2
            +$this->away
            +$this->predictionsHistoryAway;
            if($this->sum1 > $this->sum2)         $this->prediction = '1';
            elseif($this->sum1 == $this->sum2)    $this->prediction = Language::title('draw');
            elseif($this->sum1 < $this->sum2)     $this->prediction = '2';
            
            $this->playedOdds=0;
            switch($this->prediction){
                case "1":
                    $this->playedOdds = $d->odds1;
                    break;
                case (Language::title('draw')):
                    $this->playedOdds = $d->oddsD;
                    break;
                case "2":
                    $this->playedOdds = $d->odds2;
                    break;
            }
            if($this->prediction == $d->result){
                $this->win = Theme::icon('winOK');
                $this->success++;
                if($page == 'dashboard') $this->earning += $this->playedOdds;
                elseif($page == 'matchday') $this->earningSum += $this->playedOdds;
            } elseif ($d->result != "") $this->win = Theme::icon('winKO');
            
            $this->totalPlayed += $this->playedOdds;
            $this->matchs++;
            $this->profit = $this->earning - $this->matchs;
            
            if($page == 'dashboard' && $this->matchs == 10){
                $this->profitSum += $this->profit;
                $this->betSum += $this->matchs;
                $this->successSum += $this->success;
                $this->earningSum += $this->earning;
                $this->playedOddsSum += $this->totalPlayed;
                $this->nbMatchdays = $d->number;
                $val .= "       <tr>\n";
                $val .= "           <td><strong>" . $d->number . "</strong></td>";
                $val .= "           <td>" . $this->matchs. " </td>\n";
                $val .= "           <td>" . $this->success. " </td>\n";
                $this->averageOdds = (round($this->totalPlayed / $this->matchs,2));
                $val .= "           <td>" . $this->averageOdds. " </td>\n";
                $val .= "           <td>" . (money_format('%i',$this->earning)). " </td>\n";
                $val .= "           <td><span style='color:" . valColor($this->profit). " '>";
                if($this->profit>0) $val.="+";
                $val .= (money_format('%i',$this->profit)). " </span></td>\n";
                $val .= "           <td><span style='color:" . valColor($this->profitSum). " '>";
                if($this->profitSum>0) $val.="+";
                $val .= (money_format('%i',$this->profitSum)). " </span></td>\n";
                $val .= "       </tr>\n";
                
                $this->profit = $this->matchs = $this->success
                 = $this->earning = $this->totalPlayed = 0;
                
                $this->graph[$d->number] = $this->profitSum;
                
            } elseif($page == 'matchday') {
                $val.="  		<tr>\n";
                $val.="  		  <td>".$d->name1." - ".$d->name2."</td>\n";
                $val.="  		  <td>".$this->prediction."</td>\n";
                $val.="  		  <td>";
                if($d->result=='D') $val.=Language::title('draw');
                else $val.=$d->result;
                $val.="</td>\n";
                $val.="  		  <td>".$this->playedOdds."</td>\n";
                $val.="  		  <td>".$this->win."</td>\n";
                $val.="       </tr>\n";
            }
        }
        
        $val.="</table>\n";
        $val .= "</p>\n";
        $this->table = $val;
    }
    
    public function statsTable($page){
        $val = '';
        $val .= $this->table;
        if($page == 'dashboard') $val .= $this->evolution();
        return $val;
    }
    
    public function summaryPrepare($page){
        if($page == 'dashboard'){
            $this->roi = round(($this->profitSum / $this->betSum)*100);
            $this->successRate = round(($this->successSum / $this->betSum)*100);
            $this->earningByBet = (round($this->earningSum / $this->betSum,2));   
        } elseif($page == 'matchday') {
            $this->profit = money_format('%i',$this->earningSum - $this->matchs);
            $this->roi = round(($this->profit / $this->matchs)*100);
            $this->successRate = round(($this->success / $this->matchs)*100);
            $this->earning = money_format('%i',$this->earningSum);
            $this->earningByBet = (round($this->earningSum / $this->matchs,2));
        }

    }
    
    public function summaryTableDashboard(){
        $val = '';
        $val .= "<p>\n";
        $val .= "<table class='stats'>\n";
        
        $val .= "    <tr>\n";
        $val .= "      <td>" . (Language::title('bet')) . "</td>\n";
        $val .= "      <td>". $this->betSum . "</td>\n";
        $val .= "      <td>" . (Language::title('success')) . "</td>\n";
        $val .= "      <td>" . $this->successSum . "</td>\n";
        $val .= "    </tr>\n";
        
        $val .= "    <tr>\n";
        $val .= "      <td>" . (Language::title('earning')) . "</td>\n";
        $val .= "      <td>" . $this->earningSum."&nbsp;&euro;</td>\n";
        $val .= "      <td>" . (Language::title('successRate')) . "</td>\n";
        $val .= "      <td>";
        if($this->matchs > 0) $val .= $this->successRate;
        else $val .= 0;
        $val .= "&nbsp;%</td>\n";
        $val .= "    </tr>\n";
        
        $val .= "    <tr>\n";
        $val .= "      <td>" . (Language::title('earningByBet')) . "</td>\n";
        $val .= "      <td>$this->earningByBet</td>\n";
        $this->averageOdds = (round($this->playedOddsSum / $this->betSum,2));
        $val .= "      <td>" . (Language::title('oddsAveragePlayed'))
        . "</td>\n";
        $val .= "      <td>" . $this->averageOdds;
        if(($this->averageOdds < 1.8)||($this->averageOdds > 2.3)){
            $val .= "&nbsp;<a href='#' class='tooltip'>&#128172;"
                . valOdds($this->averageOdds)."</a>";
        }
        $val .= "</td>\n";
        $val .= "    </tr>\n";
        
        $val .= "    <tr>\n";
        $val .= "      <td>" . (Language::title('profit')) . "</td>\n";
        $val .= "      <td><span style='color:" . valColor($this->profitSum) . "'>";
        if($this->profitSum>0) $val .= "+";
        $val .= $this->profitSum."</span></td>\n";
        $val .= "      <td>" . (Language::title('ROI')) . "</td>\n";
        $val .= "      <td>";
        $val .= "<span style='color:" . valColor($this->roi) . "'>";
        if($this->roi>0) $val .= "+";
        $val .= $this->roi."&nbsp;%</span>";
        $val .= "&nbsp;<a href='#' class='tooltip'>&#128172;".valRoi($this->roi)."</a>";
        $val .= "</td>\n";
        $val .= "    </tr>\n";
        
        $val .= "</table>\n";
        $val .= "</p>\n";
        return $val;
    }
    
    public function summaryTableMatchday(){
        $val = '';
        $val .= "<p>\n";
        $val .= "<table class='stats'>\n";
        
        $val .= "    <tr>\n";
        $val .= "      <td>" . (Language::title('bet')) . "</td>\n";
        $val .= "      <td>". $this->matchs . "</td>\n";
        $val .= "      <td>" . (Language::title('success')) . "</td>\n";
        $val .= "      <td>" . $this->success . "</td>\n";
        $val .= "    </tr>\n";
        
        $val .= "    <tr>\n";
        $val .= "      <td>" . (Language::title('earning')) . "</td>\n";
        $val .= "      <td>" . $this->earning."&nbsp;&euro;</td>\n";
        $val .= "      <td>" . (Language::title('successRate')) . "</td>\n";
        $val .= "      <td>";
        if($this->matchs > 0) $val .= $this->successRate;
        else $val .= 0;
        $val .= "&nbsp;%</td>\n";
        $val .= "    </tr>\n";
        
        $val .= "    <tr>\n";
        $val .= "      <td>" . (Language::title('earningByBet')) . "</td>\n";
        $val .= "      <td>$this->earningByBet</td>\n";
        $this->averageOdds = (round($this->totalPlayed/$this->matchs,2));
        $val .= "      <td>" . (Language::title('oddsAveragePlayed'))
        . "</td>\n";
        $val .= "      <td>" . $this->averageOdds;
        if(($this->averageOdds < 1.8)||($this->averageOdds > 2.3)){
            $val .= "&nbsp;<a href='#' class='tooltip'>&#128172;"
                . valOdds($this->averageOdds)."</a>";
        }
        $val .= "</td>\n";
        $val .= "    </tr>\n";
        
        $val .= "    <tr>\n";
        $val .= "      <td>" . (Language::title('profit')) . "</td>\n";
        $val .= "      <td><span style='color:" . valColor($this->profit) . "'>";
        if($this->profit>0) $val .= "+";
        $val .= $this->profit."</span></td>\n";
        $val .= "      <td>" . (Language::title('ROI')) . "</td>\n";
        $val .= "      <td>";
        $val .= "<span style='color:" . valColor($this->roi) . "'>";
        if($this->roi>0) $val .= "+";
        $val .= $this->roi."&nbsp;%</span>";
        $val .= "&nbsp;<a href='#' class='tooltip'>&#128172;".valRoi($this->roi)."</a>";
        $val .= "</td>\n";
        $val .= "    </tr>\n";
        
        $val .= "</table>\n";
        $val .= "</p>\n";
        return $val;
    }
    
    public function summaryTable($page){
        $val = '';
        if($page == 'dashboard') $val.= $this->summaryTableDashboard();
        elseif($page == 'matchday') $val.= $this->summaryTableMatchday();
        return $val;
    }
    
    public function evolution(){
        $val = '';
        $val .= "<h3>" . (Language::title('profitEvolution')) . "</h3>\n";
        $w = $this->nbMatchdays * 14;
        $w = ceil($w/10)*10;
        $width=$w;
        $h = $this->profitSum * 6;
        $h = ceil($h/10)*10;
        $height=abs($h);
        $maxX=array_key_last($this->graph);
        $maxY=end($this->graph);;
        $val .= "<div class='graph'>\n";
        $val .= "<svg width='$width' height='$height'>\n";
        $val .= "<rect width='100%' height='100%' fill='#fff' stroke='#aaa' stroke-width='2'/>\n";
        // Margin
        $val .= "<g class='layer' transform='translate(40,". ($height/2) . ")'>\n";
        $cxPrec = 0;
        $cyPrec = 0;
        foreach ($this->graph as $k => $v){
            $cx = $k*10;
            $cy = -$v*2;
            $color = valColor(-($cy));
            $val .= "<circle r='2' cx='" . $cx. " ' cy='" . $cy. " ' fill='" . $color. " ' />\n";
            $val .= "<line x1='" . $cxPrec. " ' y1='" . $cyPrec. " ' x2='" . $cx. " ' y2='" . $cy. " ' stroke='" . $color. " ' stroke-width='1' />\n";
            $cxPrec = $cx;
            $cyPrec = $cy;
        }
        // Y Axis
        $val .= "<g class='y axis' fill='purple'>\n";
        $val .= "<line x1='<?= -($width-10);?>' y1='0' x2='" . ($width-10) . "' y2='0' stroke='#555' stroke-width='1' />\n";
        
        for($i=-($height/(2*25));$i<($height/(2*25)+1);$i++){
            if($i!=0){
                $val .= "<text text-anchor='end' x='-6' y='" . (($i*20)+4). "' fill='#222'>" . -($i*10). " </text>\n";
                $val .= "<line x1='-2' y1='" . ($i*20). "' x2='2' y2='" . ($i*20). "' stroke='#555' stroke-width='2' />\n";
            }
        }
        $val .= "</g>\n";
        
        // X axis
        $val .= "<g class='x axis' fill='purple'>\n";
        $val .= "<line x1='0' y1='" . -($height-10) . "' x2='0' y2='" . ($height-10) . "' stroke='#555' stroke-width='1' />\n";
        $val .= "<text x='5' y='20' fill='black'>" . Language::title('MD') . " 1</text>\n";
        $val .= "<text x='" . (($maxX*10)+5) . "' y='" . (-($maxY*2)+15) . "' fill='black'>" . (Language::title('MD')) . $maxX . "</text>\n";
        $val .= "</g>\n";
        
        $val .= "</g>\n";
        $val .= "</svg>\n";
        $val .= "</div>\n";
        return $val;
    }

}
?>