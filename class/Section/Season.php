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
        isset($_SESSION['seasonId']) ? $val .= "<a href='index.php?page=season'>$title_listChampionships</a>" : null;
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
    
    static function selectSeason($pdo, $form, $icon_quicknav){
        require '../lang/fr.php';
        $val = "<ul class='menu'>\n";
        $req = "SELECT id_season, name FROM season ORDER BY name;";
        $pdo->query($req);
        $counter = $pdo->rowCount();
        
        if($counter>0){
            
            // Select form
            $list = "<form action='index.php?page=championship' method='POST'>\n";
            $list.= $form->labelBr($title_selectTheSeason);
            $response = $pdo->query($req);
            $list.= $form->selectSubmit("seasonSelect",$response);
            $list.= "</form>\n";
            
            // Quick nav button
            $req = "SELECT DISTINCT sct.id_season, s.name
            FROM season_championship_team sct
            LEFT JOIN season s ON s.id_season = sct.id_season
            ORDER BY s.name DESC;";
            $data = $pdo->queryObj($req);
            $form->setValues($data);
            
            $val .= "<form action='index.php?page=championship' method='POST'>\n";
            $val .= $form->inputHidden("seasonSelect",$data->id_season.",".$data->name);
            $val .= $form->label($title_quickNav);
            $val .= $form->submit($icon_quicknav." ".$data->name);
            $val .= "</form>\n";
            
            $val .= $list;
            return $val;
        }
        // No season
        else    echo "  <h3>$title_noSeason</h3>\n";
        echo "</ul>\n";
    }
    
    static function deletePopup($pdo, $seasonId){
        $req="DELETE FROM season WHERE id_season=:id_season;";
        $pdo->prepare($req,[
            'id_season' => $seasonId
        ]);
        $pdo->alterAuto('season');
        popup($title_deleted,"index.php?page=season");
    }
    static function createForm($error, $form){
        require '../lang/fr.php';
        $val = $error->getError();
        $val .= "<form action='index.php?page=season' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= $form->input($title_name,"name");
        $val .= $form->submit($title_create);
        $val .= "</form>\n";
        return $val;    
    }
    
    static function createPopup($pdo, $seasonName){
        require '../lang/fr.php';
        $pdo->alterAuto('season');
        $req="INSERT INTO season VALUES(NULL,:name);";
        $pdo->prepare($req,[
            'name' => $seasonName
        ]);
        popup($title_created,"index.php?page=season");
    }
    
    static function modifyForm($pdo, $error, $form, $seasonId){
        require '../lang/fr.php';
        $req = "SELECT * FROM season WHERE id_season=:id_season;";
        $data = $pdo->prepare($req,[
            'id_season' => $seasonId
        ]);
        $val = $error->getError();
        $val .= "<form action='index.php?page=season' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= $form->inputHidden('id_season',$data->id_season);
        $val .= $form->input($title_name,'name');
        $val .= $form->submit($title_modify);
        $val .= "</form>\n";
        
        // Delete form
        $val .= "<form action='index.php?page=season' method='POST' onsubmit='return confirm()'>\n";
        $val .= $form->inputAction('delete');
        $val .= $form->inputHidden('id_season', $seasonId);
        $val .= $form->inputHidden("name",$data->name);
        $val .= $form->submit("&#9888 $title_delete &#9888");
        $val .= "</form>\n";
        return $val;
    }
    
    static function modifyPopup($pdo, $seasonName, $seasonId){
        require '../lang/fr.php';
        $req="UPDATE season
        SET name=:name
        WHERE id_season=:id_season;";
        $pdo->prepare($req,[
            'name' => $seasonName,
            'id_season' => $seasonId
        ]);
        popup($title_modified,"index.php?page=season");
    }
    
    static function list($pdo){
        require '../lang/fr.php';
        $req = "SELECT c.name, COUNT(*) as nb
        FROM championship c
        LEFT JOIN season_championship_team scc ON c.id_championship=scc.id_championship
        WHERE scc.id_season=:id_season
        GROUP BY c.name
        ORDER BY c.name";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId']
        ],true);
        $val = "<table>\n";
        $val .= "  <tr>\n";
        $val .= "      <th>$title_championship</th>\n";
        $val .= "      <th>$title_teams</th>\n";
        $val .= "  </tr>\n";
        
        foreach ($data as $d)
        {
            $val .= "  <tr>\n";
            $val .= "      <td>" . $d->name . "</td>\n";
            $val .= "      <td>" . $d->nb . "</td>\n";
            $val .= "  </tr>\n";
        }
        $val .= "</table>\n";
        return $val;
    }
}
?>