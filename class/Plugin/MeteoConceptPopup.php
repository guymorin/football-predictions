<?php 
/**
 * 
 * Class Meteo Concept Popup
 * Manage popups in meteo concept page
 */
namespace FootballPredictions\Plugin;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class MeteoConceptPopup
{
    public function __construct(){

    }
    
    static function modifyPopup($pdo, $url, $token){
        if($url!='' and $token!=''){
            $req="UPDATE plugin_meteo_concept_preferences
            SET url = '" . $url . "', token = '" . $token . "'
            WHERE id_plugin_meteo_concept_preferences = 1;";
            $pdo->exec($req);
        }
        popup(Language::title('modified'),"index.php?page=plugin_meteo_concept&modify=0");
    }

}
?>