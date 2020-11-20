<?php 
/**
 * 
 * Class Championship
 * Manage championship page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Statistics;

class Championship
{
    public function __construct(){

    }
    
    static function selectChampionship($pdo, $form){
        
        $val = "<ul class='menu'>\n";
        $req = "SELECT DISTINCT c.id_championship, c.name
    FROM championship c
    ORDER BY c.name;";
        $pdo->query($req);
        $counter = $pdo->rowCount();
        $list="";
        
        if($counter>0){
            $val .=  "  <h3>" . (Language::title('selectTheChampionship')) . "</h3>\n";
            // Select form
            $list.="<form action='index.php' method='POST'>\n";
            $list.= $form->labelBr(Language::title('championship'));
            $response = $pdo->query($req);
            $list.= $form->selectSubmit("championshipSelect", $response, true, true);
            $list.="</form>\n";
            
            // Quick nav button
            $req = "SELECT * FROM season_championship_team;";
            $data = $pdo->query($req);
            $counter = $pdo->rowCount();
            if($counter>0){
                $req = "SELECT DISTINCT sct.id_championship, c.name
                FROM season_championship_team sct
                LEFT JOIN championship c ON c.id_championship = sct.id_championship
                ORDER BY c.name DESC;";
                $data = $pdo->queryObj($req);
                
                $val .= "<form action='index.php' method='POST'>\n";
                $val .=  $form->labelBr(Language::title('quickNav'));
                $val .=  $form->inputHidden("championshipSelect",$data->id_championship.",".$data->name);
                $val .=  $form->submit(Theme::icon('quicknav')." ".$data->name);
                $val .=  "</form>\n";
                $val .= "<br />\n";
            }
            $val .=  $list;
        }
        // No championship
        else {
            $val .=  "  <h3>" . (Language::title('noChampionship')) . "</h3>\n";
            // Create if admin
            if(($_SESSION['role'])==2){
                $val .= "   <form action='index.php?page=championship&create=1' method='POST'>\n";
                $val .= "            <button type='submit'>" . (Language::title('createAChampionship')) . "</button>\n";
                $val .= "   </form>\n";
            }
        }

        $val .=  "</ul>\n";
        return $val;
    }
    
    static function deletePopup($pdo, $championshipId){
        $req = '';
        $req .= "DELETE FROM teamOfTheWeek WHERE id_matchday IN (
            SELECT id_matchday FROM matchday WHERE id_championship=:id_championship);";
        $req .= "DELETE FROM criterion WHERE id_matchgame IN (
            SELECT id_matchgame FROM matchgame WHERE id_matchday IN (
                SELECT id_matchday FROM matchday WHERE id_championship=:id_championship));";
        $req .= "DELETE FROM matchgame WHERE id_matchday IN (
            SELECT id_matchday FROM matchday WHERE id_championship=:id_championship);";
        $req .= "DELETE FROM matchday WHERE id_championship=:id_championship;";
        $req .= "DELETE FROM season_championship_team WHERE id_championship=:id_championship;";
        $req .= "DELETE FROM championship WHERE id_championship=:id_championship;";
        $req .= "UPDATE fp_user SET last_season = NULL, last_championship = NULL WHERE last_championship=:id_championship;";
        
        $pdo->prepare($req,[
            'id_championship' => $championshipId
        ]);
        $pdo->alterAuto('teamOfTheWeek');
        $pdo->alterAuto('criterion');
        $pdo->alterAuto('matchgame');
        $pdo->alterAuto('matchday');
        $pdo->alterAuto('season_championship_team');
        $pdo->alterAuto('championship');
        popup(Language::title('deleted'),"index.php?page=championship");
    }
    
    static function createForm($pdo, $error, $form){
        
        $val = '';
        $val .= "<form action='index.php?page=championship' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('championship')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input(Language::title('name'),"name");
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('create'));
        $val .= "</form>\n"; 
        return $val;
    }
    
    static function createPopup($pdo, $championshipName){      
        $pdo->alterAuto('championship');
        $req="INSERT INTO championship
        VALUES(NULL,:name);";
        $pdo->prepare($req,[
            'name' => $championshipName
        ]);
        popup(Language::title('created'),"index.php?page=championship");
    }
    
    static function selectMultiForm($pdo, $error, $form){
        $val = '';
        $val .= "<form action='index.php?page=championship' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= "</form>\n";
        $req = "SELECT id_team, name FROM team
                ORDER BY name;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();
        if($counter > 0){
            $val .= "<form action='index.php?page=championship' method='POST'>\n";
            $val .= $form->inputAction('selectTeams');
            $val .= $error->getError();
            $val .= "<fieldset>\n";
            $val .= '<legend>' . (Language::title('teams')). '</legend>';
            $val .= $form->selectSubmit('teams[]', $data, false, true);
            $val .= "</fieldset>\n";
            $val .= "<br />\n";
            $val .= $form->submit(Language::title('select'));
            $val .= "</form>\n";
        } else {
            $val .= "  <h4>" . (Language::title('noTeam')) . "</h4>\n";
            // Create if admin
            if(($_SESSION['role'])==2){
                $val .= "   <form action='index.php?page=team&create=1' method='POST'>\n";
                $val .= "            <button type='submit'>" . Language::title('createATeam') . "</button>\n";
                $val .= "   </form>\n";
            }
            
        }
        return $val;
    }
    
    static function selectMultiPopup($pdo, $teams){
        $pdo->alterAuto('season_championship_team');
        $req = '';
        foreach($teams as $t){
            $req .= "INSERT INTO season_championship_team VALUES(NULL, "
                . $_SESSION['seasonId'] . ","
                . $_SESSION['championshipId'] . "," 
                . $t . ");";
        }
        $pdo->exec($req);
        popup(Language::title('selected'),"index.php?page=championship");
    }
    
    static function modifyForm($pdo, $error, $form, $championshipId){
        
        $req = "SELECT * FROM championship WHERE id_championship=:id_championship;";
        $data = $pdo->prepare($req,[
            'id_championship' => $championshipId
        ]);
        $val = '';
        $val .= "<form action='index.php?page=championship' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('championship')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->inputHidden("id_championship", $data->id_championship);
        $val .= $form->input(Language::title('name'), "name");
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('modify'));
        $val .= "</form>\n";
        $val .= "<br />\n";
        $val .= $form->deleteForm('championship', 'id_championship', $championshipId);
        return $val;
    }
    
    static function modifyPopup($pdo, $championshipName, $championshipId){
        
        $req="UPDATE championship
        SET name=:name
        WHERE id_championship=:id_championship;";
        $pdo->prepare($req,[
            'name' => $championshipName,
            'id_championship' => $championshipId
        ]);
        popup(Language::title('modified'),"index.php?page=championship");
    }
    
    static function dashboard($pdo){
        $val = "<h3>" . (Language::title('dashboard')) . "</h3>";
        $stats = new Statistics();
        $val .= $stats->getStats($pdo, 'dashboard');
        return $val;
    }
    
    static function list($pdo, $standhome, $standaway){
        
        $val = "<div id='standing'>\n";
        $val .= "<ul>\n";
        $val .= "  <li>";
        if($standhome+$standaway==0) $val .= "<p>" . (Language::title('general')) . "</p>";
        else $val .= "<a href='index.php?page=championship'>" . (Language::title('general')) . "</a>";
        $val .= "  </li>\n\t<li>";
        if($standhome==1) $val .= "<p>" . (Language::title('home')) . "</p>";
        else $val .= "<a href='index.php?page=championship&standhome=1'>" . (Language::title('home')) . "</a>";
        $val .= "  </li>\n\t<li>";
        if($standaway==1) $val .= "<p>" . (Language::title('away')) . "</p>";
        else $val .= "<a href='index.php?page=championship&standaway=1'>" . (Language::title('away')) . "</a>\n";
        $val .= "  </li>\n";
        $val .= "</ul>\n";
        
        $val .= "    <table>\n";
        $val .= "      <tr>\n";
        $val .= "            <th> </th>\n";
        $val .= "            <th>" . (Language::title('team')) . "</th>\n";
        $val .= "            <th>" . (Language::title('pts')) . "</th>\n";
        $val .= "            <th>" . (Language::title('MD')) . "</th>\n";
        $val .= "            <th>" . (Language::title('win')) . "</th>\n";
        $val .= "            <th>" . (Language::title('draw')) . "</th>\n";
        $val .= "            <th>" . (Language::title('lose')) . "</th>\n";
        $val .= "      </tr>\n";
        
        $req="SELECT c.id_team, c.name, COUNT(m.id_matchgame) as matchgame,
    	SUM(";
        if($standaway==0){
            $req.="CASE WHEN m.result = '1' AND m.team_1=c.id_team THEN 3 ELSE 0 END +
    		CASE WHEN m.result = 'D' AND m.team_1=c.id_team THEN 1 ELSE 0 END +";
        }
        if($standhome==0){
            $req.="CASE WHEN m.result = '2' AND m.team_2=c.id_team THEN 3 ELSE 0 END +
    		CASE WHEN m.result = 'D' AND m.team_2=c.id_team THEN 1 ELSE 0 END +";
        }
        $req.="0) as points,";
        $req.="	SUM(";
        if($standaway==0){
            $req.="CASE WHEN m.result = '1' AND m.team_1=c.id_team THEN 1 ELSE 0 END +
            ";
        }
        if($standhome==0){
            $req.="CASE WHEN m.result = '2' AND m.team_2=c.id_team THEN 1 ELSE 0 END +";
        }
        $req.="0) as gagne,";
        $req.="SUM(";
        if($standaway==0){
            $req.="CASE WHEN m.result = 'D' AND m.team_1=c.id_team THEN 1 ELSE 0 END +";
        }
        if($standhome==0){
            $req.="CASE WHEN m.result = 'D' AND m.team_2=c.id_team THEN 1 ELSE 0 END +";
        }
        $req.="0) as nul,";
        $req.="SUM(";
        if($standaway==0){
            $req.="CASE WHEN m.result = '2' AND m.team_1=c.id_team THEN 1 ELSE 0 END +";
        }
        if($standhome==0){
            $req.="CASE WHEN m.result = '1' AND m.team_2=c.id_team THEN 1 ELSE 0 END +";
        }
        $req.="0) as perdu
        FROM team c
        LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team
        LEFT JOIN matchday j ON (scc.id_season=j.id_season 
            AND scc.id_championship=j.id_championship)
        LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
        WHERE scc.id_season=:id_season
        AND scc.id_championship=:id_championship
        AND (c.id_team=m.team_1 OR c.id_team=m.team_2)
        AND m.result<>''
        GROUP BY c.id_team,c.name
        ORDER BY points DESC, c.name ASC;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId']
        ],true);
        
        $counterPos=0;
        $previousPoints=0;
        $counter = $pdo->rowCount();
        if($counter>0){
            foreach ($data as $d)
            {
                $val .= "        <tr>\n";
                $val .= "          <td>";
                if($d->points!=$previousPoints){
                    $counterPos++;
                    $val .= $counterPos;
                    $previousPoints=$d->points;
                }
                $val .= "</td>\n";
                $val .= "          <td>";
                $icon = '';
                $teamIcon = Theme::icon('team')."&nbsp;";
                if($counterPos==1) $icon = Theme::icon('medalGold') . "&nbsp;";
                elseif($counterPos==2) $icon = Theme::icon('medalSilver') . "&nbsp;";
                elseif($counterPos==3) $icon = Theme::icon('medalBronze') . "&nbsp;";
                if($icon != '') $val .= $icon . $teamIcon."<strong>" .$d->name."</strong>";
                else $val .= $teamIcon.$d->name;
                $val .= "</td>\n";
                $val .= "          <td>".$d->points."</td>\n";
                $val .= "          <td>".$d->matchgame."</td>\n";
                $val .= "          <td>".$d->gagne."</td>\n";
                $val .= "          <td>".$d->nul."</td>\n";
                $val .= "          <td>".$d->perdu."</td>\n";
                $val .= "        </tr>\n";
            }
        } else {
            $val .= "        <tr>\n";
            $val .= "<td colspan='7'>" . Language::title('notPlayed') . "</td>\n";
            $val .= "        </tr>\n";
        }
    $val .= "   </table>\n";
    $val .= "</div>\n";
    return $val;
    }
}
?>