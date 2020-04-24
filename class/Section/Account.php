<?php 
/**
 * 
 * Class Account
 * Manage Account page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use \PDO;

class Account
{
    public function __construct(){

    }
    
    static function exitButton() {
        if(isset($_SESSION['userLogin'])){
            echo "<a class='session' href='index.php?page=account&exit=1'>".ucfirst($_SESSION['userLogin'])." &#10060;</a>";
        }
    }
    static function submenu($pdo, $form, $current = null){
        $val = "  	<a href='/'>" . (Language::title('homepage')) . "</a>";
        $currentClass = " class='current'";
        $classAL = $classMA = '';
        switch($current){
            case 'listAccounts':
                $classAL = $currentClass;
                break;
            case 'myAccount':
                $classMA = $currentClass;
                break;
        }
        if(($_SESSION['role'])==2) $val .= "<a" 
                    . $classAL 
                    . " href='index.php?page=accountList'>" 
                    . (Language::title('listAccounts')) 
                    . "</a>";
        
        $val .= "<a" . $classMA . " href='index.php?page=account'>" . (Language::title('myAccount')) . "</a>";
        if(($_SESSION['role'])==2){
            $req = "SELECT id_fp_user, name
            FROM fp_user 
            ORDER BY name;";
            $data = $pdo->query($req);
            $counter = $pdo->rowCount();
            
            if($counter > 1){
                $val .= "<form action='index.php?page=account' method='POST'>\n";
                $val .= $form->inputAction('modifyuser');
                $val .= $form->label(Language::title('modifyAnAccount'));
                $val .= $form->selectSubmit('id_fp_user', $data);
                $val .= "</form>\n";
            }
        }
        return $val;
    }
    
    static function logonForm($pdo, $error, $form){
        //
        $val = '';
        
        $val .= "<form action='index.php?page=account' method='POST'>\n";
        $val .= $form->inputAction('logon');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('account')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input(Language::title('login'), 'name');
        $val .= "<br />\n";
        $val .= $form->inputPassword(Language::title('password'), 'password');
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('logon'));
        $val .= "</form>\n";
        $val .= "<br />\n";
        $val .= "<a href='index.php?page=account&create=1'>" . (Language::title('createAnAccount')) . "</a>\n";
        return $val;
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
                $r = "SELECT directory_name FROM fp_theme
                WHERE id_fp_theme = $data->theme";
                $d = $pdo->queryObj($r);
                $_SESSION['directory_name'] = $d->directory_name;
                $_SESSION['role'] = $data->role;
                if($data->last_season!=null) {
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
        }
        return $val;
    }
    
    static function deletePopup($pdo, $userId){
        
        $req="DELETE FROM fp_user
        WHERE id_fp_user='" . $userId . "';";
        $data=$pdo->exec($req);
        $pdo->alterAuto('fp_user');
        if($userId == $_SESSION['userId']) popup(Language::title('deleted'),"index.php?page=account&exit=1");
        else popup(Language::title('deleted'),"index.php?page=account");
    }
    
    static function createForm($error, $form){
        $val = '';
        $val .= "<form action='index.php?page=account' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('account')) . "</legend>\n";
        $val .= $error->getError();
        
        $val .= $form->input(Language::title('login'), 'name');
        $val .= "<br />\n";
                $val .= $form->inputPassword(Language::title('password'), 'password');
        $val .= "<br />\n";
        $val .= $form->inputPassword(Language::title('passwordConfirm'), 'password2');
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        
        $val .= $form->submit(Language::title('create'));
        $val .= "</form>\n";
        $val .= "<br />\n";
        $val .= "<a href='index.php?page=account'>" . (Language::title('logon')) . "</a>\n";
        return $val;
    }
    
    static function createPopup($pdo, $userLogin, $userPassword){
        $userPassword = password_hash($userPassword, PASSWORD_DEFAULT);
        $pdo->alterAuto('fp_user');
        $req="INSERT INTO fp_user
        VALUES(NULL,:name,:password,'" . date('Y-m-d') . "',
        '" . $_SESSION['language'] . "',
        '1',NULL,NULL,'1');";
        $pdo->prepare($req,[
            'name' => $userLogin,
            'password' => $userPassword
        ]);
        if(self::logonPopup($pdo, $userLogin, $password)) {
            popup(Language::title('created'),"index.php?page=account");
        }
    }
    
    static function modifyForm($pdo, $error, $form, $userId){
        $val = '';
        $req = "SELECT * FROM fp_user WHERE id_fp_user=:id_fp_user;";
        $data = $pdo->prepare($req,[
            'id_fp_user' => $userId
        ]);
        $val .= "<form action='index.php?page=account' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('account')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->inputHidden('id_fp_user',$userId);
        $val .= $form->inputHidden('name',$data->name);
        $val .= "<p class='center'>" . $form->label(Language::title('login')) . ucfirst($_SESSION['userLogin']) . "</p>\n";
        $form->setValue('password','');
        $val .= $form->inputPassword(Language::title('password'), 'password');
        $val .= "<br />\n";

        $pattern = '~([a-z][a-z])\.(php)$~';
        $replacement = '$1';
        $subject = scandir('lang/');
        $dataLang = array_values(preg_filter($pattern, $replacement, $subject));
        $val .= $form->selectData('language', $dataLang, $_SESSION['language']);
        
        $req = "SELECT id_fp_theme, name FROM fp_theme;";
        $dataTheme = $pdo->query($req);
        $val .= $form->select('theme', $dataTheme, $_SESSION['themeId'], false);
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('modify'));
        $val .= "</form>\n";
        $val .= "<br />\n";
        $val .= $form->deleteForm('account', 'id_fp_user', $userId);     
        
        $val .= "<p><a href='index.php?page=account&exit=1'>" . (Language::title('logoff')) . "</a></p>\n";
        
        return $val;
    }
    
    static function modifyUserForm($pdo, $error, $form, $userId){
        $val = '';
        $req = "SELECT * FROM fp_user WHERE id_fp_user=:id_fp_user;";
        $data = $pdo->prepare($req,[
            'id_fp_user' => $userId
        ]);
        
        if(($_SESSION['role'])==2){
            $val .= "<form action='index.php?page=account' method='POST'>\n";
            $form->setValues($data);
            $val .= $form->inputAction('modifyuser');
            $val .= "<fieldset>\n";
            $val .= "<legend>" . (Language::title('account')) . "</legend>\n";
            $val .= $error->getError();
            $val .= $form->inputHidden('id_fp_user',$userId);
            $val .= "<p class='center'>".$form->label(Language::title('login')) . ucfirst($data->name) . "</p>\n";
            $val .= "<br />\n";
            $req = "SELECT id_fp_role, name FROM fp_role;";
            $dataRole = $pdo->query($req);
            $val .= $form->select('role', $dataRole, $data->role);
            $val .= "</fieldset>\n";
            $val .= "<br />\n";
            $val .= $form->submit(Language::title('modify'));
            $val .= "</form>\n";
            $val .= "<br />\n";
            $val .= $form->deleteForm('account', 'id_fp_user', $userId);
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
    
    static function list($pdo, $form){
        $req = "SELECT id_fp_user, name, role, registration 
        FROM fp_user ORDER BY name";
        $data = $pdo->prepare($req,null,true);
        $val = "<table>\n";
        $val .= "  <tr>\n";
        $val .= "      <th>" . (Language::title('login')) . "</th>\n";
        $val .= "      <th>" . (Language::title('role')) . "</th>\n";
        $val .= "      <th>" . (Language::title('date')) . "</th>\n";
        $val .= "  </tr>\n";
        
        foreach ($data as $d)
        {
            if($d->name == $_SESSION['userLogin']) {
                $val .= "  <tr class='current'>\n";
            } else {
                $val .= "  <tr>\n";
            }
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
            $val .= "      <td>" . $d->registration . "</td>\n";
            $val .= "</form>\n";
            $val .= "  </tr>\n";
        }
        $val .= "</table>\n";
        return $val;
    }
}
?>