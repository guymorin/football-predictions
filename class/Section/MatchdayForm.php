<?php 
/**
 * 
 * Class Matchday Form
 * Manage forms matchday page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Statistics;
use FootballPredictions\Theme;
use FootballPredictions\Forms;

class MatchdayForm
{
    public function __construct(){

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
}
?>