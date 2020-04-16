<?php 
/**
 * 
 * Class Account
 * Manage Account page
 */
namespace FootballPredictions\Section;
use \PDO;
use FootballPredictions\Language;

class Account
{
    public function __construct(){

    }
    
    static function exitButton() {
        if(isset($_SESSION['userLogin'])){
            echo "<a class='session' href='index.php?page=account&exit=1'>".$_SESSION['userLogin']." &#10060;</a>";
        }
    }
    static function submenu($pdo, $form, $current = null){
        
        $val = "  	<a href='/'>Language::title('homepage')</a>";
        if($current == 'create'){
            $val .= "<a class='current' href='index.php?page=account&create=1'>Language::title('createAnAccount')</a>";
        } else {
            $val .= "<a href='index.php?page=account&create=1'>Language::title('createAnAccount')</a>";
        }
        $req = "SELECT id_fp_user, name
        FROM fp_user
        ORDER BY name;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();

        if($counter > 1){
            $val .= "<form action='index.php?page=account' method='POST'>\n";
            $val .= $form->inputAction('modify');
            $val .= $form->label(Language::title('modifyAccount'));
            $val .= $form->selectSubmit('id_fp_user', $data);
            $val .= "</form>\n";
        }
        return $val;
    }
    
    static function logonForm($pdo, $error, $form){
        //
        $val = $error->getError();
        $val .= "<form action='index.php?page=account' method='POST'>\n";
        $val .= $form->inputAction('logon');
        $val .= $form->input(Language::title('login'), 'name');
        $val .= "<br />\n";
        $val .= $form->inputPassword(Language::title('password'), 'password');
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('logon'));
        $val .= "</form>\n";
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
                $_SESSION['theme'] = $data->theme;
                $_SESSION['role'] = $data->role;
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
        return $val;
    }
    
    static function deletePopup($pdo, $id_fp_user){
        
        $req="DELETE FROM fp_user
        WHERE id_fp_user='" . $id_fp_user . "';";
        $data=$pdo->exec($req);
        $pdo->alterAuto('fp_user');
        popup(Language::title('deleted'),"index.php?page=account");
    }
    
    static function createForm($error, $form){
        $val = $error->getError();
        $val .= "<form action='index.php?page=account' method='POST'>\n";
        $val .= $form->inputAction('create');
        
        $val .= $form->input(Language::title('login'), 'name');
        $val .= "<br />\n";

        $val .= $form->inputPassword(Language::title('password'), 'password');
        $val .= "<br />\n";

        $val .= $form->input(Language::title('emailAddress'), 'email');
        $val .= "<br />\n";
        
        $val .= $form->submit(Language::title('create'));
        $val .= "</form>\n";
        $val .= "<a href='index.php?page=account'>" . (Language::title('logon')) . "</a>\n";
        return $val;
    }
    
    static function createPopup($pdo, $userLogin, $userPassword){
        $userPassword = password_hash($userPassword, PASSWORD_DEFAULT);
        $pdo->alterAuto('fp_user');
        $req="INSERT INTO fp_user
        VALUES(NULL,:name,:password,'','" . date('Y-m-d') . "','" . $_SESSION['language'] . "','1');";
        $pdo->prepare($req,[
            'name' => $userLogin,
            'password' => $userPassword
        ]);
        popup(Language::title('created'),"index.php?page=account");
    }
    
    static function modifyForm($pdo, $error, $form, $userId){
        $val = '';
        return $val;
    }
    
    static function modifyPopup($pdo, $userId, $userName){

    }
    
    static function list($pdo){
        
        $req = "SELECT * FROM fp_user;";
        $data = $pdo->prepare($req,null);
        $val = "";
        $val .= "<div id='circle'>" . (substr($data->name,0,1)) . "</div>\n";
        $val .= "<p><a href='index.php?page=account&exit=1'>" . (Language::title('logoff')) . "</a></p>\n";
        
        return $val;
    }
}
?>