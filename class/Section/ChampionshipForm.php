<?php 
/**
 * 
 * Class Championship Form
 * Manage forms in championship page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Statistics;

class ChampionshipForm
{
    public function __construct(){

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
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('create')." "
                .Language::title('create'));
        $val .= "</fieldset>\n";
        $val .= "</form>\n"; 
        return $val;
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
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('modify')." "
                .Language::title('modify'));
        $val .= "</form>\n";
        $val .= $form->deleteForm('championship', 'id_championship', $championshipId);
        $val .= "</fieldset>\n";
        return $val;
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
            $val .= "<fieldset>\n";
            $val .= $form->submit(Theme::icon('add')." "
                .Language::title('select'));
            $val .= "</fieldset>\n";
            $val .= "</form>\n";
        } else {
            $val .= "  <h4>" . (Language::title('noTeam')) . "</h4>\n";
            // Create if admin
            if(($_SESSION['role'])==2){
                $val .= "   <form action='index.php?page=team&create=1' method='POST'>\n";
                $val .= "<fieldset>\n";
                $val .= "            <button type='submit'>"
                    . Theme::icon('create'). " "
                        . Language::title('createATeam')
                        . "</button>\n";
                        $val .= "</fieldset>\n";
                        $val .= "   </form>\n";
            }
        }
        return $val;
    }
}
?>