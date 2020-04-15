<?php
/* This is the Football Predictions dashboard section page */
/* Author : Guy Morin */

use FootballPredictions\Language;

echo "<h2>$icon_championship " . (Language::title('championship')) . "</h2>\n";
echo "<h3>" . (Language::title('dashboard')) . "</h3>\n";

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
WHERE m.result<>'' 
ORDER BY j.number;";
$data = $pdo->prepare($req,null,true);

// Table of benefits
$table = "	 <table class='benef'>\n";
$table .= "  		<tr>\n";
$table .= "  		  <th>" . (Language::title('matchday')) . "</th>\n";
$table .= "  		  <th>" . (Language::title('bet')) . "</th>\n";
$table .= "         <th>" . (Language::title('success')) . "</th>\n";
$table .= "         <th>" . (Language::title('oddsAveragePlayed')) . "</th>\n";
$table .= "         <th>" . (Language::title('earning')) . "</th>\n";
$table .= "         <th>" . (Language::title('profit')) . "</th>\n";
$table .= "         <th>" . (Language::title('profit')) . "<br />total</th>\n";
$table .= "       </tr>\n";

$matchdayEarningSum = $matchdayMatchs = $mdEarning = $matchdaySuccess = $matchdayPlayedOdds = 
$matchdayBetSum = $matchdaySuccessSum = $matchdayEarningSum = $matchdayPlayedOddsSum = $matchdayProfitSum = 0;
foreach ($data as $d)
{
    // Marketvalue
    $mv1 = $d->marketValue1; 
    $mv2 = $d->marketValue2; 
    
    $home = $d->home_away1; 
    $away = $d->home_away2; 
    
    // Predictions history
        $req="SELECT SUM(CASE WHEN m.result = '1' THEN 1 ELSE 0 END) AS Home,
        SUM(CASE WHEN m.result = 'D' THEN 1 ELSE 0 END) AS Draw,
        SUM(CASE WHEN m.result = '2' THEN 1 ELSE 0 END) AS Away
        FROM matchgame m 
        LEFT JOIN criterion cr ON cr.id_match=m.id_matchgame 
        WHERE cr.motivation1 = :motivation1 
        AND cr.motivation2 = :motivation2 
        AND cr.currentForm1 = :currentForm1
        AND cr.currentForm2 = :currentForm2
        AND cr.physicalForm1 = :physicalForm1
        AND cr.physicalForm2 = :physicalForm2 
        AND cr.weather1 = :weather1
        AND cr.weather2 = :weather2
        AND cr.bestPlayers1 = :bestPlayers1
        AND cr.bestPlayers2 = :bestPlayers2 
        AND cr.marketValue1 = :marketValue1
        AND cr.marketValue2 = :marketValue2 
        AND cr.home_away1 = :home_away1
        AND cr.home_away2 = :home_away2 
        AND m.date < :mdate";
        $data2 = $pdo->prepare($req,[
            'motivation1' => $d->motivation1,
            'motivation2' => $d->motivation2,
            'currentForm1' => $d->currentForm1,
            'currentForm2' => $d->currentForm2,
            'physicalForm1' => $d->physicalForm1,
            'physicalForm2' => $d->physicalForm2,
            'weather1' => $d->weather1,
            'weather2' => $d->weather2,
            'bestPlayers1' => $d->bestPlayers1,
            'bestPlayers2' => $d->bestPlayers2,
            'marketValue1' => $d->marketValue1,
            'marketValue2' => $d->marketValue2,
            'home_away1' => $d->home_away1,
            'home_away2' => $d->home_away2,
            'mdate' => $d->date
        ]);
        $predictionsHistoryHome=criterion('predictionsHistoryHome',$data2,$pdo);
        $predictionsHistoryAway=criterion('predictionsHistoryAway',$data2,$pdo);
    
    // Sum
    $win=0;
    $id=$d->id_matchgame;
    $sum1=
        $d->motivation1
        +$d->currentForm1
        +$d->physicalForm1
        +$d->weather1
        +$d->bestPlayers1
        +$predictionsHistoryHome
        +$mv1
        +$home;
    $sum2=
        $d->motivation2
        +$d->currentForm2
        +$d->physicalForm2
        +$d->weather2
        +$d->bestPlayers2
        +$predictionsHistoryAway
        +$mv2
        +$away;
    if($sum1>$sum2) $prediction = '1';
    elseif($sum1==$sum2) $prediction = 'D';
    elseif($sum1<$sum2) $prediction = '2';
    
    $playedOdds=0;
    switch($prediction){
        case '1':
            $playedOdds = $d->odds1;
            break;
        case 'D':
            $playedOdds = $d->oddsD;
            break;
        case '2':
            $playedOdds = $d->odds2;
            break;
    }
    
    if($prediction==$d->result){
        $matchdaySuccess++;
        $mdEarning += $playedOdds;
    }
    $matchdayPlayedOdds += $playedOdds;

    $matchdayMatchs++;

    $matchdayProfit = $mdEarning - $matchdayMatchs;

    if($matchdayMatchs==10){
        
        $matchdayProfitSum += $matchdayProfit;
        $matchdayBetSum += $matchdayMatchs;
        $matchdaySuccessSum += $matchdaySuccess;
        $matchdayEarningSum += $mdEarning;
        $matchdayPlayedOddsSum += $matchdayPlayedOdds;

        $table .= "       <tr>\n";
        $table .= "           <td><strong>" . $d->number . "</strong></td>";
        $table .= "           <td>" . $matchdayMatchs. " </td>\n";
        $table .= "           <td>" . $matchdaySuccess. " </td>\n";
        $averageOdds=(round($matchdayPlayedOdds/$matchdayMatchs,2));
        $table .= "           <td>" . $averageOdds. " </td>\n";
        $table .= "           <td>" . (money_format('%i',$mdEarning)). " </td>\n";
        $table .= "           <td><span style='color:" . valColor($matchdayProfit). " '>";
        if($matchdayProfit>0) $table.="+";
        $table .= (money_format('%i',$matchdayProfit)). " </span></td>\n";
        $table .= "           <td><span style='color:" . valColor($matchdayProfitSum). " '>";
        if($matchdayProfitSum>0) $table.="+";
        $table .= (money_format('%i',$matchdayProfitSum)). " </span></td>\n";
        $table .= "       </tr>\n";
        
        $matchdayMatchs = $matchdaySuccess = $matchdayPlayedOdds = $mdEarning = $matchdayProfit = 0;
        $graph[$d->number] = $matchdayProfitSum;
    }
}
$table .= "	 </table>\n";

