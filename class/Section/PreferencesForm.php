<?php 
/**
 * 
 * Class Preferences Form
 * Manage forms in preferences page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class PreferencesForm
{
    public function __construct(){

    }
    
    static function createForm($error, $form){
        $val = '';
        $val .= "<form action='index.php?page=preferences' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('Preferences')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input(Language::title('websiteName'), 'name');
        $val .= "<br />\n";
        $val .= "</fieldset>\n";
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('create')." ".Language::title('create'));
        $val .= "</fieldset>\n";
        $val .= "</form>\n";
        return $val;
    }
    
    static function modifyForm($pdo, $error, $form){
        $req ="SELECT name FROM fp_preferences LIMIT 0,1;";
        $data = $pdo->prepare($req,[]);
        $val = '';
        $val .= "<form action='index.php?page=preferences' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('Preferences')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input(Language::title('websiteName'),'name');
        $val .= "<br />\n";
        $val .= "</fieldset>\n";
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('modify')." ".Language::title('modify'));
        $val .= "</fieldset>\n";
        $val .= "</form>\n";
        return $val;
    }

}
?>