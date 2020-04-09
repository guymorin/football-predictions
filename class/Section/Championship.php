<?php 
/**
 * 
 * Class Championship
 * Manage championship page
 */
namespace FootballPredictions\Section;
use \PDO;

class Championship
{
    public function __construct(){

    }
    
    static function submenu($pdo, $form){
        require '../lang/fr.php';
        
        if(isset($_SESSION['championshipId'])){
            $val = "  	<a href='/'>$title_homepage</a>";
            $val .= "<a href='index.php?page=dashboard'>$title_dashboard</a>";
            $val .= "<a href='index.php?page=championship'>$title_standing</a>";
        } else $val .= "      <a href='index.php?page=season&exit=1'>".$_SESSION['seasonName']." &#10060;</a>";
        $val .= "<a href='index.php?page=championship&create=1'>$title_createAChampionship</a>\n";
        $req = "SELECT DISTINCT c.id_championship, c.name
        FROM championship c
        ORDER BY c.name;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();
        if($counter > 1){
            $val .= "<form action='index.php?page=championship' method='POST'>\n";
            $val .= $form->inputAction('modify');
            $val .= $form->label($title_modifyAChampionship);
            $val .= $form->selectSubmit('id_championship', $data);
            $val .= "</form>\n";
        }
        return $val;
    }
}
?>