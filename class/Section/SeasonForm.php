<?php 
/**
 * 
 * Class Season Form
 * Manage forms in season page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class SeasonForm
{
    public function __construct(){

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
}
?>