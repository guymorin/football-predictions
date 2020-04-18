<?php session_start();?>
<?php 
/* This is the Football Predictions index page */
/* Author : Guy Morin */

// Class
require '../vendor/autoload.php';

// Namespaces
use FootballPredictions\App;
use FootballPredictions\Database;
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Language;
use FootballPredictions\Section\Championship;
use FootballPredictions\Section\Matchday;
use FootballPredictions\Section\Player;
use FootballPredictions\Section\Season;
use FootballPredictions\Section\Team;
use FootballPredictions\Section\Account;

// Language
if(empty($_SESSION['language'])) Language::getBrowserLang();

// Files to include
require '../include/connection.php';
require '../include/functions.php';

$error = new Errors();
$form = new Forms($_POST);

// Popup if needed
    // Season selected
    if(isset($_POST['seasonSelect'])){
        $v=explode(",",$_POST['seasonSelect']);
        $_SESSION['seasonId'] = $error->check("Digit",$v[0]);
        $_SESSION['seasonName'] = $error->check("Alnum",$v[1]);
        $req = "UPDATE fp_user SET last_season = '" . $_SESSION['seasonId'] . "' 
        WHERE id_fp_user = '" . $_SESSION['userId'] . "';";
        $pdo->exec($req);
        header('Location:index.php');
    }
    // Championship selected
    if(isset($_POST['championshipSelect'])){
        $v=explode(",",$_POST['championshipSelect']);
        $_SESSION['championshipId'] = $error->check("Digit",$v[0]);
        $_SESSION['championshipName'] = $error->check("Alnum",$v[1]);
        $req = "UPDATE fp_user SET last_championship = '" . $_SESSION['championshipId'] . "'
        WHERE id_fp_user = '" . $_SESSION['userId'] . "';";
        $pdo->exec($req);
        header('Location:index.php');
    }
    // Matchday selected
    if(isset($_POST['matchdaySelect'])&&(!isset($_SESSION['matchdayId']))){
        $v=explode(",",$_POST['matchdaySelect']);
        $_SESSION['matchdayId'] = $error->check("Digit",$v[0]);
        $_SESSION['matchdayNum'] = $error->check("Digit",$v[1]);
        header('Location:index.php');
    }
// Check the page value
$page="";
if(isset($_GET['page'])) $page=$error->check("Alnum",$_GET['page']);

$create = $modify = $delete = $exit = $logon = $modifyuser = 0;
isset($_GET['create'])          ? $create = $error->check("Action",$_GET['create']) : null;
$create==0 && isset($_POST['create'])  ? $create = $error->check("Action",$_POST['create']) : null;
isset($_GET['modify'])          ? $modify = $error->check("Action",$_GET['modify']) : null;
isset($_POST['modify'])         ? $modify = $error->check("Action",$_POST['modify']) : null;
isset($_POST['delete'])         ? $delete = $error->check("ActionDelete",$_POST['delete']) : null;
if(isset($_GET['exit'])) $exit=$error->check("Action",$_GET['exit']);
isset($_POST['logon'])          ? $logon = $error->check("Action",$_POST['logon']) : null;
isset($_POST['modifyuser'])     ? $modifyuser = $error->check("Action",$_POST['modifyuser']) : null;

// Exit
if($exit==1){
    switch($page){
        case "account":
            unset($_SESSION['userId']);
            unset($_SESSION['userLogin']);
            unset($_SESSION['language']);
            unset($_SESSION['theme']);
            unset($_SESSION['role']);
            unset($_SESSION['seasonId']);
            unset($_SESSION['seasonName']);
            unset($_SESSION['championshipId']);
            unset($_SESSION['championshipName']);
            unset($_SESSION['matchdayId']);
            unset($_SESSION['matchdayNum']);
            break;
        case "season":
            unset($_SESSION['seasonId']);
            unset($_SESSION['seasonName']);
            unset($_SESSION['championshipId']);
            unset($_SESSION['championshipName']);
            unset($_SESSION['matchdayId']);
            unset($_SESSION['matchdayNum']);
            break;
        case "championship":
            unset($_SESSION['championshipId']);
            unset($_SESSION['championshipName']);
            unset($_SESSION['matchdayId']);
            unset($_SESSION['matchdayNum']);
            break;
        case "matchday":
            unset($_SESSION['matchdayId']);
            unset($_SESSION['matchdayNum']);
            break;
    }
    header('Location:index.php');
}

