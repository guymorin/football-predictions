<?php 
/**
 * 
 * Class Matchday
 * Manage Matchday page
 */
namespace FootballPredictions\Section;
use \PDO;

class Matchday
{
    public function __construct(){

    }
    
    static function submenu($pdo, $form){
        require '../lang/fr.php';
        echo "  	<a href='/'>$title_homepage</a>";
        if(isset($_SESSION['matchdayId'])){
            echo "<a href='index.php?page=matchday'>$title_statistics</a>";
            echo "<a href='index.php?page=prediction'>$title_predictions</a>";
            echo "<a href='index.php?page=results'>$title_results</a>";
            echo "<a href='index.php?page=teamOfTheWeek'>$title_teamOfTheWeek</a>";
            echo "<a href='index.php?page=match&create=1'>$title_createAMatch</a>";
            echo "<a href='index.php?page=match&modify=1'>$title_modifyAMatch</a>";
        } else echo "<a href='index.php?page=matchday&create=1'>$title_createAMatchday</a>\n";
    }
    
    static function createMatchForm($pdo, $error, $form){
        require '../lang/fr.php';
        $val = $error->getError();
        $val .= "<form action='index.php?page=match' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= $form->inputHidden('matchdayId', $_SESSION['matchdayId']);
        
        $val .= $form->inputDate($title_date, 'date', '');
        $val .= "<br />";
        
        $req="SELECT c.id_team, c.name FROM team c
            LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team
            WHERE scc.id_season=:id_season AND scc.id_championship=:id_championship ORDER BY c.name;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId']
        ],true);
        $val .= $form->selectTeam($pdo,'team_1');
        $val .= $form->selectTeam($pdo,'team_2');
        $val .= "<br />";
        
        $val .= $form->labelBr($title_odds);
        $val .= $form->inputNumber('1', 'odds1', '0', '0.01');
        $val .= $form->inputNumber($title_draw, 'oddsD', '0', '0.01');
        $val .= $form->inputNumber('2', 'odds2', '0', '0.01');
        $val .= "<br />";
        
        $val .= $form->labelBr($title_result);
        $val .= $form->labelId('1', '1');
        $val .= $form->inputRadio('1', 'result', '1', false);
        $val .= $form->labelId('D', $title_draw);
        $val .= $form->inputRadio('D', 'result', 'D', false);
        $val .= $form->labelId('2', '2');
        $val .= $form->inputRadio('2', 'result', '2', false);
        $val .= "<br />";
        
        $val .= $form->submit($title_create);
        
        $val .= "</form>\n";   
        return $val;
    }
    
    static function modifyMatchForm($pdo, $data, $idMatch, $error, $form){
        require '../lang/fr.php';
        echo $error->getError();
        echo "	 <form action='index.php?page=match' method='POST'>\n";
        $form->setValues($data);
        echo $form->inputAction('modify');
        echo $form->inputHidden('id_matchgame',$data->id_matchgame);
        
        echo $form->inputDate($title_date, 'date', $data->date);
        echo "<br />";
        
        echo $form->selectTeam($pdo,'team_1',$data->id1);
        echo $form->selectTeam($pdo,'team_2',$data->id2);
        echo "<br />";
        
        echo "	    <p><label>$title_odds :</label>\n";
        echo "         1<input type='number' step='0.01' size='2' name='odds1' value='".$data->odds1."'>\n";
        echo "         $title_draw<input type='number' step='0.01' size='2' name='oddsD' value='".$data->oddsD."'>\n";
        echo "         2<input type='number' step='0.01' size='2' name='odds2' value='".$data->odds2."'>\n";
        echo "      </p>\n";
        
        echo "	    <p><label>$title_result :</label>\n";
        echo "     <input type='radio' name='result' id='1' value='1'";
        if($data->result=="1") echo " checked";
        echo "><label for='1'>1</label>\n";
        echo "     <input type='radio' name='result' id='D' value='D'";
        if($data->result=="D") echo " checked";
        echo "><label for='D'>$title_draw</label>\n";
        echo "     <input type='radio' name='result' id='2' value='2'";
        if($data->result=="2") echo " checked";
        echo "><label for='2'>2</label>\n";
        
        echo $form->submit($title_modify);
        echo "</form>\n";
        
        echo "<form action='index.php?page=match' method='POST' onsubmit='return confirm()'>\n";
        echo $form->inputAction('delete');
        echo $form->inputHidden('id_matchgame', $idMatch);
        echo $form->submit("&#9888 $title_delete $data->name1 - $data->name2 &#9888");
        echo "</form>\n";
    }
}
?>