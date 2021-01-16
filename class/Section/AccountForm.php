<?php 
/**
 * 
 * Class Account Form
 * Manage forms account page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class AccountForm
{
    public function __construct(){

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
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('create')." "
                .Language::title('create'));
        $val .= "</form>\n";
        if(empty($_SESSION['install']) || $_SESSION['install']!='true') $val .= "<a href='index.php?page=account'>" . (Language::title('logon')) . "</a>\n";
        $val .= "</fieldset>\n";
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
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('enter')." "
            .Language::title('logon'));
        $val .= "</form>\n";
        $val .= "<a href='index.php?page=account&create=1'>" . (Language::title('createAnAccount')) . "</a>\n";
        $val .= "</fieldset>\n";
        return $val;
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

        $pattern = '~([a-z]{2}_[A-Z]{2})(?!\.pot)$~';
        $replacement = '$1';
        $subject = scandir('lang/locale');
        
        $dataLang = array_values(preg_filter($pattern, $replacement, $subject));
        
        $val .= $form->selectData('language', $dataLang, $_SESSION['language']);
        
        $req = "SELECT id_fp_theme, name FROM fp_theme;";
        $dataTheme = $pdo->query($req);
        $val .= $form->select('theme', $dataTheme, $_SESSION['themeId'], false);
        $val .= "</fieldset>\n";
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('modify')." "
                .Language::title('modify'));
        $val .= "</form>\n";
        $val .= $form->deleteForm('account', 'id_fp_user', $userId);     
        $val .= "</fieldset>\n";
        
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
            $val .= "<fieldset>\n";
            $val .= $form->submit(Language::title('modify'));
            $val .= "</form>\n";
            $val .= $form->deleteForm('account', 'id_fp_user', $userId);
            $val .= "<fieldset>\n";
        }
        return $val;
    }
}
?>