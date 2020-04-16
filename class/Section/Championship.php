<?php 
/**
 * 
 * Class Championship
 * Manage championship page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use \PDO;

class Championship
{
    public function __construct(){

    }

    static function exitButton() {
        if(isset($_SESSION['championshipName'])){
            echo "<a class='session' href='index.php?page=championship&exit=1'>".$_SESSION['championshipName']." &#10060;</a>";
        }
    }
    
    static function submenu($pdo, $form, $current=null){
        
        $val ='';
        if(isset($_SESSION['championshipId'])){
            $val .= "  	<a href='/'>" . (Language::title('homepage')) . "</a>";
            
            if($current == 'standing'){
                $val .= "<a class='current' href='index.php?page=championship'>" . (Language::title('standing')) . "</a>";
            } else {
                $val .= "<a href='index.php?page=championship'>" . (Language::title('standing')) . "</a>";
            }
            if($current == 'dashboard'){
                $val .= "<a class='current' href='index.php?page=dashboard'>" . (Language::title('dashboard')) . "</a>";
            } else {
                $val .= "<a href='index.php?page=dashboard'>" . (Language::title('dashboard')) . "</a>";
            }
        } else {
            Account::exitButton();
            Season::exitButton();
        }
        
        if($current == 'create'){
            $val .= "<a class='current' href='index.php?page=championship&create=1'>" . (Language::title('createAChampionship')) . "</a>\n";
        } else {
            $val .= "<a href='index.php?page=championship&create=1'>" . (Language::title('createAChampionship')) . "</a>\n";
        }
        $req = "SELECT DISTINCT c.id_championship, c.name
        FROM championship c
        ORDER BY c.name;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();
        if($counter > 1){
            $val .= "<form action='index.php?page=championship' method='POST'>\n";
            $val .= $form->inputAction('modify');
            $val .= $form->label(Language::title('modifyAChampionship'));
            $val .= $form->selectSubmit('id_championship', $data);
            $val .= "</form>\n";
        }
        return $val;
    }
    
    static function selectChampionship($pdo, $form, $icon_quicknav){
        
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
            $list.= $form->labelBr(Language::title('selectTheChampionship'));
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
            $val .=  $form->label(Language::title('quickNav'));
            $val .=  $form->inputHidden("championshipSelect",$data->id_championship.",".$data->name);
            $val .=  $form->submit($icon_quicknav." ".$data->name);
            $val .=  "</form>\n";
            
            $val .=  $list;
        }
        // No championship
        else    $val .=  "  <h2>" . (Language::title('noChampionship')) . "</h2>\n";
        $val .=  "</ul>\n";
        return $val;
    }
    
    static function deletePopup($pdo, $championshipId){
        
        $req="DELETE FROM championship WHERE id_championship=:id_championship;";
        $pdo->prepare($req,[
            'id_championship' => $championshipId
        ]);
        $pdo->alterAuto('championship');
        popup(Language::title('deleted'),"index.php?page=championship");
    }
    
    static function createForm($pdo, $error, $form){
        
        $val = $error->getError();
        $val .= "<form action='index.php?page=championship' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= $form->input(Language::title('name'),"name");
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
    
    static function modifyForm($pdo, $error, $form, $championshipId){
        
        $req = "SELECT * FROM championship WHERE id_championship=:id_championship;";
        $data = $pdo->prepare($req,[
            'id_championship' => $championshipId
        ]);
        $val = $error->getError();
        $val .= "<form action='index.php?page=championship' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= $form->inputHidden("id_championship", $data->id_championship);
        $val .= $form->input(Language::title('name'), "name");
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('modify'));
        $val .= "</form>\n";
        // Delete form
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