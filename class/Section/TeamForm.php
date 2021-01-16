<?php 
/**
 * 
 * Class Team Form
 * Manage forms in team page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use \PDO;

class TeamForm
{
    public function __construct(){

    }
    
    static function createForm($pdo, $error, $form, $teamName, $weatherCode){    
        $val = '';
        $val .= "<form action='index.php?page=team' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('team')) . "</legend>\n";
        $val .= $error->getError();
        $form->setValue('name', $teamName);
        $form->setValue('weather_code', $weatherCode);
        $val .= $form->input(Language::title('name'), 'name');
        $val .= "<br />\n";
        $val .= $form->input(Language::title('weathercode'), 'weather_code');
        $val .= "</fieldset>\n";
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('create')." "
                .Language::title('create'));
        $val .= "</fieldset>\n";
        $val .= "</form>\n";  
        return $val;
    }
    
    static function modifyForm($pdo, $error, $form, $teamId){  
        $req = "SELECT * FROM team WHERE id_team=:id_team;";
        $data = $pdo->prepare($req,[
            'id_team' => $teamId
        ]);
        $val = '';
        $val .= "<form action='index.php?page=team' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('team')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->inputHidden('id_team',$teamId);
        $val .= $form->input(Language::title('name'), 'name');
        $val .= "<br />\n";
        $val .= $form->input(Language::title('weathercode'), 'weather_code');
        $val .= "</fieldset>\n";
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('modify')." "
            .Language::title('modify'));
        $val .= "</form>\n";
        $req = "SELECT * FROM season_championship_team 
        WHERE id_season = :id_season 
        AND id_championship = :id_championship 
        AND id_team = :id_team;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId'],
            'id_team' => $teamId
        ],true);
        $counter = $pdo->rowCount();
        if($counter==0){
            $val .= "<form action='index.php?page=team' method='POST'>\n";
            $val .= $form->inputAction('add');
            $val .= $form->inputHidden('id_team',$teamId);
            $val .= $form->submit(Theme::icon('add')." "
                .Language::title('addTeamTo')." :<br />"
                .$_SESSION['championshipName']." "
                .$_SESSION['seasonName']);
            $val .= "</form>\n";
            $val .= "<br />\n";
        }

        $val .= $form->deleteForm('team', 'id_team', $teamId);
        $val .= "</fieldset>\n";
        return $val;
    }
    
    static function modifyFormMarketValue($pdo, $error, $form){
        $val = '';
        $req = "SELECT DISTINCT c.*, v.marketValue
        FROM team c
        LEFT JOIN marketValue v ON v.id_team=c.id_team
        LEFT JOIN season_championship_team scc ON scc.id_team=c.id_team 
        WHERE scc.id_season=:id_season
        AND scc.id_championship=:id_championship 
        AND v.id_season=:id_season 
        ORDER BY c.name;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId']
        ],true);
        $counter = $pdo->rowCount();
        if($counter > 0){
            if(($_SESSION['role'])==2){ 
                $val = $error->getError();
                $val .= "<form action='index.php?page=team' method='POST'>\n";
                $form->setValues($data);
            }
            $val .= "<table class='team'>\n";
            $val .= "  <tr>\n";
            $val .= "      <th>" . (Language::title('team')) . "</th>\n";
            $val .= "      <th>" . (Language::title('marketValue')) . "</th>\n";
            $val .= "  </tr>\n";
            foreach ($data as $d)
            {
                $val .= "  <tr>\n";
                $val .= "      <td>\n";
                if(($_SESSION['role'])==2) $val .= $form->inputHidden('id_team[]', $d->id_team);
                $val .= Theme::icon('team') . "&nbsp;" . $d->name;
                $val .= "      </td>\n";
                $val .= "      <td>\n";
                if(($_SESSION['role'])==2){ 
                    $form->setValue('marketValue',$d->marketValue);
                    $val .= $form->input('', 'marketValue[]');
                } else $val.= $d->marketValue;
                $val .= "      </td>\n";
                $val .= "  </tr>\n";
            }
            $val .= "</table>\n";
            if(($_SESSION['role'])==2){ 
                $val .= "<fieldset>\n";
                $val .= $form->submit(Theme::icon('modify')." ".Language::title('modify'));
                $val .= "</fieldset>\n";
            }
        } else $val .= Language::title('noTeam');
        return $val;
    }
}
?>