// Values
$roi = round(($matchdayProfitSum/$matchdayBetSum)*100);
$tauxReussite = round(($matchdaySuccessSum/$matchdayBetSum)*100);
$gainParMise = (round($matchdayEarningSum/$matchdayBetSum,2));

echo "<p>\n<table class='stats'>\n";
echo "  <tr>\n";
echo "      <td>" . (Language::title('bet')) . "</td>\n";
echo "      <td>" . $matchdayBetSum. " </td>\n";

// Profit
echo "      <td>" . (Language::title('profit')) . "</td>\n";
echo "      <td>";
echo "<span style='color:" . valColor($matchdayProfitSum). " '>";
if($matchdayProfitSum>0) echo "+";
echo (money_format('%i',$matchdayProfitSum)). " &nbsp;&euro;</span></td>\n";

// ROI
echo "      <td>ROI</td>\n";
echo "      <td>";
echo "<span style='color:" . valColor($roi). " '>";
if($roi>0) echo "+";
echo $roi. " &nbsp;%</span>";
echo "&nbsp;<a href='#' class='tooltip'>&#128172;" . valRoi($roi). " </a>";
echo "</td>\n";
echo " </tr>\n";

echo " <tr>\n";
// Success
echo "      <td>" . (Language::title('success')) . "</td>\n";
echo "      <td>$matchdaySuccessSum</td>\n";
// Earning
echo "      <td>" . (Language::title('earning')) . "</td>\n";
echo "      <td>" . money_format('%i',$matchdayEarningSum). " &nbsp;&euro;</td>\n";
// Earning by bet
echo "      <td>" . (Language::title('earningByBet')) . "</td>\n";
echo "      <td>$gainParMise</td>\n";
echo " </tr>\n";

