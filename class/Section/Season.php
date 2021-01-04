<?php 
/**
 * 
 * Class Season Form
 * Manage Season Form page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class Season
{
    public function __construct(){

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
        else {
            $val .= "  <h4>" . (Language::title('noSeason')) . "</h4>\n";
        
            // Create if admin
            if(($_SESSION['role'])==2){
                $val .= "   <form action='index.php?page=season&create=1' method='POST'>\n";
                $val .= "<fieldset>\n";
                $val .= "            <button type='submit'> " 
                            .Theme::icon('create')." "
                            . (Language::title('createASeason')) . "</button>\n";
                $val .= "</fieldset>\n";
                $val .= "   </form>\n";
            }
        }
        
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
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('create')." "
                .Language::title('create'));
        $val .= "</fieldset>\n";
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
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('modify')." "
                .Language::title('modify'));
        $val .= "</form>\n";
        $val .= $form->deleteForm('season', 'id_season', $seasonId);
        $val .= "</fieldset>\n";
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
        GROUP BY c.name, c.id_championship 
        ORDER BY c.name";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId']
        ],true);
        $counter= $pdo->rowCount();
        if($counter>0){ 
            $val = "<table>\n";
            $val .= "  <tr>\n";
            $val .= "      <th>" . (Language::title('championship')) . "</th>\n";
            $val .= "      <th>" . (Language::title('teams')) . "</th>\n";
            $val .= "  </tr>\n";
            
            foreach ($data as $d)
            {
                if(isset($_SESSION['championshipName']) and ($d->name == $_SESSION['championshipName']) ) {
                    $val .= "  <tr class='current'>\n";
                    $val .= "<form>\n";
                    $val .= "<td>";
                    $val .= "<button disabled type='submit' style='cursor:default;' value='" . ($d->id_championship) . "," . ($d->name)."'>" . (Theme::icon('championship') . " " . ($d->name)) . "</button>";
                    $val .= "</td>\n";
                } else {
                    $val .= "  <tr>\n";
                    $val .= "<form id='" . ($d->id_championship) . "' action='index.php?page=dashboard' method='POST'>\n";
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
        } else      header('Location:index.php?page=championship');
        return $val;
    }
}
?>