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
        require '../lang/fr.php';
        $val = "  	<a href='/'>$title_homepage</a>";
        if($current == 'create'){
            $val .= "<a class='current' href='index.php?page=account&create=1'>$title_createAnAccount</a>";
        } else {
            $val .= "<a href='index.php?page=account&create=1'>$title_createAnAccount</a>";
        }
        $req = "SELECT id_fp_user, login
        FROM fp_user
        ORDER BY login;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();

        if($counter > 1){
            $val .= "<form action='index.php?page=account' method='POST'>\n";
            $val .= $form->inputAction('modify');
            $val .= $form->label($title_modifynAccount);
            $val .= $form->selectSubmit('id_fp_user', $data);
            $val .= "</form>\n";
        }
        return $val;
    }
    
    static function logonForm($pdo, $error, $form){
        //require '../lang/fr.php';
        $val = $error->getError();
        $val .= "<form action='index.php?page=account' method='POST'>\n";
        $val .= $form->inputAction('logon');
        $val .= $form->input($title_login, 'login');
        $val .= "<br />\n";
        $val .= $form->inputPassword($title_password, 'password');
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('logon'));
        $val .= "</form>\n";
        $val .= $title_createAnAccount;
        return $val;
    }

    static function logonPopup($pdo, $login, $password){
        $val = false;
        $req="SELECT * FROM fp_user
        WHERE login = :login;";
        $data = $pdo->prepare($req,['login' => $login]);
        $counter = $pdo->rowCount();
        if($counter == 1) {
            if(password_verify($password, $data->password)){
                $val = true;
                $_SESSION['userLogin'] = $login;
                $_SESSION['language'] = $data->language;
                $_SESSION['theme'] = $data->theme;
            }
        }
        return $val;
    }
    
    static function deletePopup($pdo, $id_fp_user){
        require '../lang/fr.php';
        $req="DELETE FROM fp_user
        WHERE id_fp_user='" . $id_fp_user . "';";
        $data=$pdo->exec($req);
        $pdo->alterAuto('fp_user');
        popup($title_deleted,"index.php?page=account");
    }
    
    static function createForm($error, $form){
        require '../lang/fr.php';
        $val = $error->getError();
        $val .= "<form action='index.php?page=account' method='POST'>\n";
        $val .= $form->inputAction('create');
        
        $val .= $form->input($title_login, 'login');
        $val .= "<br />\n";
        
        $val .= $form->input($title_login, 'password');
        $val .= "<br />\n";

        $val .= $form->input($title_login, 'email');
        $val .= "<br />\n";
        
        $val .= $form->submit($title_create);
        $val .= "</form>\n";
        return $val;
    }
    
    static function createPopup($pdo, $teamId, $playerId, $playerName, $playerFirstname, $playerPosition){
        require '../lang/fr.php';
        $pdo->alterAuto('season_team_player');
        $pdo->alterAuto('player');
        $req1="INSERT INTO player
        VALUES(NULL,:name,:firstname,:position);";
        $pdo->prepare($req1,[
            'name' => $playerName,
            'firstname' => $playerFirstname,
            'position' => $playerPosition
        ]);
        $playerId=$pdo->lastInsertId();
        $req2="INSERT INTO season_team_player VALUES(NULL,:id_season,:id_team,:id_player);";
        $pdo->prepare($req2,[
            'id_season' => $_SESSION['seasonId'],
            'id_team' => $teamId,
            'id_player' => $playerId
        ]);
        popup($title_created,"index.php?page=player");
    }
    
    static function modifyForm($pdo, $error, $form, $playerId){
        require '../lang/fr.php';
        $req ="SELECT j.id_player, j.name, j.firstname, j.position, c.id_team
        FROM player j
        LEFT JOIN season_team_player scj ON j.id_player=scj.id_player
        LEFT JOIN team c ON scj.id_team=c.id_team
        WHERE j.id_player=:id_player;";
        $data = $pdo->prepare($req,[
            'id_player' => $playerId
        ]);
        $val = $error->getError();
        $val .= "<form action='index.php?page=player' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= $form->inputHidden('id_player', $data->id_player);
        $val .= $form->input($title_name,'name');
        $val .= $form->input($title_firstname,'firstname');
        $val .= "<br />\n";
        $val .= $form->inputRadioPosition($data);
        $val .= "<br />\n";
        $val .= $form->selectTeam($pdo, null, $data->id_team);
        $val .= "<br />\n";
        $val .= $form->submit($title_modify);
        $val .= "</form>\n";
        $val .= "<br />\n";
        // Delete form
        $val .= $form->deleteForm('player', 'id_player', $playerId, false, 'id_team', $data->id_team);
        return $val;
    }
    
    static function modifyPopup($pdo, $teamId, $playerId, $playerName, $playerFirstname, $playerPosition){
        require '../lang/fr.php';
        // Check if the player is known in Season Team Player table
        $req = "SELECT COUNT(*) as nb
        FROM season_team_player
        WHERE id_season=:id_season
        AND id_team=:id_team
        AND id_player=:id_player;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_team' => $teamId,
            'id_player' => $playerId
        ],true);
        $req="UPDATE player
        SET name=:name, firstname=:firstname, position=:position
        WHERE id_player=:id_player;";
        if($data->nb==0){
            $req.="INSERT INTO season_team_player
            VALUES(NULL,:id_season,:id_team,:id_player);";
        }
        if($data[0]==1){
            $req.="UPDATE season_team_player SET id_season=:id_season,id_team=:id_team WHERE id_player=:id_player;";
        }
        $pdo->prepare($req,[
            'name' => $playerName,
            'firstname' => $playerFirstname,
            'position' => $playerPosition,
            'id_season' => $_SESSION['seasonId'],
            'id_team' => $teamId,
            'id_player' => $playerId
        ]);
        popup($title_modified,"index.php?page=player");
    }
    
    static function list($pdo){
        require '../lang/fr.php';
        $req = "SELECT COUNT(e.rating) as nb,AVG(e.rating) as rating,c.name as team,j.name,j.firstname
        FROM player j
        LEFT JOIN season_team_player scj ON j.id_player=scj.id_player
        LEFT JOIN team c ON  c.id_team=scj.id_team
        LEFT JOIN teamOfTheWeek e ON e.id_player=j.id_player
        GROUP BY team, j.name,j.firstname
        ORDER BY nb DESC, rating DESC,j.name,j.firstname LIMIT 0,3";
        $data = $pdo->prepare($req,null,true);
        $val = "<p>\n";
        $val .= "  <table>\n";
        $val .= "      <tr><th></th><th>$title_player</th><th>$title_team</th><th>$title_teamOfTheWeek</th><th>$title_ratingAverage</th></tr>\n";
        $counterPodium = 0;
        $icon = "&#129351;"; // Gold medal
        foreach ($data as $d)
        {
            $counterPodium++;
            if($counterPodium==2) $icon="&#129352;"; // Silver medal
            else $icon="&#129353;"; // Bronze medal
            
            $val .= "      <td><strong>".$counterPodium."</strong></td>\n";
            $val .= "      <td>".$icon." <strong>".mb_strtoupper($d->name,'UTF-8')." ".$d->firstname."</strong></td>\n";
            $val .= "      <td>".$d->team."</td>\n";
            $val .= "      <td>".$d->nb."</td>\n";
            $val .= "      <td>".round($d->rating,1)."</td>\n";
            $val .= "  </tr>\n";
        }
        $val .= "  </table>\n";
        $val .= "</p>\n";
        
        
        $val .= "<h3>$title_bestPlayersByTeam</h3>\n";
        
        $req = "SELECT COUNT(e.rating) as nb,AVG(e.rating) as rating,c.name as team,j.name,j.firstname
    FROM player j
    LEFT JOIN season_team_player scj ON j.id_player=scj.id_player
    LEFT JOIN team c ON  c.id_team=scj.id_team
    LEFT JOIN teamOfTheWeek e ON e.id_player=j.id_player
    GROUP BY team,j.name,j.firstname
    ORDER BY team ASC, nb DESC, rating DESC, j.name,j.firstname ASC";
        $data = $pdo->prepare($req,null,true);
        $val .= "  <table>\n";
        $val .= "      <tr><th>$title_team</th><th>$title_player</th><th>$title_teamOfTheWeek</th><th>$title_ratingAverage</th></tr>\n";
        $counter = "";
        foreach ($data as $d)
        {
            $val .= "      <td>";
            if($counter!=$d->team){
                $counterPodium = 0;
                $val .= "<strong>".$d->team."</strong>";
            }
            
            $counterPodium++;
            switch($counterPodium){
                case 1:
                    $icon = "&#129351;"; // gold medal
                    break;
                case 2:
                    $icon="&#129352;"; // silver medal
                    break;
                case 3:
                    $icon="&#129353;"; // bronze medal
                    break;
                default:
                    $icon="";
            }
            
            $val .= "</td><td>";
            if($icon!="") $val .= $icon." <strong>".mb_strtoupper($d->name,'UTF-8')." ".$d->firstname."</strong>";
            else $val .= mb_strtoupper($d->name,'UTF-8')." ".$d->firstname;
            $val .= "</td><td>".$d->nb."</td><td>".round($d->rating,1)."</td>\n";
            $val .= "  </tr>\n";
            $counter=$d->team;
        }
        
        $val .= "  </table>\n";
        return $val;
    }
}
?>