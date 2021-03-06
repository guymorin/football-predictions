<?php
/* This is the Football Predictions prediction section page */
/* Author : Guy Morin */

// Files to include
use FootballPredictions\Language;
use FootballPredictions\Predictions;
use FootballPredictions\Theme;

echo "<h2>" . Theme::icon('matchday') . " " 
        . (Language::title('matchday')) . " "
        . $_SESSION['matchdayNum']
        ."</h2>\n";

// Values
$manual = '';
isset($_POST['manual'])     ? $manual=$error->check("Digit",$_POST['manual']) : null;
$isValidate = 0;
isset($_POST['isValidate']) ? $isValidate=$error->check("Digit",$_POST['isValidate']) : null;
if($manual=='') isset($_GET['manual'])      ? $manual=$error->check("Digit",$_GET['manual']) : null;

// Modified popup
if($modify==1){
    $rMatch="";
    isset($_POST['id_match'])       ? $idMatch = array_filter($_POST['id_match']) : null;
    isset($_POST['motivation1'])    ? $moMatch1 = array_filter($_POST['motivation1']) : null;
    isset($_POST['motivation2'])    ? $moMatch2 = array_filter($_POST['motivation2']) : null;
    isset($_POST['currentForm1'])   ? $seMatch1 = array_filter($_POST['currentForm1']) : null;
    isset($_POST['currentForm2'])   ? $seMatch2 = array_filter($_POST['currentForm2']) : null;
    isset($_POST['physicalForm1'])  ? $foMatch1 = array_filter($_POST['physicalForm1']) : null;
    isset($_POST['physicalForm2'])  ? $foMatch2 = array_filter($_POST['physicalForm2']) : null;
    isset($_POST['weather1'])       ? $meMatch1 = array_filter($_POST['weather1']) : null;
    isset($_POST['weather2'])       ? $meMatch2 = array_filter($_POST['weather2']) : null;
    isset($_POST['bestPlayers1'])   ? $joMatch1 = array_filter($_POST['bestPlayers1']) : null;
    isset($_POST['bestPlayers2'])   ? $joMatch2 = array_filter($_POST['bestPlayers2']) : null;
    isset($_POST['marketValue1'])   ? $vaMatch1 = array_filter($_POST['marketValue1']) : null;
    isset($_POST['marketValue2'])   ? $vaMatch2 = array_filter($_POST['marketValue2']) : null;
    isset($_POST['home_away1'])     ? $doMatch1 = array_filter($_POST['home_away1']) : null;
    isset($_POST['home_away2'])     ? $doMatch2 = array_filter($_POST['home_away2']) : null;
    isset($_POST['trend1'])         ? $trMatch1 = array_filter($_POST['trend1']) : null;
    isset($_POST['trend2'])         ? $trMatch2 = array_filter($_POST['trend2']) : null;
    isset($_POST['histo1'])         ? $hiMatch1 = array_filter($_POST['histo1']) : null;
    isset($_POST['histoD'])         ? $hiMatchD = array_filter($_POST['histoD']) : null;
    isset($_POST['histo2'])         ? $hiMatch2 = array_filter($_POST['histo2']) : null;
    
    $pdo->alterAuto('criterion');
    $req="";
    
    if(!empty($idMatch)){
        
        foreach($idMatch as $k){
            
            $data = $pdo->prepare("SELECT COUNT(*) as nb FROM criterion 
            WHERE id_matchgame=:id_matchgame;",[
                'id_matchgame' => $k
            ]);
            
            if($data->nb == 0){
                $req.="INSERT INTO criterion VALUES(NULL,'".$k."','";
                isset($moMatch1[$k]) ? $req.=$moMatch1[$k] : $req.=0;
                $req.="','";
                isset($seMatch1[$k]) ? $req.=$seMatch1[$k] : $req.=0;
                $req.="','";
                isset($foMatch1[$k]) ? $req.=$foMatch1[$k] : $req.=0;
                $req.="','";
                isset($meMatch1[$k]) ? $req.=$meMatch1[$k] : $req.=0;
                $req.="','";
                isset($joMatch1[$k]) ? $req.=$joMatch1[$k] : $req.=0;
                $req.="','";
                isset($vaMatch1[$k]) ? $req.=$vaMatch1[$k] : $req.=0;
                $req.="','";
                isset($doMatch1[$k]) ? $req.=$doMatch1[$k] : $req.=0;
                $req.="','";
                isset($moMatch2[$k]) ? $req.=$moMatch2[$k] : $req.=0;
                $req.="','";
                isset($seMatch2[$k]) ? $req.=$seMatch2[$k] : $req.=0;
                $req.="','";
                isset($foMatch2[$k]) ? $req.=$foMatch2[$k] : $req.=0;
                $req.="','";
                isset($meMatch2[$k]) ? $req.=$meMatch2[$k] : $req.=0;
                $req.="','";
                isset($joMatch2[$k]) ? $req.=$joMatch2[$k] : $req.=0;
                $req.="','";
                isset($vaMatch2[$k]) ? $req.=$vaMatch2[$k] : $req.=0;
                $req.="','";
                isset($doMatch2[$k]) ? $req.=$doMatch2[$k] : $req.=0;
                $req.="','";
                isset($trMatch1[$k]) ? $req.=$trMatch1[$k] : $req.=0;
                $req.="','";
                isset($trMatch2[$k]) ? $req.=$trMatch2[$k] : $req.=0;
                $req.="','";
                isset($hiMatch1[$k]) ? $req.=$hiMatch1[$k] : $req.=0;
                $req.="','";
                isset($hiMatchD[$k]) ? $req.=$hiMatchD[$k] : $req.=0;
                $req.="','";
                isset($hiMatch2[$k]) ? $req.=$hiMatch2[$k] : $req.=0;
                $req.="','";
                if($isValidate>0) $req.=1;
                else $req.=0;
                $req.="');";
            }
            if($data->nb == 1){
                $req.="UPDATE criterion SET ";
                $req.="motivation1='";
                isset($moMatch1[$k]) ? $req.=$moMatch1[$k] : $req.=0;
                $req.="',";
                $req.="currentForm1='";
                isset($seMatch1[$k]) ? $req.=$seMatch1[$k] : $req.=0;
                $req.="',";
                $req.="physicalForm1='";
                isset($foMatch1[$k]) ? $req.=$foMatch1[$k] : $req.=0;
                $req.="',";
                $req.="weather1='";
                isset($meMatch1[$k]) ? $req.=$meMatch1[$k] : $req.=0;
                $req.="',";
                $req.="bestPlayers1='";
                isset($joMatch1[$k]) ? $req.=$joMatch1[$k] : $req.=0;
                $req.="',";
                $req.="marketValue1='";
                isset($vaMatch1[$k]) ? $req.=$vaMatch1[$k] : $req.=0;
                $req.="',";
                $req.="home_away1='";
                isset($doMatch1[$k]) ? $req.=$doMatch1[$k] : $req.=0;
                $req.="',";
                $req.="motivation2='";
                isset($moMatch2[$k]) ? $req.=$moMatch2[$k] : $req.=0;
                $req.="',";
                $req.="currentForm2='";
                isset($seMatch2[$k]) ? $req.=$seMatch2[$k] : $req.=0;
                $req.="',";
                $req.="physicalForm2='";
                isset($foMatch2[$k]) ? $req.=$foMatch2[$k] : $req.=0;
                $req.="',";
                $req.="weather2='";
                isset($meMatch2[$k]) ? $req.=$meMatch2[$k] : $req.=0;
                $req.="',";
                $req.="bestPlayers2='";
                isset($joMatch2[$k]) ? $req.=$joMatch2[$k] : $req.=0;
                $req.="',";
                $req.="marketValue2='";
                isset($vaMatch2[$k]) ? $req.=$vaMatch2[$k] : $req.=0;
                $req.="',";
                $req.="home_away2='";
                isset($doMatch2[$k]) ? $req.=$doMatch2[$k] : $req.=0;
                $req.="',";
                $req.="trend1='";
                isset($trMatch1[$k]) ? $req.=$trMatch1[$k] : $req.=0;
                $req.="',";
                $req.="trend2='";
                isset($trMatch2[$k]) ? $req.=$trMatch2[$k] : $req.=0;
                $req.="',";
                $req.="histo1='";
                isset($hiMatch1[$k]) ? $req.=$hiMatch1[$k] : $req.=0;
                $req.="',";
                $req.="histoD='";
                isset($hiMatchD[$k]) ? $req.=$hiMatchD[$k] : $req.=0;
                $req.="',";
                $req.="histo2='";
                isset($hiMatch2[$k]) ? $req.=$hiMatch2[$k] : $req.=0;
                $req.="',";
                $req.="isValidate='";
                if($isValidate>0) $req.=1;
                else $req.=0;
                $req.="' WHERE id_matchgame='".$k."';";
            }  
        }
    }
    $pdo->exec($req);
    popup(Language::title('modifyPredictions'),"index.php?page=prediction&manual=".$manual);

}
// Default page or manual page
else {
    echo "<h3>" . (Language::title('prediction')) . "</h3>\n";
    
    echo changeMD($pdo,"prediction");
    echo "<div id='response'></div>\n";
    echo "<div id='uform_response'>\n";
    
    // Select data
    $data = result('selectCriterion',$pdo);
    $counter = $pdo->rowCount();
    if($counter > 0){
        
        
        if(($_SESSION['role']==2)and($data[0]->isValidate==0)) {
            $switchText = 'toManual';
            if($manual==1) $switchText = 'toAuto';          
            echo Predictions::switchButton($form, $switchText);
        }

        // Modify form
        echo "<form id='criterion' action='index.php?page=prediction' method='POST'>\n";
        echo $form->inputAction('modify');

        if($manual==1)  echo $form->inputHidden('manual','1');
        else            {
            echo $form->inputHidden('isValidate','1');
            Predictions::teamsBonusMalus($pdo);
        }
        
        // Predictions for the matchday
        foreach ($data as $d)
        {
            $pred = new Predictions();
            $isResult = true;
            $isManual = false;
            if(($manual==1)or($d->isValidate==1))   $isManual=true;
            elseif($d->result=="")                  $isResult=false;
            
            $pred->setCriteria($d, $pdo, $isResult, $isManual);
            $pred->sumCriterion($d);
            echo $pred->displayCriteria($d, $form, $isManual);
        }
        
        echo "<fieldset style='display: block;width: 0; margin: 0 auto;'>\n";
        echo $form->submit(Theme::icon('floppyDisk')." ".Language::title('save'));
        echo "</fieldset>\n";
        
        echo "</form>\n";
        
    } else echo Language::title('noMatch');
    echo "</div>\n";
    
}

