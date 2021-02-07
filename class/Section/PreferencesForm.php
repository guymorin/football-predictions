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
        $req ="SELECT id_plugin, plugin_name, plugin_desc, activate FROM plugin;";
        $data = $pdo->prepare($req,[],true);
        $counter = $pdo->rowCount();
        if($counter>0){
            $val .= "<table>";
            $val .= "<tr>\n";
            $val .= "   <th colspan='3'>Plugins</th>\n";
            $val .= "</tr>\n";
            foreach ($data as $d)
            {
                $val .= "<tr>\n";
                $val .= $form->inputHidden('id_plugin['.$d->id_plugin.']',$d->id_plugin);
                $val .= "   <td>" 
                    . $form->inputCheckBox('activate['.$d->id_plugin.']', '1', $d->activate)
                        . "</td>\n";
                $val .= "   <td><strong>" . $d->plugin_name . "</strong></td>\n";
                $val .= "   <td>" . $d->plugin_desc . "</td>\n";
                $val .= "</tr>\n";
            }
            $val .= "</table>";
        }
        $val .= "</fieldset>\n";
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('modify')." ".Language::title('modify'));
        $val .= "</fieldset>\n";
        $val .= "</form>\n";
        return $val;
    }

}
?>