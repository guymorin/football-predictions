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
    
    public function __construct($db_host, $db_name, $db_user, $db_password){
        $this->db_host = $db_host;
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
    }
    
    private function getPDO(){
        /* Include to connect the database */
        if($this->db==null){
            try
            {
                $db = new PDO(
               "mysql:host=$this->db_host;dbname=phpmyadmin;charset=utf8",
                $this->db_user,
                $this->db_pass);
            }
            catch (PDOException $e)
            {
                die("<div class='error'>".$title_error." : ". $e->getMessage()."</div>");
            }
            $this->db = $db;
        }
        return $this->$db;
    }
    
    public function query($req, $classname){
        $response = $this->getPDO()->query($req);
        $data = $response->fetchAll(PDO::FETCH_CLASS, $classname);
        return $data;
    }
    
    public function prepare($req, $attributes, $one=false){
        $response = $this->getPDO()->prepare($req);
        $response->execute($attributes);
        $response->setFetchMode(PDO::FETCH_CLASS,$classname);
        if($one==true){
            $data = $response->fetchAll();
        } else {
            $data = $response->fetch();
        }
        return $data;
    }
}
?>
