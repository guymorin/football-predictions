<?php
require 'vendor/autoload.php';

// Namespaces
use FootballPredictions\App;
use FootballPredictions\Language;
$pdo = App::getDb();
if(isset($_POST['name'])){
    $name = $_POST['name'];
    $name = ucfirst(strtolower($name));
    $req = "SELECT * FROM player WHERE name=:name";
    $pdo->prepare($req,[
        'name' => $name        
    ],true);
    $counter = $pdo->rowCount();
    
    $response = null;
    if($counter > 0){
            $response = "<span style='color: red;'>" . (Language::title('warningExists')) . "</span>";  
    }
    
    echo $response;
    die;
}
?>