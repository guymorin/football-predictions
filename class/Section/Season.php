<?php 
/**
 * 
 * Class Season
 * Manage season page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class Season
{
    public function __construct(){

    }
    
    static function exitButton() {
        if(isset($_SESSION['seasonName'])){
            echo "<a class='session' href='index.php?page=season&exit=1'>".$_SESSION['seasonName']." &#10060;</a>";
        }
    }
    
    static function submenu($pdo, $form, $current=null){
        $val ='';
        $currentClass = " class='current'";
        $classL = $classC = '';
        switch($current){
            case 'list':
                $classL = $currentClass;
                break;
            case 'create':
                $classC = $currentClass;
                break;
        }
        if(isset($_SESSION['seasonId']) && $_SESSION['seasonId']>0) {
            Season::exitButton();
            $val .= "<a" . $classL . " href='index.php?page=season'>" . (Language::title('listChampionships')) . "</a>";
            if(isset($_SESSION['championshipId'])){
                $val .= "<a href='index.php?page=dashboard'>" . (Theme::icon('championship')) . " ";
                if($_SESSION['championshipId']>0) {
                    $val .= $_SESSION['championshipName'];
                } else {
                    $val .= Language::title('championship');
                }
            $val .= "</a>";
            }
        } else {
            Account::exitButton();
            $val .= "<a" . $classC . " href='index.php?page=season&create=1'>" . (Language::title('createASeason')) . "</a>";
            if(($_SESSION['role'])==2){
                $req = "SELECT id_season, name FROM season ORDER BY name;";
                $data = $pdo->query($req);
                $counter = $pdo->rowCount();
                if($counter > 1){
                    $val .= "<form action='index.php?page=season' method='POST'>\n";
                    $val .= $form->inputAction('modify');
                    $val .= $form->label(Language::title('modifyASeason'));
                    $val .= $form->selectSubmit('id_season', $data);
                    $val .= "</form>\n";   
                }
            }
        }
        return $val;
    }
    
    static function selectSeason($pdo, $form){
        
        $val = "  <h3>" . (Language::title('selectTheSeason')) . "</h3>\n";
        $req = "SELECT DISTINCT id_season, name
        FROM season
        ORDER BY name DESC";
        $data = $pdo->prepare($req,[],true);
        $counter = $pdo->rowCount();
        if($counter>0){   
            $val .= "<table>\n";
            $val .= "  <tr>\n";
            $val .= "      <th>" . (Language::title('season')) . "</th>\n";
            $val .= "  </tr>\n";
            
            foreach ($data as $d)
            {
                $val .= "  <tr>\n";
                $val .= "<form id='" . ($d->id_season) . "' action='index.php' method='POST'>\n";
                $val .= $form->inputHidden("seasonSelect", $d->id_season . "," . $d->name);
                $val .= "<td>";
                $val .= "<button type='submit' value='". ($d->name) . "'>" . (Theme::icon('season') . " " . ($d->name)) . "</button>";
                $val .= "</td>\n";
                $val .= "</form>\n";
                $val .= "  </tr>\n";
            }
            $val .= "</table>\n";
        }
        // No season
        else    $val .= "  <h3>" . (Language::title('noSeason')) . "</h3>\n";
        return $val;        
    }
    
    static function deletePopup($pdo, $seasonId){
        
        $req .= "DELETE FROM teamOfTheWeek WHERE id_matchday IN (
            SELECT id_matchday FROM matchday WHERE WHERE id_season='".$seasonId."');";
        $req .= "DELETE FROM criterion WHERE id_matchgame IN (
            SELECT id_matchgame FROM matchgame WHERE id_matchday IN (
                SELECT id_matchday FROM matchday WHERE WHERE id_season='".$seasonId."'));";
        $req .= "DELETE FROM matchgame WHERE id_matchday IN (
            SELECT id_matchday FROM matchday WHERE WHERE id_season='".$seasonId."');";
        $req="DELETE FROM matchday WHERE id_season=$seasonId;";
        $req="DELETE FROM marketValue WHERE id_season=$seasonId;";
        $req="DELETE FROM season_team_player WHERE id_season=$seasonId;";
        $req="DELETE FROM season_championship_team WHERE id_season=$seasonId;";
        $req="DELETE FROM season WHERE id_season=$seasonId;";
        $pdo->exec($req);
        $pdo->alterAuto('teamOfTheWeek');
        $pdo->alterAuto('criterion');
        $pdo->alterAuto('matchgame');
        $pdo->alterAuto('matchday');
        $pdo->alterAuto('marketValue');
        $pdo->alterAuto('season_team_player');
        $pdo->alterAuto('season_championship_team');
        $pdo->alterAuto('season');
        popup(Language::title('deleted'),"index.php?page=season");
    }
    static function createForm($error, $form){
        
        $val = '';
        $val .= "<form action='index.php?page=season' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('season')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input(Language::title('name'),"name");
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('create'));
        $val .= "</form>\n";
        return $val;    
    }
    
    static function createPopup($pdo, $seasonName){
        
        $pdo->alterAuto('season');
        $req="INSERT INTO season VALUES(NULL,:name);";
        $pdo->prepare($req,[
            'name' => $seasonName
        ]);
        popup(Language::title('created'),"index.php?page=season");
    }
    
    static function modifyForm($pdo, $error, $form, $seasonId){
        
        $req = "SELECT * FROM season WHERE id_season=:id_season;";
        $data = $pdo->prepare($req,[
            'id_season' => $seasonId
        ]);
        $val = '';
        $val .= "<form action='index.php?page=season' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('season')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->inputHidden('id_season',$data->id_season);
        $val .= $form->input(Language::title('name'),'name');
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('modify'));
        $val .= "</form>\n";
        $val .= "<br />\n";
        $val .= $form->deleteForm('season', 'id_season', $seasonId);
        return $val;
    }
    
    static function modifyPopup($pdo, $seasonName, $seasonId){
        
        $req="UPDATE season
        SET name=:name
        WHERE id_season=:id_season;";
        $pdo->prepare($req,[
            'name' => $seasonName,
            'id_season' => $seasonId
        ]);
        popup(Language::title('modified'),"index.php?page=season");
    }
    
    static function list($pdo, $form){
        $req = "SELECT c.id_championship, c.name, COUNT(*) as nb
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
        $val .= "      <th>" . (Language::title('championship')) . "</th>\n";
        $val .= "      <th>" . (Language::title('teams')) . "</th>\n";
        $val .= "  </tr>\n";
        
        foreach ($data as $d)
        {
            if($d->name == $_SESSION['championshipName']) {
                $val .= "  <tr class='current'>\n";
                $val .= "<form>\n";
                $val .= "<td>";
                $val .= "<button disabled type='submit' style='cursor:default;' value='" . ($d->id_championship) . "," . ($d->name)."'>" . (Theme::icon('championship') . " " . ($d->name)) . "</button>";
                $val .= "</td>\n";
            } else {
                $val .= "  <tr>\n";
                $val .= "<form id='" . ($d->id_championship) . "' action='index.php' method='POST'>\n";
                $val .= $form->inputHidden("championshipSelect", $d->id_championship . "," . $d->name);
                $val .= "<td>";
                $val .= "<button type='submit' value='". ($d->name) . "'>" . (Theme::icon('championship') . " " . ($d->name)) . "</button>";
                $val .= "</td>\n";
            }
            $val .= "      <td>" . $d->nb . "</td>\n";
            $val .= "</form>\n";
            $val .= "  </tr>\n";
        }
        $val .= "</table>\n";
        return $val;
    }
}
?>