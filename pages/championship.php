<?php
/* This is the Football Predictions championship section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\App;
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Section\Championship;

?>

<h2><?= Theme::icon('championship') . ' ' . (Language::title('championship'));?></h2>

<?php
// Values

$championshipId = $standaway = $standhome = 0;
$championshipName = $selectTeams = '';
isset($_POST['id_championship'])    ? $championshipId=$error->check("Digit",$_POST['id_championship']) : null;
isset($_POST['name'])		        ? $championshipName=$error->check("Alnum",$_POST['name'], Language::title('name')) : null;
isset($_GET['standhome'])           ? $standhome=$error->check("Action",$_GET['standhome']) : null;
isset($_GET['standaway'])           ? $standaway=$error->check("Action",$_GET['standaway']) : null;
isset($_POST['teams'])              ? $selectTeams = $_POST['teams'] : null;
// First, select a championship
if(
    (empty($_SESSION['championshipId']))
    &&($create == 0)
    &&($modify == 0)
    &&($delete == 0)
    ){
    echo Championship::selectChampionship($pdo, $form);
}
// Create
elseif($create == 1){
    if ($_SESSION['noTeam'] == true
        && isset($_SESSION['seasonName'])
        && isset($_SESSION['championshipName'])
        ) {
        echo "<h3>" . (Language::title('selectTheTeams')) . "</h3>\n";
        echo "<h4>" . $_SESSION['championshipName'] . " - "
                    . $_SESSION['seasonName']. "</h4>\n";
        if($selectTeams != '') Championship::selectMultiPopup($pdo, $selectTeams);
        echo Championship::selectMultiForm($pdo, $error, $form);          
    } else {
        echo "<h3>" . (Language::title('createAChampionship')) . "</h3>\n";
        if($pdo->findName('championship', $championshipName))  $error->setError(Language::title('errorExists'));
        elseif($championshipName != '') Championship::createPopup($pdo, $championshipName);
        echo Championship::createForm($pdo, $error, $form);
    }

}
// Delete / Modify
elseif($delete == 1  || $delete == 2 || $modify == 1){
    App::exitNoAdmin();
    echo "<h3>" . (Language::title('modifyAChampionship')) . "</h3>\n";
    echo Championship::modifyForm($pdo, $error, $form, $championshipId);    
    if($delete == 1) echo $form->popupConfirm('championship', 'id_championship', $championshipId);
    elseif($delete == 2){
        if($championshipId==0) popup(Language::title('error'),"index.php?page=championship");
        else Championship::deletePopup($pdo, $championshipId);
    }    
    elseif($modify == 1){
        if($championshipId==0) popup(Language::title('error'),"index.php?page=championship");
        elseif($championshipName != '') Championship::modifyPopup($pdo, $championshipName, $championshipId);
    }
}
// List (standing)
elseif(isset($_SESSION['championshipId'])&&($exit==0)){
    echo "<h3>".$_SESSION['championshipName']." : " . (Language::title('standing')) . "</h3>\n";
    echo Championship::list($pdo, $standhome, $standaway);
}
?>

