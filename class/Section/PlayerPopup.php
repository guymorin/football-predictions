<?php 
/**
 * 
 * Class Player Popup
 * Manage popups in player page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use \PDO;

class PlayerPopup
{
    public function __construct(){

    }
    
    static function createPopup($pdo, $teamId, $playerId, $playerName, $playerFirstname, $playerPosition){
        
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
        popup(Language::title('created'),"index.php?page=player");
    }
    
    static function deletePopup($pdo, $teamId, $playerId){
        $req = '';
        $req .= "DELETE FROM teamOfTheWeek
        WHERE id_player=:id_player;";
        $req .= "DELETE FROM season_team_player
        WHERE id_player=:id_player;";
        $req .= "DELETE FROM player
        WHERE id_player=:id_player;";
        $data = $pdo->prepare($req,[
            'id_player' => $playerId
        ]);
        $pdo->alterAuto('teamOfTheWeek');
        $pdo->alterAuto('season_team_player');
        $pdo->alterAuto('player');
        popup(Language::title('deleted'),"index.php?page=player");
    }
    
    static function modifyPopup($pdo, $teamId, $playerId, $playerName, $playerFirstname, $playerPosition){
        
        // Check if the player is known in Season Team Player table
        $req = "SELECT * 
        FROM season_team_player
        WHERE id_season=:id_season
        AND id_team=:id_team
        AND id_player=:id_player;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_team' => $teamId,
            'id_player' => $playerId
        ],true);
        $counter = $pdo->rowCount();
        
        $req="UPDATE player
        SET name=:name, firstname=:firstname, position=:position
        WHERE id_player=:id_player;";
        if($counter==0){
            $req.="INSERT INTO season_team_player
            VALUES(NULL,:id_season,:id_team,:id_player);";
        } else {
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
        popup(Language::title('modified'),"index.php?page=player");
    }
}
?>