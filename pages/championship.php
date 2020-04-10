<?php
/* This is the Football Predictions championship section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Section\Championship;

?>

<h2><?= "$icon_championship $title_championship";?></h2>

<?php
// Values

$championshipId=$create=$modify=$delete=$standaway=$standhome=0;
$championshipName="";
isset($_POST['id_championship'])    ? $championshipId=$error->check("Digit",$_POST['id_championship']) : null;
isset($_POST['name'])		        ? $championshipName=$error->check("Alnum",$_POST['name'],$error) : null;

isset($_GET['create'])              ? $create=$error->check("Action",$_GET['create']) : null;
$create==0&&isset($_POST['create']) ? $create=$error->check("Action",$_POST['create']) : null;
isset($_POST['modify'])             ? $modify=$error->check("Action",$_POST['modify']) : null;
isset($_POST['delete'])             ? $delete=$error->check("Action",$_POST['delete']) : null;
isset($_GET['standhome'])           ? $standhome=$error->check("Action",$_GET['standhome']) : null;
isset($_GET['standaway'])           ? $standaway=$error->check("Action",$_GET['standaway']) : null;

// First, select a championship
if(
    (empty($_SESSION['championshipId']))
    &&($create == 0)
    &&($modify == 0)
    &&($delete == 0)
    ){
    echo Championship::selectChampionship($pdo, $form, $icon_quicknav);
}

// Delete
elseif($delete == 1){
    if($championshipId==0) popup($title_error,"index.php?page=championship");
    else Championship::deletePopup($pdo, $championshipId);
}
// Create
elseif($create == 1){
    echo "<h3>$title_createAChampionship</h3>\n";
    if($pdo->findName('championship', $championshipName))  $error->setError($title_errorExists);
    elseif($championshipName!="") Championship::createPopup($pdo, $championshipName);
    echo Championship::createForm($pdo, $error, $form);
}
// Modify
elseif($modify == 1){
    echo "<h3>$title_modifyAChampionship</h3>\n";
    if($championshipId==0) popup($title_error,"index.php?page=championship");
    elseif($championshipName!="") Championship::modifyPopup($pdo, $championshipName, $championshipId);
    echo Championship::modifyForm($pdo, $error, $form, $championshipId);
}
// List (standing)
elseif(isset($_SESSION['championshipId'])&&($exit==0)){
    echo "<h3>".$_SESSION['championshipName']." : $title_standing</h3>\n";
    echo Championship::list($pdo, $standhome, $standaway);
}
?>