?>
<script src="jquery/jquery_3-5-1.min.js"></script>
<script>
$(document).ready(function(){

	var isSubmit = 1;
	var formData = null;
	var valData = 0;
		            	
	$("input").change(function(){
		
		isSubmit = 0;
		valData = $(this).attr('name');
 		valData = valData.match(/\[(.*?)\]/ig); 
		valData = valData[0].replace(/[[\]]/g,'');
		
    	$("#criterion").submit(function(c){
    		if(isSubmit==0){
        		c.preventDefault();
        		var histo1 = 0;
        		var histoD = 0;
        		var histo2 = 0;
        		var sum1 = 0;
        		var sumD = 0;
        		var sum2 = 0;
           		formData = $(this).serialize(); 
                $.ajax({
                    url: 'checkHistoryData.php',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function(data){
                        var data_length = data.length;
                        for (var i = 0; i < data_length; i++) {
                        	if((data[i]["id_matchgame"])==valData){
                        		histo1 = data[i]["histo1"];
                        		histoD = data[i]["histoD"];
                        		histo2 = data[i]["histo2"];
                        		sum1 = data[i]["sum1"];
                        		sumD = data[i]["sumD"];
                        		sum2 = data[i]["sum2"];
                        	}
                        }
                    	$("input[name='histo1["+valData+"]']").val(histo1);
                    	$("input[name='histoD["+valData+"]']").val(histoD);
                    	$("input[name='histo2["+valData+"]']").val(histo2);
                    	document.getElementById("sum1["+valData+"]").innerHTML=sum1;
                    	document.getElementById("sumD["+valData+"]").innerHTML=sumD;
                    	document.getElementById("sum2["+valData+"]").innerHTML=sum2;
                    }
                });
    		}  		            
    	});
   		$("#criterion").submit();	
		isSubmit = 1;
	});

 });
</script>

