<?php session_start();?>
<?php
require 'vendor/autoload.php';

// Namespaces
use FootballPredictions\Predictions;
use FootballPredictions\App;
use FootballPredictions\Forms;
use FootballPredictions\Language;
use FootballPredictions\Theme;

// Language
if(empty($_SESSION['language'])) Language::getBrowserLang();
require 'lang/localization.php';

// File to include
require 'include/changeMD.php';
require 'include/criterion.php';
require 'include/functions.php';

// PHP classes
$pdo = App::getDb();

$val = "";

// Select data
if(isset($_POST)){
    $formData = $_POST;
    $form = new Forms($formData);
    // Convert POST values
    $idMatch = $motivation1 = $motivation2 
    = $physicalForm1 = $physicalForm2 
    = $bestPlayers1 = $bestPlayers2 
    = $weather1 = $weather2 
    = $marketValue1 = $marketValue2 
    = $home_away1 = $home_away2 
    = $currentForm1 = $currentForm2 
    = $trend1 = $trend2 
    = $histo1 = $histoD = $histo2 
    = array();
    foreach($formData as $k => $v){
        if($k=='manual')         $manual=$v;
        if($k=='isValidate')     $isValidate=$v;
        if($k=='id_match')       $idMatch=$v;
        if($k=='motivation1')    $motivation1=$v;
        if($k=='motivation2')    $motivation2=$v;
        if($k=='physicalForm1')  $physicalForm1=$v;
        if($k=='physicalForm2')  $physicalForm2=$v;
        if($k=='bestPlayers1')   $bestPlayers1=$v;
        if($k=='bestPlayers2')   $bestPlayers2=$v;
        if($k=='weather1')       $weather1=$v;
        if($k=='weather2')       $weather2=$v;
        if($k=='marketValue1')   $marketValue1=$v;
        if($k=='marketValue2')   $marketValue2=$v;
        if($k=='home_away1')     $home_away1=$v;
        if($k=='home_away2')     $home_away2=$v;
        if($k=='currentForm1')   $currentForm1=$v;
        if($k=='currentForm2')   $currentForm2=$v;
        if($k=='trend1')         $trend1=$v;
        if($k=='trend2')         $trend2=$v;
        if($k=='histo1')         $histo1=$v;
        if($k=='histoD')         $histoD=$v;
        if($k=='histo2')         $histo2=$v;
    }
    
    // Convert values in data
    $dataArray = array();
    foreach($idMatch as $i){
        $name1 = $name2 = $eq1 = $eq2 = $weather_code = $date = $number = '';
        $req ="SELECT t1.name as name1, t2.name as name2, 
            mg.team_1 as eq1, mg.team_2 as eq2, 
            t1.weather_code, mg.date, md.number as number 
            FROM matchgame mg 
            LEFT JOIN team t1 ON t1.id_team = mg.team_1 
            LEFT JOIN team t2 ON t2.id_team = mg.team_2 
            LEFT JOIN matchday md ON md.id_matchday = mg.id_matchday 
            WHERE id_matchgame=:matchgame ;";
        $dataVal = $pdo->prepare($req,[
            'matchgame' => $i
        ],true);
        foreach($dataVal as $dv){
            $name1 = $dv->name1;
            $name2 = $dv->name2;
            $eq1 = $dv->eq1;
            $eq2 = $dv->eq2;
            $weather_code = $dv->weather_code;
            $date = $dv->date;
            $number = $dv->number;
        }
        
        array_push($dataArray,array(
            'id_matchgame'  => $i,
            'motivation1'   => $motivation1[$i],
            'motivation2'   => $motivation2[$i],
            'currentForm1'  => $currentForm1[$i],
            'currentForm2'  => $currentForm2[$i],
            'physicalForm1' => $physicalForm1[$i],
            'physicalForm2' => $physicalForm2[$i],
            'weather1'      => $weather1[$i],
            'weather2'      => $weather2[$i],
            'bestPlayers1'  => $bestPlayers1[$i],
            'bestPlayers2'  => $bestPlayers2[$i],
            'marketValue1'  => $marketValue1[$i],
            'marketValue2'  => $marketValue2[$i],
            'home_away1'    => $home_away1[$i],
            'home_away2'    => $home_away2[$i],
            'trend1'        => $trend1[$i],
            'trend2'        => $trend2[$i],
            'histo1'        => $histo1[$i],
            'histoD'        => $histoD[$i],
            'histo2'        => $histo2[$i],
            'isValidate'    => $isValidate,
            'name1'         => $name1,
            'name2'         => $name2,
            'eq1'           => $eq1,
            'eq2'           => $eq2,
            'weather_code'  => $weather_code,
            'result'        => NULL,
            'date'          => $date,
            'number'        => $number,
            'sum1'          => 0,
            'sumD'          => 0,
            'sum2'          => 0
        ));
    }
    
    // Convert array in object
    $data=json_decode(json_encode($dataArray));
    
    // Update history criterion
    $dataArray = array();
    foreach($data as $d){
        $pred = new Predictions();
        $r = result('history',$pdo,$d,$d->weather1,$d->weather2);
        $d->histo1=criterion("predictionsHistoryHome",$r,$pdo);
        $d->histoD=criterion("predictionsHistoryDraw",$r,$pdo);
        $d->histo2=criterion("predictionsHistoryAway",$r,$pdo);
        array_push($dataArray,$d);
        $pred->setCriteria($d, $pdo, true);
        $pred->sumCriterion($d);
        $d->sum1 = $pred->sum1;
        $d->sumD = $pred->sumD;
        $d->sum2 = $pred->sum2;
    }
    
    
    
    // Convert array in object with history update
    $data=json_encode($dataArray);
    
}
echo $data;
die;
?>