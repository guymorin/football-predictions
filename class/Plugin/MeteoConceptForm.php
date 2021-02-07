<?php 
/**
 * 
 * Class Meteo Concept Form
 * Manage forms meteo concept page
 */
namespace FootballPredictions\Plugin;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class MeteoConceptForm
{
    public function __construct(){

    }
    
    static function modifyForm($pdo, $error, $form){
        $val = '';
      
        $req = "SELECT * FROM plugin_meteo_concept_preferences
        WHERE id_plugin_meteo_concept_preferences=1;";
        $data = $pdo->prepare($req,[]);
        $val .= "<form action='index.php?page=plugin_meteo_concept' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . ucfirst('Meteo-Concept') . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input('Token', 'token', null, 100);
        $val .= $form->input('URL', 'url', null, 100);
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