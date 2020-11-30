<?php
// Predictions matchgame manual include file
use FootballPredictions\Language;
use FootballPredictions\Predictions;
use FootballPredictions\Theme;

echo "<h3>" . (Language::title('prediction')) . "</h3>\n";

// Select data
$data = result('selectCriterion',$pdo);
$counter = $pdo->rowCount();

if($counter > 0){
    
    if($_SESSION['role']==2) echo Predictions::switchButton($form, 'toAuto');

    // Modify form
    echo "<form id='criterion' action='index.php?page=prediction' method='POST' onsubmit='return confirm();'>\n";
    echo $form->inputAction('modify');
    echo $form->inputHidden('manual','1');

    
    // Predictions for the matchday
    foreach ($data as $d)
    {
        $pred = new Predictions();
        $pred->setHistory($pdo, $d);
        $pred->setTrend($pdo, $d);
        $pred->setCriteria($d, $pdo, true);
        $pred->sumCriterion($d);
        $pred->displayCriteria($d, $form, false);   
    }
    
    echo $form->submit(Theme::icon('modify')." ".Language::title('modify'));
    
    echo "</form>\n";
    
} else echo Language::title('noMatch');
?>