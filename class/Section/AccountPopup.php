<?php 
/**
 * 
 * Class Account Popup
 * Manage popups in account page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class AccountPopup
{
    public function __construct(){

    }
    
    static function createPopup($pdo, $userLogin, $userPassword){
        $userPassword = password_hash($userPassword, PASSWORD_DEFAULT);
        $pdo->alterAuto('fp_user');
        $role = 1;
        if(isset($_SESSION['install']) and $_SESSION['install'] == 'true') $role = 2;
        $req="INSERT INTO fp_user
        VALUES(NULL,:name,:password,'" . date('Y-m-d') . "',
        '" . $_SESSION['language'] . "',
        '1',NULL,NULL,'" . $role . "');";
        $pdo->prepare($req,[
            'name' => $userLogin,
            'password' => $userPassword
        ]);
        if(self::logonPopup($pdo, $userLogin, $userPassword)) {
            if(isset($_SESSION['install']) and $_SESSION['install'] == 'true'){
                popup(Language::title('installComplete'),"index.php?page=account&exit=1");
                $_SESSION['install']=false;
            } else popup(Language::title('created'),"index.php?page=account");
            
        }
    }
    
    static function deletePopup($pdo, $userId){
        
        $req="DELETE FROM fp_user
        WHERE id_fp_user='" . $userId . "';";
        $data=$pdo->exec($req);
        $pdo->alterAuto('fp_user');
        if($userId == $_SESSION['userId']) popup(Language::title('deleted'),"index.php?page=account&exit=1");
        else popup(Language::title('deleted'),"index.php?page=account");
    }
    
    static function logonPopup($pdo, $userLogin, $password){
        $val = false;
        $req="SELECT * FROM fp_user
        WHERE name = :name;";
        $data = $pdo->prepare($req,['name' => $userLogin]);
        $counter = $pdo->rowCount();
        if($counter == 1) {
            if(password_verify($password, $data->password)){
                $val = true;
                $_SESSION['userId'] = $data->id_fp_user;
                $_SESSION['userLogin'] = $userLogin;
                $_SESSION['language'] = $data->language;
                $_SESSION['themeId'] = $data->theme;
                $_SESSION['role'] = $data->role;
                $r = "SELECT directory_name FROM fp_theme
                WHERE id_fp_theme = $data->theme";
                $d = $pdo->queryObj($r);
                $_SESSION['directory_name'] = $d->directory_name;
                if($data->last_season!=null && $data->last_championship!=null) {
                    $req1 = "SELECT name FROM season WHERE id_season = '" . $data->last_season . "'";
                    $d1 = $pdo->prepare($req1,null);
                    $_SESSION['seasonId'] = $data->last_season;
                    $_SESSION['seasonName'] = $d1->name;
                    $req1 = "SELECT name FROM championship WHERE id_championship = '" . $data->last_championship . "'";
                    $d1 = $pdo->prepare($req1,null);
                    $_SESSION['championshipId'] = $data->last_championship;
                    $_SESSION['championshipName'] = $d1->name;
                }
            }
            $val = true;
        }
        return $val;
    }
    
    static function modifyPopup($pdo, $userId, $userLogin, $userPassword, $userLanguage, $userTheme){
        $userPassword = password_hash($userPassword, PASSWORD_DEFAULT);
        $req="UPDATE fp_user SET password = '" . $userPassword . "',
        language = '" . $userLanguage . "',
        theme = " . $userTheme . " 
        WHERE id_fp_user = '" . $userId . "';";
        $pdo->exec($req);
        $_SESSION['language'] = $userLanguage;
        $_SESSION['themeId'] = $userTheme;
        popup(Language::title('modified'),"index.php?page=account");
    }
    
    static function modifyUserPopup($pdo, $userId, $userRole){
        $req="UPDATE fp_user SET role = '" . $userRole . "' 
        WHERE id_fp_user = '" . $userId . "';";
        $pdo->exec($req);
        popup(Language::title('modified'),"index.php?page=account");
    }
}
?>