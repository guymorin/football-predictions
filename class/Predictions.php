<?php
/**
 *
 * Class Predictions
 * Generate predictions elements
 */
namespace FootballPredictions;
use \PDO;
use FootballPredictions\Section\Matchday;

class Predictions
{
    private $win;
    
    /**
     *
     * @param array $data Form or database data
     */
    public function __construct(){
    }
    
    static function switchButton($form,$type){
        // Switch form
        $val = "<form id='criterion' action='index.php?page=prediction' method='POST'>\n";
        $icon = Theme::icon('switch')." ";
        switch($type){
            case "toAuto":
                $val .= $form->inputHidden('manual','0');
                $val .= $form->submit($icon.Language::title('swithToAuto'));
                break;
            case "toManual":
                $val .= $form->inputHidden('modify','2');
                $val .= $form->inputHidden('manual','1');
                $val .= $form->submit($icon.Language::title('swithToManual'));
                break;
            default:
        }
        $val .= "</form>\n";
        $val .= "<br />\n";
        return $val;
    }
}
?>