// Choose a season, a championship...
if(empty($_SESSION['userLogin'])) $page="account";
elseif(empty($_SESSION['seasonId'])) $page="season";
elseif(
    (empty($_SESSION['championshipId']))
    &&($page=="")
) $page="championship";


/* Header */

?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
    <?php require("../theme/default/theme.php");?>

</head>

<body>
<script>
window.onscroll = function() {myFunction()};

function myFunction() {
  if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
    document.getElementById('fp-submenu').className = "off";
  } else {
    document.getElementById('fp-submenu').className = "";
  }
}
</script>
<header>
    <nav id='fp-submenu'>
<?php
$current = '';
switch($page){
    case "account":
            if(isset($_SESSION['userLogin'])) {
                $current = 'myAccount';
                echo Account::submenu($pdo, $form, $current);
            }
        break;
    case "championship":
    case "dashboard":
        if($create == 1)                $current = 'create';
        elseif($modify == 1 && $page == 'championship') $current = 'modify';
        elseif($page == 'championship') $current = 'standing';
        elseif($page=='dashboard')      $current = 'dashboard';
        echo Championship::submenu($pdo, $form, $current);
        break;
    case "matchday":
    case "match":
    case "prediction":
    case "results":
    case "teamOfTheWeek":
        if($create == 1 && $page == 'matchday')         $current = 'create';
        elseif($modify == 1 && $page == 'matchday')     $current = 'modify';
        elseif($page == 'matchday'
               && isset($_SESSION['matchdayId']))       $current = 'statistics';
        elseif($page == 'matchday')              $current = 'list';
        elseif($create == 1 && $page == 'match')        $current = 'createMatch';
        elseif($page=='prediction')                     $current = 'prediction';
        elseif($page=='results')                        $current = 'results';
        elseif($page=='teamOfTheWeek')                  $current = 'teamOfTheWeek';
        echo Matchday::submenu($pdo, $form, $current);
        break;
    case "player":
        if($create == 1)        $current = 'create';
        elseif($modify == 1)    $current = 'modify';
        else                    $current = 'bestPlayers';
        echo Player::submenu($pdo, $form, $current);
        break;
    case "season":
        if($create == 1)        $current = 'create';
        elseif($modify == 1)    $current = 'modify';
        else                    $current = 'list';
        echo Season::submenu($pdo, $form, $current);
        break;
    case "team":
        if($create == 1 && $page == 'team')        $current = 'create';
        elseif($modify == 1 && $page == 'team')    $current = 'modify';
        else                                       $current = 'marketValue';
        echo Team::submenu($pdo, $form, $current);
        break;
    default:
        Account::exitButton();
        Season::exitButton();
        Championship::exitButton();
        Matchday::exitButton();
        break;
}
?>
    </nav>
	<?php require("../include/fp-menu.php");?>
	<?= App::setTitle(Language::title('site'));?>
    <h1><a href="/"><a href="/"><?= App::getTitle();?></a></h1>
</header>

<?php

/* Page */

