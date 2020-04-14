<?php
/* This is the Football Predictions account section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Section\Account;

?>

<h2><?= "$icon_account $title_account";?></h2>
<h3><?= "$title_logon";?></h3>

<?php
// Values
$userId = 0;
$userLogin = $userPassword = "";
isset($_POST['id_fp_user'])  ? $userId=$error->check("Digit",$_POST['id_fp_user']) : null;
isset($_POST['login'])       ? $userLogin=$error->check("Alnum",$_POST['login'], $title_login) : null;

if(    
    (empty($_SESSION['userLogin']))
    &&($create == 0)
    &&($modify == 0)
    &&($delete == 0)
){
    if($logon == 0) echo Account::logonForm($pdo, $error, $form);
    elseif($logon == 1){
        if(Account::logonPopup($pdo, $userLogin, $userPassword)){
            $_SESSION['userLogin'] = $userLogin;
            header('Location:index.php?page=account');
        } else {
            $error->addError($title_logon,$title_error);
            echo  Account::logonForm($pdo, $error, $form);
        }
    }
}
// Logon

// Delete
elseif($delete == 1){
    echo $form->popupConfirm('account', 'id_fp_user', $usersId);
}
elseif($delete == 2){
    Account::deletePopup($pdo, $userId);
}
// Create
elseif($create == 1){
    echo "<h3>$title_createAnAccount</h3>\n";
    if($pdo->findName('user', $userLogin))  $error->addError($title_login, $title_errorExists);
    elseif($userLogin!="") Account::createPopup($pdo, $userLogin);
    echo Account::createForm($error, $form);
}
// Modify
elseif($modify == 1){
    echo "<h3>$title_modifyAnAccount</h3>\n";
    if($pdo->findName('user', $userLogin))  $error->addError($title_login, $title_errorExists);
    elseif($userLogin!="") Account::modifyPopup($pdo, $userLogin, $userId);
    echo Account::modifyForm($pdo, $error, $form, $userId);
}
// List
else {
    echo "<h3>".$_SESSION['userLogin']."</h3>\n";
    //echo Account::list($pdo);
}
?>
</section>