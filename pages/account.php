<?php
/* This is the Football Predictions account section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\App;
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Section\Account;

?>

<h2><?= Theme::icon('account') . ' ' . (Language::title('account'));?></h2>

<?php
// Values
$userId = 0;
$userLogin = $userPassword = $userPassword2 = $userLanguage = $userTheme = $userRole = "";
isset($_POST['id_fp_user'])  ? $userId = $error->check("Digit",$_POST['id_fp_user']) : null;
isset($_POST['name'])        ? $userLogin = $error->check("Alnum",$_POST['name'], Language::title('login')) : null;
isset($_POST['password'])    ? $userPassword = $_POST['password'] : null;
isset($_POST['password2'])   ? $userPassword2 = $_POST['password2'] : null;
isset($_POST['language'])    ? $userLanguage = $_POST['language'] : null;
isset($_POST['theme'])       ? $userTheme = $_POST['theme'] : null;
isset($_POST['role'])        ? $userRole = $_POST['role'] : null;

$userLogin = strtolower($userLogin);

// Logon
if(    
    (empty($_SESSION['userLogin']))
    &&($create == 0)
    &&($modify == 0)
    &&($delete == 0)
){
    echo "<h3>" . (Language::title('logon')) . "</h3>\n";
    if($logon == 0) echo Account::logonForm($pdo, $error, $form);
    elseif($logon == 1){
        if(Account::logonPopup($pdo, $userLogin, $userPassword)){
            header('Location:index.php');
        } elseif($userLogin != '') {
            $error->setError(Language::title('errorPassword'));
        }
        echo  Account::logonForm($pdo, $error, $form);
    }
}
// Create
elseif($create == 1){
    echo "<h3>" . (Language::title('createAnAccount')) . "</h3>\n";
    
    if($pdo->findName('fp_user', $userLogin))  $error->addError(Language::title('login'), Language::title('errorExists'));
    elseif($userPassword != $userPassword2) $error->addError(Language::title('password'), Language::title('errorPasswordNoMatch'));
    elseif($userLogin!="") Account::createPopup($pdo, $userLogin, $userPassword);
    echo Account::createForm($error, $form);
}

// Delete / Modify
elseif($delete == 1  || $delete == 2 || $modify == 1 || $modifyuser == 1){
    App::exitNoAdmin();
    if($userId == $_SESSION['userId']){
        echo "<h3>" . (Language::title('myAccount')) . "</h3>\n";
        echo Account::modifyForm($pdo, $error, $form, $userId);
    } else {
        echo "<h3>" . (Language::title('modifyAnAccount')) . "</h3>\n";
        echo Account::modifyUserForm($pdo, $error, $form, $userId);
    }

    if($delete == 1) echo $form->popupConfirm('account', 'id_fp_user', $userId);
    elseif($delete == 2) Account::deletePopup($pdo, $userId);
    elseif($modify == 1){
        if($userLogin!="") Account::modifyPopup($pdo, $userId, $userLogin, $userPassword, $userLanguage, $userTheme);
    }
    elseif($modifyuser == 1){
        if($userRole!="") Account::modifyUserPopup($pdo, $userId, $userRole);
    } 
// List
} else {
    echo "<h3>" . (Language::title('myAccount')) . "</h3>\n";
    echo Account::modifyForm($pdo, $error, $form, $_SESSION['userId']);
}
?>