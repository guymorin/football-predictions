<?php 
/**
 *
 * Class Database
 * Connect to database
 *
 */
namespace FootballPredictions;
use \PDO;
use \PDOException;

class Database
{
    private $db_host;
    private $db_name;
    private $db_user;
    private $db_pass;
    private $db;
    
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
                "mysql:host=$this->db_host;dbname=phpmyadmin;charset=utf8",
                $this->db_user,
                $this->db_pass
                );
            } catch (PDOException $e) {
                die("<div class='error'>".$title_error." : ". $e->getMessage()."</div>");
            }
            $this->db = $db;
        }
        return $this->db;
    }
    
    public function findName($table, $name){
        $val = false;
        $req = "SELECT * FROM " . $table . " WHERE name= '" . $name . "';";
        if($this->rowCount($req)>0) $val = true;
        return $val;
    }

    public function alterAuto($table){
        $this->getPDO()->exec("ALTER TABLE " . $table . " AUTO_INCREMENT=0;");
    }
  
    public function exec($req){
        $this->getPDO()->exec($req);
    }
    
    public function lastInsertId(){
        return $this->getPDO()->lastInsertId();
    }
    
    public function prepare($req, $attributes=null, $all=false){
        $response = $this->getPDO()->prepare($req);
        $response->execute($attributes);
        $response->setFetchMode(PDO::FETCH_OBJ);
        if($all==true){
            $data = $response->fetchAll();
        } else {
            $data = $response->fetch();
        }
        return $data;
    }
    
    public function query($req){
        $val = $this->getPDO()->query($req);
        return $val;
    }
    
    public function queryObj($req){
        $val = $this->query($req);
        $val = $val->fetch(PDO::FETCH_OBJ);
        return $val;
    }

    
    public function rowCount($req){
        $val = $this->getPDO()->query($req);
        $val = $val->rowCount();
        return $val;
    }

}
?>
