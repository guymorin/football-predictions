<?php 
/**
 * 
 * Class Account
 * Manage Account page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class Account
{
    public function __construct(){

    }
    
    static function title(){
        $val = "<h2>";
        $val .= Theme::icon('account') . " " . Language::title('account');
        $val .= "</h2>\n";
        return $val;
    }
    
    static function titleInstall(){
        $val = "<h2>";
        if(isset($_SESSION['install']) and $_SESSION['install'] == 'true')  $val .= Theme::icon('admin'). " " . Language::title('install')." 3/3";
        else                                $val .= Language::title('homepage');
        $val .= "</h2>\n";
        $val .= "<h3>";
        if(isset($_SESSION['install']) and $_SESSION['install'] == 'true')  $val .= Language::title('createAnAdmin');
        else                                $val .= Language::title('createAnAccount');
        $val .= "</h3>\n";
        return $val;
    }
    
    static function titleModify($pdo, $error, $form, $userId){
        $val = '';
        if($userId == $_SESSION['userId']){
            $val .= "<h3>" . Language::title('myAccount') . "</h3>\n";
            $val .= AccountForm::modifyForm($pdo, $error, $form, $userId);
        } else {
            $val .= "<h3>" . Language::title('modifyAnAccount') . "</h3>\n";
            $val .= AccountForm::modifyUserForm($pdo, $error, $form, $userId);
        }
        return $val;
    }
    
    static function list($pdo, $form){
        $req = "SELECT id_fp_user, name, role, registration 
        FROM fp_user ORDER BY registration DESC";
        $data = $pdo->prepare($req,null,true);
        $val = "<table>\n";
        $val .= "  <tr>\n";
        $val .= "      <th>" . (Language::title('date')) . "</th>\n";
        $val .= "      <th>" . (Language::title('login')) . "</th>\n";
        $val .= "      <th>" . (Language::title('role')) . "</th>\n";
        $val .= "  </tr>\n";
        
        foreach ($data as $d)
        {
            if($d->name == $_SESSION['userLogin']) {
                $val .= "  <tr class='current'>\n";
            } else {
                $val .= "  <tr>\n";
            }
            $val .= "      <td>" . $d->registration . "</td>\n";
            $val .= "<form id='" . ($d->id_fp_user) . "' action='index.php?page=account' method='POST'>\n";
            $val .= $form->inputAction("modifyuser");
            $val .= $form->inputHidden("id_fp_user", $d->id_fp_user);
            $val .= "      <td>";
            if($d->name != $_SESSION['userLogin']) $val .= "<a href='#' onclick='document.getElementById(" . ($d->id_fp_user) . ").submit();'>";
            $val .= ucfirst($d->name);
            if($d->name != $_SESSION['userLogin']) $val .= "</a></td>\n";
            $req = "SELECT name FROM fp_role WHERE id_fp_role = '" . $d->role . "';";
            $dataRole = $pdo->prepare($req);
            $val .= "      <td>" . Language::title($dataRole->name) . "</small></td>\n";
            $val .= "</form>\n";
            $val .= "  </tr>\n";
        }
        $val .= "</table>\n";
        return $val;
    }
}
?>