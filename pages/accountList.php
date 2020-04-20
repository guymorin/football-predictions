<?php
/* This is the Football Predictions list of accounts section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\App;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Section\Account;
App::exitNoAdmin();
?>

<h2><?= Theme::icon('account') . ' ' . (Language::title('account'));?></h2>
<h3><?= Language::title('listAccounts');?></h3>

<?= Account::list($pdo, $form); ?>