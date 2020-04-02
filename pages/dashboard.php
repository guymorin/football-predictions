<?php
/* This is the Football Predictions dashboard section page */
/* Author : Guy Morin */

// Files to include
require("championship_nav.php");

echo "<section>\n";
echo "<h2>$icon_championship $title_championship</h2>\n";
echo "<h3>$title_dashboard</h3>\n";

$graph=array(0=>0);

// Statistics
$req="SELECT m.id_matchgame,
cr.motivation1,cr.motivation2,
cr.currentForm1,cr.currentForm2,
cr.physicalForm1,cr.physicalForm2,
cr.weather1,cr.weather2,
cr.bestPlayers1,cr.bestPlayers2,
cr.marketValue1,cr.marketValue2,
cr.home_away1,cr.home_away2,
c1.name as name1,c2.name as name2,c1.id_team as eq1,c2.id_team as eq2,
m.result, m.date, m.odds1, m.oddsD, m.odds2, j.number 
FROM matchgame m 
LEFT JOIN team c1 ON m.team_1=c1.id_team 
LEFT JOIN team c2 ON m.team_2=c2.id_team 
LEFT JOIN criterion cr ON cr.id_match=m.id_matchgame 
LEFT JOIN matchday j ON j.id_matchday=m.id_matchday 
WHERE m.result<>'';
ORDER BY j.number 
;";
$response = $db->query($req);

// Table of benefits
$table="	 <table class='benef'>\n";
$table.="  		<tr>\n";
$table.="  		  <th>$title_matchday</th>\n";
$table.="  		  <th>$title_bet</th>\n";
$table.="         <th>$title_success</th>\n";
$table.="         <th>$title_oddsAveragePlayed</th>\n";
$table.="         <th>$title_earning</th>\n";
$table.="         <th>$title_profit</th>\n";
$table.="         <th>$title_profit<br />total</th>\n";
$table.="       </tr>\n";

$matchdayBetSum=$matchdaySuccessSum=$matchdayEarningSum;$matchdayPlayedOddsSum;$matchdayProfitSum=0;
while ($data = $response->fetch(PDO::FETCH_OBJ))
{
    $PlayedOdds=0;         

    // Marketvalue
    $mv1 = $data->marketValue1; 
    $mv2 = $data->marketValue2; 
    
    $home = $data->home_away1; 
    $away = $data->home_away2; 
    
    // Predictions history
        $req="SELECT SUM(CASE WHEN m.result = '1' THEN 1 ELSE 0 END) AS Home,
        SUM(CASE WHEN m.result = 'D' THEN 1 ELSE 0 END) AS Draw,
        SUM(CASE WHEN m.result = '2' THEN 1 ELSE 0 END) AS Away
        FROM matchgame m 
        LEFT JOIN criterion cr ON cr.id_match=m.id_matchgame 
        WHERE cr.motivation1='".$data->motivation1."' 
        AND cr.motivation2='".$data->motivation2."' 
        AND cr.currentForm1='".$data->currentForm1."' 
        AND cr.currentForm2='".$data->currentForm2."' 
        AND cr.physicalForm1='".$data->physicalForm1."' 
        AND cr.physicalForm2='".$data->physicalForm2."' 
        AND cr.weather1='".$data->weather1."' 
        AND cr.weather2='".$data->weather2."' 
        AND cr.bestPlayers1='".$data->bestPlayers1."' 
        AND cr.bestPlayers2='".$data->bestPlayers2."' 
        AND cr.marketValue1='".$data->marketValue1."' 
        AND cr.marketValue2='".$data->marketValue2."' 
        AND cr.home_away1='".$data->home_away1."' 
        AND cr.home_away2='".$data->home_away2."' 
        AND m.date<'".$data->date."'";
        $r = $db->query($req)->fetch(PDO::FETCH_OBJ);
        $predictionsHistoryHome=criterion("predictionsHistoryHome",$r,$db);
        $predictionsHistoryAway=criterion("predictionsHistoryAway",$r,$db);
    
    // Sum
    $win="";
    $id=$data->id_matchgame;
    $sum1=
        $data->motivation1
        +$data->currentForm1
        +$data->physicalForm1
        +$data->weather1
        +$data->bestPlayers1
        +$predictionsHistoryHome
        +$mv1
        +$home;
    $sum2=
        $data->motivation2
        +$data->currentForm2
        +$data->physicalForm2
        +$data->weather2
        +$data->bestPlayers2
        +$predictionsHistoryAway
        +$mv2
        +$away;
    if($sum1>$sum2) $prediction="1";
    elseif($sum1==$sum2) $prediction="D";
    elseif($sum1<$sum2) $prediction="2";
    
    $playedOdds=0;
    switch($prediction){
        case "1":
            $PlayedOdds = $data->odds1;
            break;
        case "D":
            $PlayedOdds = $data->oddsD;
            break;
        case "2":
            $PlayedOdds = $data->odds2;
            break;
    }
    
    if($prediction==$data->result){
        $matchdaySuccess++;
        $jGains+=$PlayedOdds;
    }
    $matchdayPlayedOdds+=$PlayedOdds;

    $matchdayMatchs++;

    $matchdayProfit=$jGains-$matchdayMatchs;
    
    if($matchdayMatchs==10){
        
        $matchdayProfitSum+=$matchdayProfit;
        $matchdayBetSum+=$matchdayMatchs;
        $matchdaySuccessSum+=$matchdaySuccess;
        $matchdayEarningSum+=$jGains;
        $matchdayPlayedOddsSum+=$matchdayPlayedOdds;
        $table.="       <tr>\n";
        $table.="           <td><strong>".$data->number."</strong></td>\n";
        $table.="           <td>".$matchdayMatchs."</td>\n";
        $table.="           <td>".$matchdaySuccess."</td>\n";
        $averageOdds=(round($matchdayPlayedOdds/$matchdayMatchs,2));
        $table.="           <td>".$averageOdds."</td>\n";
        $table.="           <td>".(money_format('%i',$jGains))."</td>\n";
        $table.="           <td><span style='color:".valColor($matchdayProfit)."'>";
        if($matchdayProfit>0) $table.="+";
        $table.=(money_format('%i',$matchdayProfit))."</span></td>\n";
        $table.="           <td><span style='color:".valColor($matchdayProfitSum)."'>";
        if($matchdayProfitSum>0) $table.="+";
        $table.=(money_format('%i',$matchdayProfitSum))."</span></td>\n";
        $table.="       </tr>\n";
        
        $matchdayMatchs=$matchdaySuccess=$matchdayPlayedOdds=$jGains=$matchdayProfit=0;
        $graph[$data->number]=$matchdayProfitSum;
    }
}
$response->closeCursor();
$table.="	 </table>\n";

