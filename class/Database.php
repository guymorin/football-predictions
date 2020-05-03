<?php 
/**
 *
 * Class Database
 * Connect to database
 *
 */
namespace FootballPredictions;
use FootballPredictions\Language;
use \PDO;
use \PDOException;

class Database
{
    private $db_host;
    private $db_name;
    private $db_user;
    private $db_pass;
    private $db;
    private $response;
    
    public function __construct($db_host, $db_name, $db_user, $db_pass){
        $this->db_host = $db_host;
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
    }
    
    private function getPDO(){
        /* Include to connect the database */
        if($this->db==null){
            try{
                $db = new PDO(
                "mysql:dbname=$this->db_name;host=$this->db_host;charset=utf8",
                $this->db_user,
                $this->db_pass
                );
            } catch (PDOException $e) {
                die("<div class='error'>".Language::title('error')." : ". $e->getMessage()."</div>");
            }
            $this->db = $db;
        }
        return $this->db;
    }

    public function alterAuto($table){
        $this->getPDO()->exec("ALTER TABLE " . $table . " AUTO_INCREMENT=0;");
    }
  
    public function exec($req){
        $this->getPDO()->exec($req);
    }

    public function findName($table, $name){
        $val = false;
        $req = "SELECT * FROM " . $table . " WHERE name= '" . $name . "';";
        $this->query($req);
        $counter = $this->rowCount();
        if($counter > 0) $val = true;
        return $val;
    }
    
    public function lastInsertId(){
        return $this->getPDO()->lastInsertId();
    }
    
    public function prepare($req, $attributes=null, $all=false){
        $val = $this->getPDO()->prepare($req);
        $val->execute($attributes);
        $this->response = $val;
        $val->setFetchMode(PDO::FETCH_OBJ);
        if($all==true){
            $val = $val->fetchAll();
        } else {
            $val = $val->fetch();
        }
        return $val;
    }
    
    public function query($req){
        $val = $this->getPDO()->query($req);
        $this->response = $val;
        return $val;
    }
    
    public function queryObj($req){
        $val = $this->query($req);
        $val = $val->fetch(PDO::FETCH_OBJ);
        return $val;
    }

    public function rowCount(){
        $val = $this->response->rowCount();
        return $val;
    }
    
    public function export(){
        $mysqlExportPath = "data/" . date("Ymd") . "_dump.sql";
        $command = 'mysqldump --opt -h' .$this->db_host .' -u' .$this->db_user .' -p' .$this->db_pass .' ' .$this->db_name .' > ' .$mysqlExportPath;
        $worked = null;
        $output = array();
        $val = '';
        exec($command, $output, $worked);
        switch($worked){
            case 0:
                $val .= Language::title('saved');
                break;
            case 1:
                $val .= Language::title('error') . " : " . Language::title('errorExport');
                break;
            case 2:
                $val .= Language::title('error') . " : " . Language::title('errorPath');
                break;
        }
        //$val .= '<br />' . getcwd() . '/' . $mysqlExportPath;
        return $val;
    }
    public function import($host, $name, $user, $pass){
        $mysqlImportFilename = "data/create.sql";
        $command = 'mysql -h' .$host . ' -u' .$user . ' -p' .$pass . ' ' .$name .' < ' .$mysqlImportFilename;

        $worked = null;
        $output = array();
        $val = '';
        exec($command, $output, $worked);
        switch($worked){
            case 0:
                $val .= Language::title('created');
                break;
            case 1:
                $val .= Language::title('createDatabase') . " : " . Language::title('errorImport');
                break;
        }
        return $val;
    }
}
?>
