<?php 
/**
 * 
 * Class Matchday
 * Manage Matchday page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Statistics;
use FootballPredictions\Theme;
use FootballPredictions\Forms;

class Matchday
{
    public function __construct(){

    }
    
    static function deletePopup($pdo, $matchdayId){
        $req = '';
        $req .= "DELETE FROM teamOfTheWeek WHERE id_matchday='".$matchdayId."';";
        $req .= "DELETE FROM criterion WHERE id_matchgame IN (
            SELECT id_matchgame FROM matchgame WHERE id_matchday='".$matchdayId."');";
        $req .= "DELETE FROM matchgame WHERE id_matchday='".$matchdayId."';";
        $req .= "DELETE FROM matchday WHERE id_matchday='".$matchdayId."';";
        $pdo->exec($req);
        $pdo->alterAuto('teamOfTheWeek');
        $pdo->alterAuto('criterion');
        $pdo->alterAuto('matchgame');
        $pdo->alterAuto('matchday');
        popup(Language::title('deleted'),"index.php?page=matchday");
    }
        
    static function createForm($pdo, $error, $form){
        
        $val = '';
        $val .= "<form action='index.php?page=matchday' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('matchday')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input(Language::title('number'),'number');
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('create'));
        $val .= "</form>\n";
        return $val;
    }
    
    static function createMultiForm($pdo, $error, $form){
        
        $val = '';
        $val .= "<form action='index.php?page=matchday&create=1' method='POST'>\n";
        $val .= $form->inputAction('createMulti');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('matchdays')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input(Language::title('matchdayNumber'),'totalNumber');
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('create'));
        $val .= "</form>\n";
        return $val;
    }
    
    static function createPopup($pdo, $matchdayNumber){
        
        $pdo->alterAuto('matchday');
        $req = "INSERT INTO matchday
        VALUES(NULL,:id_season,:id_championship,:number);";
        $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId'],
            'number' => $matchdayNumber
        ]);
        popup(Language::title('created'),"index.php?page=matchday");
    }
    
    static function createMultiPopup($pdo, $matchdayTotalNumber){
        
        $pdo->alterAuto('matchday');
        $req = '';
        for($i=1;$i<=$matchdayTotalNumber;$i++){
            $req .= "INSERT INTO matchday
            VALUES(NULL,:id_season,:id_championship,$i);";            
        }
        $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId'],
        ]);
        $_SESSION['noMatchday'] = false;
        popup(Language::title('created'),"index.php?page=matchday");
    }
        
    static function modifyForm($pdo, $matchdayId, $error, $form){
        
        $req = "SELECT * FROM matchday WHERE id_matchday=:id_matchday;";
        $data = $pdo->prepare($req,[
            'id_matchday' => $matchdayId
        ]);
        
        $val = '';
        $val .= " <form action='index.php?page=matchday' method='POST';'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('matchday')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->inputHidden("id_matchday", $data->id_matchday);
        $val .= $form->input(Language::title('number'), "number");
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('modify'));
        $val .= " </form>\n";
        $val .= "<br />\n";
        $val .= $form->deleteForm('matchday', 'id_matchday', $matchdayId);
        return $val;
    }
    
    static function modifyPopup($pdo, $matchdayNumber, $matchdayId){
        
        $req="UPDATE matchday
        SET number=:number
        WHERE id_matchday=:id_matchday;";
        $pdo->prepare($req,[
            'number' => $matchdayNumber,
            'id_matchday' => $matchdayId
        ]);
        popup(Language::title('modified'),"index.php?page=matchday");
    }
    
    static function stats($pdo){
        changeMD($pdo,"statistics");
        $val = "<h3>" . (Language::title('statistics')) . "</h3>";
        $stats = new Statistics();
        $val .= $stats->getStats($pdo, 'matchday');
        return $val;
    }
    
    static function list($pdo, $form){
        $val = "";
        // Quicknav button
        $req = "SELECT DISTINCT j.id_matchday, j.number, COUNT(CASE WHEN m.result IN('1','D','2') THEN 1 END) as result FROM matchday j
                LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
                WHERE m.result < 10 
                AND j.id_season=:id_season
                AND j.id_championship=:id_championship 
                GROUP BY  j.id_matchday, j.number 
                HAVING result < 5 
                ORDER BY j.number;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId']
        ]);
        $quickNav = "";
        $counter = $pdo->rowCount();
        if( ($counter>0) && ($data->number>3) ){
            $quickNav .= "<table>\n";
            $quickNav .= "  <tr>\n";
            $quickNav .= "      <td>" . (Language::title('matchdayNext')) . " :</td>\n";
            $quickNav .= "<form id='" . ($data->id_matchday) . "' action='index.php?page=statistics' method='POST'>\n";
            $quickNav .= $form->inputHidden("matchdaySelect", $data->id_matchday . "," . $data->number);
            $quickNav .= "  <td>";
            $quickNav .= "<button type='submit' value='".((Language::title('MD')) . ($data->number))."'>" . (Theme::icon('matchday') . " " . (Language::title('MD')) . ($data->number)) . "</button>";
            $quickNav .= "</td>\n";
            $quickNav .= "</form>\n";
            $quickNav .= "  </tr>\n";   
            $quickNav .= "</table>\n<br />\n";   
        }
        $val .= $quickNav;
       
        $req = "SELECT md.id_matchday, md.number as number, COUNT(id_matchgame) as nb, COUNT(CASE WHEN mg.result IN('1','D','2') THEN 1 END) as played
        FROM matchday md
        LEFT JOIN matchgame mg ON mg.id_matchday=md.id_matchday
        WHERE md.id_season = :id_season 
        AND md.id_championship = :id_championship 
        GROUP BY md.id_matchday, md.number
        ORDER BY md.number";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId']
        ],true);
        $val .= "<table class='matchdayList'>\n";
        $val .= "  <tr>\n";
        $val .= "      <th>" . (Language::title('matchday')) . "</th>\n";
        $val .= "      <th>" . (Language::title('matchNumber')) . "</th>\n";
        $val .= "      <th>" . (Language::title('matchPlayed')) . "</th>\n";
        $val .= "  </tr>\n";
        $counter = $pdo->rowCount();
        if($counter>0){
            
            foreach ($data as $d)
            {
                if(isset($_SESSION['matchdayNum']) && $d->number==$_SESSION['matchdayNum']) {
                    $val .= "  <tr class='current'>\n";
                    $val .= "<form>\n";
                    $val .= "<td>";
                    $val .= "<button disabled style='cursor:default' type='submit' value='".((Language::title('MD')) . ($d->number))."'>" . (Theme::icon('matchday') . " " . (Language::title('MD')) . ($d->number)) . "</button>";
                    $val .= "</td>\n";
                }
                else {
                    $val .= "  <tr>\n";
                    $val .= self::matchdayButtons($d->id_matchday,$d->number);
                }
                $val .= "      <td>" . $d->nb . "</td>\n";
                $val .= "      <td>" . $d->played . "</td>\n";
                $val .= "  </tr>\n";
            }            
        } else {
            $val .= "  <tr>\n";
            $val .= "      <td colspan='3'>" . Language::title('noMatchday') . " / " . Language::title('noMatch') . "</td>\n";
            $val .= "  </tr>\n";
        }

        $val .= "</table>\n";
        return $val;
    }
    
    static function matchdayButtons($id, $num){
        $val = '';
        $form = new Forms();
        $val .= "<form id='" . ($id) . "' action='index.php?page=statistics' method='POST'>\n";
        $val .= $form->inputHidden("matchdaySelect", $id . "," . $num);
        $val .= "<td>";
        $val .= "<button type='submit' value='".((Language::title('MD')) . ($num))."'>" 
                . (Theme::icon('matchday') . " " 
                . (Language::title('MD')) . ($num)) 
                . "</button>";
        $val .= "</td>\n";
        $val .= "</form>\n";
        return $val;
    }
}
?>