echo " <tr>\n";
// Success rate
echo "      <td>" . (Language::title('successRate')) . "</td>\n";
echo "      <td>";
if($matchdayBetSum==0) $tauxReussite= 0;
echo $tauxReussite. " &nbsp;%</td>\n";

// Average odds played
$averageOdds=(round($matchdayPlayedOddsSum/$matchdayBetSum,2));
echo "      <td>" . (Language::title('oddsAveragePlayed')) . "</td>\n";
echo "      <td>" . $averageOdds;
if(($averageOdds<1.8)||($averageOdds>2.3)){
    echo "&nbsp;<a href='#' class='tooltip'>&#128172;" . valOdds($averageOdds). " </a>";
}
echo "</td>\n";
echo "      <td></td>\n";
echo "      <td></td>\n";
echo " </tr>\n";
echo "</table>\n</p>";

echo "<h3>" . (Language::title('profitEvolution')) . "</h3>\n";
?>

<?php
$width=500;
$height=300;
$maxX=array_key_last($graph);
$maxY=end($graph);;
?>
<svg width="<?= $width;?>" height="<?= $height;?>">
<!-- fond -->
<rect width="100%" height="100%" fill="#fff" stroke="#666" stroke-width="4"/>
        
<!-- margin -->
<g class="layer" transform="translate(40,<?= ($height/2);?>)">
  
<?php
$cxPrec = 0;
$cyPrec = 0;
foreach ($graph as $k => $v){
    $cx = $k*10;
    $cy = -$v*2;
    $color = valColor(-($cy));
    echo "<circle r='2' cx='" . $cx. " ' cy='" . $cy. " ' fill='" . $color. " ' />\n";
    echo "<line x1='" . $cxPrec. " ' y1='" . $cyPrec. " ' x2='" . $cx. " ' y2='" . $cy. " ' stroke='" . $color. " ' stroke-width='1' />\n";
    $cxPrec = $cx;
    $cyPrec = $cy;
}
?>

<!-- Y Axis -->
    <g class="y axis" fill="purple">
      <line x1="<?= -($width-10);?>" y1="0" x2="<?= ($width-10);?>" y2="0" stroke="#555" stroke-width="1" />
<?php
for($i=-($height/(2*25));$i<($height/(2*25)+1);$i++){
    if($i!=0){
        echo "<text text-anchor='end' x='-6' y='" . (($i*20)+4). "' fill='#555'>" . -($i*10). " </text>\n";
        echo "<line x1='-2' y1='" . ($i*20). "' x2='2' y2='" . ($i*20). "' stroke='#555' stroke-width='2' />\n";
    }
}
?>

<!-- X axis -->
    </g>
    <g class="x axis" fill="purple">
      <line x1="0" y1="<?= -($height-10);?>" x2="0" y2="<?= ($height-10);?>" stroke="#555" stroke-width="1" />
      <text x="5" y="20" fill="black"><?= Language::title('MD');?>1</text>
      <text x="<?= ($maxX*10)+5;?>" y="<?= (-($maxY*2)+15);?>" fill="black"><?= Language::title('MD').$maxX;?></text>
    </g>

  </g>
</svg>
    
<?php
        echo "<h3>" . (Language::title('profitByMatchday')) . "</h3>\n";
        echo $table;
?>
