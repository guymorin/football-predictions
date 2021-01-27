<?php 
/**
 * 
 * Class Preferences Popup
 * Manage popups in preferences page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class PreferencesPopup
{
    public function __construct(){

    }
    
    static function createPopup($name, $pdo){
        $pdo->alterAuto('fp_preferences');
        $req="INSERT INTO fp_preferences
        VALUES(NULL,:name);";
        $pdo->prepare($req,[
            'name' => $name
        ]);
        popup(Language::title('created'),"index.php?page=account&create=1");
    }
    
    static function modifyPopup($name, $pdo){
        // Delete all values
        $req = "DELETE FROM fp_preferences;";
        $data = $pdo->prepare($req,[],true);
        // Create the new value
        $pdo->alterAuto('fp_preferences');
        $req="INSERT INTO fp_preferences
        VALUES(NULL,:name);";
        $pdo->prepare($req,[
            'name' => $name
        ]);
        popup(Language::title('modified'),"index.php?page=preferences");
    }
}
?>