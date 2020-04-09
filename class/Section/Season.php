<?php 
/**
 * 
 * Class Season
 * Manage season page
 */
namespace FootballPredictions\Section;
use \PDO;

class Season
{
    public function __construct(){

    }
    
    static function submenu($pdo, $form){
        require '../lang/fr.php';
        $val = "<a href='/'>$title_homepage</a>";
        $val .= "<a href='index.php?page=season&create=1'>$title_createASeason</a>";
        $req = "SELECT id_season, name FROM season ORDER BY name;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();
        if($counter > 1){
            $val .= "<form action='index.php?page=season' method='POST'>\n";
            $val .= $form->inputAction('modify');
            $val .= $form->label($title_modifyASeason);
            $val .= $form->selectSubmit('id_season', $data);
            $val .= "</form>\n";
            
        }
        return $val;
    }
}
?>