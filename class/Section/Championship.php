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
    
    static function submenu($pdo, $form, $current=null){
        require '../lang/fr.php';
        
        if(isset($_SESSION['championshipId'])){
            $val = "  	<a href='/'>$title_homepage</a>";
            
            if($current == 'standing'){
                $val .= "<a class='current' href='index.php?page=championship'>$title_standing</a>";
            } else {
                $val .= "<a href='index.php?page=championship'>$title_standing</a>";
            }
            if($current == 'dashboard'){
                $val .= "<a class='current' href='index.php?page=dashboard'>$title_dashboard</a>";
            } else {
                $val .= "<a href='index.php?page=dashboard'>$title_dashboard</a>";
            }
        } else {
            $val .= "<a class='session' href='index.php?page=season&exit=1'>".(isset($_SESSION['seasonName']) ? $_SESSION['seasonName'] : null)." &#10060;</a>";
        }
        
        if($current == 'create'){
            $val .= "<a class='current' href='index.php?page=championship&create=1'>$title_createAChampionship</a>\n";
        } else {
            $val .= "<a href='index.php?page=championship&create=1'>$title_createAChampionship</a>\n";
        }
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
    
    static function selectChampionship($pdo, $form, $icon_quicknav){
        require '../lang/fr.php';
        $val = "<ul class='menu'>\n";
        $req = "SELECT DISTINCT c.id_championship, c.name
    FROM championship c
    ORDER BY c.name;";
        $pdo->query($req);
        $counter = $pdo->rowCount();
        $list="";
        
        if($counter>0){
            
            // Select form
            $list.="<form action='index.php' method='POST'>\n";
            $list.= $form->labelBr($title_selectTheChampionship);
            $response = $pdo->query($req);
            $list.= $form->selectSubmit("championshipSelect", $response);
            $list.="</form>\n";
            
            // Quick nav button
            $req = "SELECT DISTINCT sct.id_championship, c.name
        FROM season_championship_team sct
        LEFT JOIN championship c ON c.id_championship = sct.id_championship
        ORDER BY c.name DESC;";
            $data = $pdo->queryObj($req);
            
            $val .= "<form action='index.php' method='POST'>\n";
            $val .=  $form->label($title_quickNav);
            $val .=  $form->inputHidden("championshipSelect",$data->id_championship.",".$data->name);
            $val .=  $form->submit($icon_quicknav." ".$data->name);
            $val .=  "</form>\n";
            
            $val .=  $list;
        }
        // No championship
        else    $val .=  "  <h2>$title_noChampionship</h2>\n";
        $val .=  "</ul>\n";
        return $val;
    }
    
    static function deletePopup($pdo, $championshipId){
        require '../lang/fr.php';
        $req="DELETE FROM championship WHERE id_championship=:id_championship;";
        $pdo->prepare($req,[
            'id_championship' => $championshipId
        ]);
        $pdo->alterAuto('championship');
        popup($title_deleted,"index.php?page=championship");
    }
    
    static function createForm($pdo, $error, $form){
        require '../lang/fr.php';
        $val = $error->getError();
        $val .= "<form action='index.php?page=championship' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= $form->input($title_name,"name");
        $val .= "<br />\n";
        $val .= $form->submit($title_create);
        $val .= "</form>\n"; 
        return $val;
    }
    
    static function createPopup($pdo, $championshipName){
        require '../lang/fr.php';
        $pdo->alterAuto('championship');
        $req="INSERT INTO championship
        VALUES(NULL,:name);";
        $pdo->prepare($req,[
            'name' => $championshipName
        ]);
        popup($title_created,"index.php?page=championship");
    }
    
    static function modifyForm($pdo, $error, $form, $championshipId){
        require '../lang/fr.php';
        $req = "SELECT * FROM championship WHERE id_championship=:id_championship;";
        $data = $pdo->prepare($req,[
            'id_championship' => $championshipId
        ]);
        $val = $error->getError();
        $val .= "<form action='index.php?page=championship' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= $form->inputHidden("id_championship", $data->id_championship);
        $val .= $form->input($title_name, "name");
        $val .= "<br />\n";
        $val .= $form->submit($title_modify);
        $val .= "</form>\n";
        
        // Delete form
        $val .= $form->deleteForm('championship', 'id_championship', $championshipId);
        return $val;
    }
    
    static function modifyPopup($pdo, $championshipName, $championshipId){
        require '../lang/fr.php';
        $req="UPDATE championship
        SET name=:name
        WHERE id_championship=:id_championship;";
        $pdo->prepare($req,[
            'name' => $championshipName,
            'id_championship' => $championshipId
        ]);
        popup($title_modified,"index.php?page=championship");
    }
    
    static function list($pdo, $standhome, $standaway){
        require '../lang/fr.php';
        $val = "<div id='standing'>\n";
        $val .= "<ul>\n";
        $val .= "  <li>";
        if($standhome+$standaway==0) $val .= "<p>$title_general</p>";
        else $val .= "<a href='index.php?page=championship'>$title_general</a>";
        $val .= "  </li>\n\t<li>";
        if($standhome==1) $val .= "<p>$title_home</p>";
        else $val .= "<a href='index.php?page=championship&standhome=1'>$title_home</a>";
        $val .= "  </li>\n\t<li>";
        if($standaway==1) $val .= "<p>$title_away</p>";
        else $val .= "<a href='index.php?page=championship&standaway=1'>$title_away</a>\n";
        $val .= "  </li>\n";
        $val .= "</ul>\n";
        
        $val .= "    <table>\n";
        $val .= "      <tr>\n";
        $val .= "            <th> </th>\n";
        $val .= "            <th>$title_team</th>\n";
        $val .= "            <th>$title_pts</th>\n";
        $val .= "            <th>$title_MD</th>\n";
        $val .= "            <th>$title_win</th>\n";
        $val .= "            <th>$title_draw</th>\n";
        $val .= "            <th>$title_lose</th>\n";
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
        
        $counter=0;
        $previousPoints=0;
        
        foreach ($data as $d)
        {
            $val .= "        <tr>\n";
            $val .= "          <td>";
            if($d->points!=$previousPoints){
                $counter++;
                $val .= $counter;
                $previousPoints=$d->points;
            }
            $val .= "</td>\n";
            $val .= "          <td>".$d->name."</td>\n";
            $val .= "          <td>".$d->points."</td>\n";
            $val .= "          <td>".$d->matchgame."</td>\n";
            $val .= "          <td>".$d->gagne."</td>\n";
            $val .= "          <td>".$d->nul."</td>\n";
            $val .= "          <td>".$d->perdu."</td>\n";
            $val .= "        </tr>\n";
        }
    $val .= "   </table>\n";
    $val .= "</div>\n";
    return $val;
    }
}
?>