<?php 
/**
 * 
 * Class Team
 * Manage Team page
 */
namespace FootballPredictions\Section;
use \PDO;

class Team
{
    public function __construct(){

    }
    
    static function submenu($pdo, $form, $current=null){
        require '../lang/fr.php';
        $val = "  	<a href='/'>$title_homepage</a>";
        if($current == 'marketValue'){
            $val .= "<a class='current' href='index.php?page=team'>$title_marketValue</a>";
        } else {
            $val .= "<a href='index.php?page=team'>$title_marketValue</a>";
        }
        if($current == 'create'){
            $val .= "<a class='current' href='index.php?page=team&create=1'>$title_createATeam</a>\n"; 
        } else {
            $val .= "<a href='index.php?page=team&create=1'>$title_createATeam</a>\n";
        }
        $req = "SELECT * FROM team c ORDER BY name;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();
        if($counter > 1){
            $val .= "<form action='index.php?page=team' method='POST'>\n";
            $val .= $form->inputAction('modify');
            $val .= $form->label($title_modifyATeam);
            $val .= $form->selectSubmit('id_team', $data);
            $val .= "</form>\n";
        }
        return $val;
    }
    
    static function deletePopup($pdo, $teamId){
        require '../lang/fr.php';
        $req="DELETE FROM team WHERE id_team=:id_team;";
        $pdo->prepare($req,[
            'id_team' => $teamId
        ]);
        $pdo->alterAuto('team');
        popup($title_deleted,"index.php?page=team");
    }
    static function createForm($pdo, $error, $form, $teamName, $weatherCode){
        require '../lang/fr.php';
        $val = $error->getError();
        $val .= "<form action='index.php?page=team' method='POST'>\n";
        $val .= $form->inputAction('create');
        $form->setValue('name', $teamName);
        $form->setValue('weather_code', $weatherCode);
        $val .= $form->input($title_name, 'name');
        $val .= "<br />\n";
        $val .= $form->input($title_weathercode, 'weather_code');
        $val .= "<br />\n";
        $val .= $form->submit($title_create);
        $val .= "</form>\n";  
        return $val;
    }
    
    static function createPopup($pdo, $teamName, $weatherCode){
        require '../lang/fr.php';
        $pdo->alterAuto('team');
        $req="INSERT INTO team VALUES(NULL, '" . $teamName . "', $weatherCode);";
        $pdo->exec($req);
        popup($title_created,"index.php?page=team");
    }
    
    static function modifyForm($pdo, $error, $form, $teamId){
        require '../lang/fr.php';
        $req = "SELECT * FROM team WHERE id_team=:id_team;";
        $data = $pdo->prepare($req,[
            'id_team' => $teamId
        ]);
        $val = $error->getError();
        $val .= "<form action='index.php?page=team' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= $form->inputHidden('id_team',$teamId);
        $val .= $form->input($title_name, 'name');
        $val .= "<br />\n";
        $val .= $form->input($title_weathercode, 'weather_code');
        $val .= "<br />\n";
        $val .= $form->submit($title_modify);
        $val .= "</form>\n";
        // Delete
        $val .= $form->deleteForm('team', 'id_team', $teamId);
        return $val;
    }
    
    static function modifyFormMarketValue($pdo, $error, $form){
        require '../lang/fr.php';
        $req = "SELECT c.*, v.marketValue
        FROM team c
        LEFT JOIN marketValue v ON v.id_team=c.id_team
        LEFT JOIN season_championship_team scc ON scc.id_team=c.id_team
        WHERE scc.id_season=:id_season
        AND scc. id_championship=:id_championship;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId']
        ],true);
        $val = $error->getError();
        $val .= "<form action='index.php?page=team' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->label($title_modifyAMarketValue);
        $val .= "<table>\n";
        $val .= "  <tr>\n";
        $val .= "      <th>$title_team</th>\n";
        $val .= "      <th>$title_marketValue</th>\n";
        $val .= "  </tr>\n";
        foreach ($data as $d)
        {
            $val .= "  <tr>\n";
            $val .= "      <td>\n";
            $val .= $form->inputHidden('id_team[]', $d->id_team);
            $val .= $d->name;
            $val .= "      </td>\n";
            $val .= "      <td>\n";
            $form->setValue('marketValue',$d->marketValue);
            $val .= $form->input('', 'marketValue[]');
            $val .= "      </td>\n";
            $val .= "  </tr>\n";
        }
        $val .= "</table>\n";
        $val .= $form->submit($title_modify);
        return $val;
    }
    
    static function modifyPopup($pdo, $teamName, $weatherCode, $teamId){
        require '../lang/fr.php';
        $req="UPDATE team SET name = '" . $teamName . "', weather_code = '" . $weatherCode . "' 
        WHERE id_team = '" . $teamId . "';";
        $pdo->exec($req);
        popup($title_modified,"index.php?page=team");
    }
    
    static function modifyPopupMarketValue($pdo, $error, $val){
        require '../lang/fr.php';
        $pdo->alterAuto('marketValue');
        $req = "";
        foreach($val as $k=>$v){
            $v=$error->check('Digit', $v, $title_marketValue);
            if($v>0){
                $r = "SELECT COUNT(*) as nb FROM marketValue
                WHERE id_season=:id_season AND id_team=:id_team;";
                $data = $pdo->prepare($r,[
                    'id_season' => $_SESSION['seasonId'],
                    'id_team' => $k
                ]);
                
                if($data->nb==0){
                    $req .= "INSERT INTO marketValue VALUES(NULL,'".$_SESSION['seasonId']."','".$k."','".$v."');";
                }
                if($data->nb==1){
                    $req .= "UPDATE marketValue SET marketValue='".$v."' WHERE id_season='".$_SESSION['seasonId']."' AND id_team='".$k."';";
                }
            }
        }
        
        $pdo->exec($req);
        popup($title_modified,"index.php?page=team");
    }
}
?>