echo "<section>\n";
if($page!="") require("../pages/" . $page . ".php");
else {
    
    // Section with menu
        
    echo "<ul class='menu'>\n";
    echo "    <li><h2>$icon_account " . (Language::title('account')) . "</h2>\n";
    echo "       <ul>\n";
    echo "            <li><a href='index.php?page=account'>" . (Language::title('myAccount')) . "</a></li>\n";
    echo "       </ul>\n";
    echo "    </li>\n";
    echo "    <li><h2>$icon_championship " . (Language::title('championship')) . "</h2>\n";
    echo "       <ul>\n";
    echo "            <li><a href='index.php?page=championship'>" . (Language::title('standing')) . "</a></li>\n";
    echo "            <li><a href='index.php?page=dashboard'>" . (Language::title('dashboard')) . "</a></li>\n";
    echo "       </ul>\n";
    echo "    </li>\n";
    echo "    <li><h2>$icon_matchday " . (Language::title('matchday')) . " " . (isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum']:NULL)."</h2>\n";
    if(isset($_SESSION['matchdayId'])){
        echo "        <ul>\n";
        echo "            <li><a href='index.php?page=matchday'>" . (Language::title('statistics')) . "</a></li>\n";
        echo "            <li><a href='index.php?page=prediction'>" . (Language::title('predictions')) . "</a></li>\n";
        echo "            <li><a href='index.php?page=results'>" . (Language::title('results')) . "</a></li>\n";
        echo "            <li><a href='index.php?page=teamOfTheWeek'>" . (Language::title('teamOfTheWeek')) . "</a></li>\n";
        echo "        </ul>\n";
    } else {
        echo "        <ul>\n";

        $req = "SELECT DISTINCT id_matchday, number
FROM matchday
WHERE id_season=" . $_SESSION['seasonId']."
AND id_championship=" . $_SESSION['championshipId'] . " ORDER BY number DESC;";
        $response = $pdo->query($req);
        $counter = $pdo->rowCount();
        
        if($counter>0){
            echo "        <ul>\n";
            echo "            <li><a href='index.php?page=matchday'>" . (Language::title('listMatchdays')) . "</a></li>\n";
            echo "        </ul>\n";
            // Select form
            $list = "<form action='index.php' method='POST'>\n";
            $list .= $form->label(Language::title('selectTheMatchday'));
            $list .= $form->selectSubmit("matchdaySelect", $response);
            $list .= "</form>\n";
            
            // Quicknav button
            $req = "SELECT DISTINCT j.id_matchday, j.number FROM matchday j
            LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
            WHERE m.result IS NULL 
            AND j.id_season=:id_season
            AND j.id_championship=:id_championship
            ORDER BY j.number;";
            $data = $pdo->prepare($req,[
                'id_season' => $_SESSION['seasonId'],
                'id_championship' => $_SESSION['championshipId']
            ]);
            $counter = $pdo->rowCount();
            if($counter>0){
                // $form->setValues($data);
                echo "<form action='index.php' method='POST'>\n";
                echo $form->label(Language::title('quickNav'));
                echo $form->inputHidden("matchdaySelect", $data->id_matchday . "," . $data->number);
                echo $form->submit($icon_quicknav . " " . (Language::title('MD')) . $data->number);
                echo "</form>\n";
            }
            
            echo $list;
            
        } else {
            echo "      <p>" . (Language::title('noMatchday')) . "</p>\n";
            echo "          <ul>\n";
            echo "            <li><a href='index.php?page=matchday&create=1'>" . (Language::title('createAMatchday')) . "</a></li>\n";
            echo "          </ul>\n";
        }
        
        echo "        </ul>\n";
        echo "    </li>\n";
    }
    echo "    </li>\n";
    echo "    <li><h2>" . $icon_team . " " . (Language::title('team')) . "</h2>\n";
    echo "        <ul>\n";
    echo "            <li><a href='index.php?page=team'>" . (Language::title('marketValue')) . "</a></li>\n";
    echo "        </ul>\n";
    echo "    </li>\n";
    echo "    <li><h2>" . $icon_player . " " . (Language::title('player')) . "</h2>\n";
    echo "        <ul>\n";
    echo "            <li><a href='index.php?page=player'>" . (Language::title('bestPlayers')) . "</a></li>\n";
    echo "        </ul>\n";
    echo "    </li>\n";
    echo "</ul>\n";

}
echo "</section>\n";
?>

<footer>
    
</footer>

</body>

</html>

