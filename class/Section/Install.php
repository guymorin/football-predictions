<?php 
/**
 * 
 * Class Install
 * Database install page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Database;
use \PDO;
use FootballPredictions\Theme;

class Install
{
    public function __construct(){

    }
    
    
    static function createForm($error, $form){
        $val = '';
        $val .= "<form action='index.php?page=install' method='POST'>\n";
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('install')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input(Language::title('installHost'), 'DbHost');
        $val .= "<br />\n";
        $val .= $form->input(Language::title('installName'), 'DbName');
        $val .= "<br />\n";
        $val .= $form->input(Language::title('installUser'), 'DbUser');
        $val .= "<br />\n";
        $val .= $form->input(Language::title('installPass'), 'DbPass');
        $val .= "<br />\n";
        $val .= "</fieldset>\n";
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('create')." ".Language::title('create'));
        $val .= "</fieldset>\n";
        $val .= "</form>\n";
        return $val;
    }
    
    static function createPopup($host, $name, $user, $pass){
        $val = '';
        $test = Database::import($host, $name, $user, $pass);
        $val .= $test;
        if($test == (Language::title('created'))){
            $info = "<?php\n";
            $info .= "    \$DB_HOST='" . $host . "';\n";
            $info .= "    \$DB_NAME='" . $name . "';\n";
            $info .= "    \$DB_USER='" . $user . "';\n";
            $info .= "    \$DB_PASS='" . $pass . "';\n";
            $info .= "?>";
            $file='install/AppConnect.inc';
            file_put_contents($file, $info);
        }
        $_SESSION['install'] = 'true';
        popup($val,"index.php?page=account&create=1");
    }
}
?>