// Values
$roi = round(($matchdayProfitSum/$matchdayBetSum)*100);
$tauxReussite = round(($matchdaySuccessSum/$matchdayBetSum)*100);
$gainParMise = (round($matchdayEarningSum/$matchdayBetSum,2));

echo "<p>\n<table class='stats'>\n";
echo "  <tr>\n";
echo "      <td>$title_bet</td>\n";
echo "      <td>".$matchdayBetSum."</td>\n";

// Profit
echo "      <td>$title_profit</td>\n";
echo "      <td>";
echo "<span style='color:".valColor($matchdayProfitSum)."'>";
if($matchdayProfitSum>0) echo "+";
echo (money_format('%i',$matchdayProfitSum))."&nbsp;&euro;</span></td>\n";

// ROI
echo "      <td>ROI</td>\n";
echo "      <td>";
echo "<span style='color:".valColor($roi)."'>";
if($roi>0) echo "+";
echo $roi."&nbsp;%</span>";
echo "&nbsp;<a href='#' class='tooltip'>&#128172;".valRoi($roi)."</a>";
echo "</td>\n";
echo " </tr>\n";

echo " <tr>\n";
// Success
echo "      <td>$title_success</td>\n";
echo "      <td>$matchdaySuccessSum</td>\n";
// Earning
echo "      <td>$title_earning</td>\n";
echo "      <td>".money_format('%i',$matchdayEarningSum)."&nbsp;&euro;</td>\n";
// Earning by bet
echo "      <td>$title_earningByBet</td>\n";
echo "      <td>$gainParMise</td>\n";
echo " </tr>\n";

echo " <tr>\n";
// Success rate
echo "      <td>$title_successRate</td>\n";
echo "      <td>";
if($matchdayBetSum==0) $tauxReussite= 0;
echo $tauxReussite."&nbsp;%</td>\n";

// Average odds played
$averageOdds=(round($matchdayPlayedOddsSum/$matchdayBetSum,2));
echo "      <td>$title_oddsAveragePlayed</td>\n";
echo "      <td>".$averageOdds;
if(($averageOdds<1.8)||($averageOdds>2.3)){
    echo "&nbsp;<a href='#' class='tooltip'>&#128172;".valOdds($averageOdds)."</a>";
}
echo "</td>\n";
echo "      <td></td>\n";
echo "      <td></td>\n";
echo " </tr>\n";
echo "</table>\n</p>";

echo "<h3>$title_profitEvolution</h3>\n";
?>

<?php
$width=500;
$height=300;
$maxX=array_key_last($graph);
$maxY=end($graph);;
?>
<svg width="<?php echo $width;?>" height="<?php echo $height;?>">
<!-- fond -->
<rect width="100%" height="100%" fill="#dec" stroke="#9c7" stroke-width="4"/>
        
<!-- margin -->
<g class="layer" transform="translate(40,<?php echo ($height/2);?>)">
  
<?php
foreach ($graph as $k => $v){
    $cx=$k*10;
    $cy=-$v*2;
    $color=valColor(-($cy));
    echo "<circle r='2' cx='".$cx."' cy='".$cy."' fill='".$color."' />\n";
    echo "<line x1='".$cxPrec."' y1='".$cyPrec."' x2='".$cx."' y2='".$cy."' stroke='".$color."' stroke-width='1' />\n";
    $cxPrec=$cx;
    $cyPrec=$cy;
}
?>

<!-- Y Axis -->
    <g class="y axis" fill="purple">
      <line x1="<?php echo -($width-10);?>" y1="0" x2="<?php echo ($width-10);?>" y2="0" stroke="#555" stroke-width="1" />
<?php
for($i=-($height/(2*25));$i<($height/(2*25)+1);$i++){
    if($i!=0){
        echo "<text text-anchor='end' x='-6' y='".(($i*20)+4)."' fill='#583'>".-($i*10)."</text>\n";
        echo "<line x1='-2' y1='".($i*20)."' x2='2' y2='".($i*20)."' stroke='#583' stroke-width='2' />\n";
    }
}
?>

<!-- X axis -->
    </g>
    <g class="x axis" fill="purple">
      <line x1="0" y1="<?php echo -($height-10);?>" x2="0" y2="<?php echo ($height-10);?>" stroke="#555" stroke-width="1" />
      <text x="5" y="20" fill="black"><?php echo $title_MD;?>1</text>
      <text x="<?php echo ($maxX*10)+5;?>" y="<?php echo (-($maxY*2)+15);?>" fill="black"><?php echo $title_MD.$maxX;?></text>
    </g>

  </g>
</svg>
    
<?php
        echo "<h3>$title_profitByMatchday</h3>\n";
        echo $table; 
        echo "</section>\n";
?>   
