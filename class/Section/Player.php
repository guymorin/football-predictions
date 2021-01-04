<?php 
/**
 * 
 * Class Player
 * Manage Player page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use \PDO;

class Player
{
    public function __construct(){

    }
    
    static function list($pdo){
        
        $req = "SELECT COUNT(e.rating) as nb,AVG(e.rating) as rating,c.name as team,j.name,j.firstname
        FROM player j
        LEFT JOIN season_team_player scj ON j.id_player=scj.id_player
        LEFT JOIN team c ON  c.id_team=scj.id_team
        LEFT JOIN teamOfTheWeek e ON e.id_player=j.id_player
        LEFT JOIN season_championship_team sct ON sct.id_team=scj.id_team 
        LEFT JOIN matchday md ON md.id_matchday = e.id_matchday 
        WHERE scj.id_season='".$_SESSION['seasonId']."'  
        AND sct.id_season='".$_SESSION['seasonId']."' 
        AND sct.id_championship='".$_SESSION['championshipId']."'  
        AND md.id_season ='".$_SESSION['seasonId']."'
        GROUP BY team, j.name,j.firstname
        ORDER BY nb DESC, rating DESC,j.name,j.firstname LIMIT 0,3";
        $data = $pdo->prepare($req,null,true);
        $val = "<p>\n";
        $val .= "  <table class='player'>\n";
        $val .= "      <tr><th></th><th>" . (Language::title('player')) . "</th><th>" . (Language::title('team')) . "</th><th>" . (Language::title('teamOfTheWeek')) . "</th><th>" . (Language::title('ratingAverage')) . "</th></tr>\n";
        $counterPodium = 0;
        $icon =  Theme::icon('medalGold'); // Gold medal
        $iconPlayer = Theme::icon('player')." "; // Player icon
        $counter = $pdo->rowCount();
        if($counter>0){
            foreach ($data as $d)
            {
                $counterPodium++;
                if($counterPodium==2) $icon = Theme::icon('medalSilver'); // Silver medal
                elseif($counterPodium==3) $icon = Theme::icon('medalBronze'); // Bronze medal
                
                $val .= "      <td><strong>".$counterPodium."</strong></td>\n";
                $val .= "      <td>".$icon." ".$iconPlayer." ".mb_strtoupper($d->name,'UTF-8')." ".$d->firstname."</td>\n";
                $val .= "      <td>".Theme::icon('team')."&nbsp;".$d->team."</td>\n";
                $val .= "      <td>".$d->nb."</td>\n";
                $val .= "      <td>".round($d->rating,1)."</td>\n";
                $val .= "  </tr>\n";
            }
        } else {
            $val .= "        <tr>\n";
            $val .= "<td colspan='5'>" . Language::title('notPlayed') . "</td>\n";
            $val .= "        </tr>\n";
        }
        $val .= "  </table>\n";
        $val .= "</p>\n";
        
        
        $val .= "<h3>" . (Language::title('bestPlayersByTeam')) . "</h3>\n";
        
        $req = "SELECT COUNT(e.rating) as nb,AVG(e.rating) as rating,c.name as team,j.name,j.firstname
    FROM player j
    LEFT JOIN season_team_player scj ON j.id_player=scj.id_player
    LEFT JOIN team c ON  c.id_team=scj.id_team 
    LEFT JOIN teamOfTheWeek e ON e.id_player=j.id_player 
    LEFT JOIN matchday md ON md.id_matchday=e.id_matchday 
    WHERE   scj.id_season = :id_season
    AND md.id_season = :id_season 
    AND c.id_team IN (SELECT id_team FROM season_championship_team 
        WHERE id_season = :id_season AND id_championship = :id_championship)
    GROUP BY team,j.name,j.firstname
    ORDER BY team ASC, nb DESC, rating DESC, j.name,j.firstname ASC";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId']
        ],true);
        $val .= "  <table class='playerTeam'>\n";
        $val .= "      <tr><th>" . (Language::title('team')) . "</th><th>" . (Language::title('player')) . "</th><th>" . (Language::title('teamOfTheWeek')) . "</th><th>" . (Language::title('ratingAverage')) . "</th></tr>\n";
        $counter = "";
        $counterPlayers = $pdo->rowCount();
        if($counterPlayers>0){
            foreach ($data as $d)
            {
                $val .= "      <td>";
                if($counter!=$d->team){
                    $counterPodium = 0;
                    $val .= Theme::icon('team')."&nbsp;".$d->team;
                }
                
                $counterPodium++;
                switch($counterPodium){
                    case 1:
                        $icon = Theme::icon('medalGold');
                        break;
                    case 2:
                        $icon= Theme::icon('medalSilver');
                        break;
                    case 3:
                        $icon= Theme::icon('medalBronze');
                        break;
                    default:
                        $icon="";
                }
                
                $val .= "</td><td>";
                if($icon != '') $val .= $icon." ";
                $val .= $iconPlayer." ".mb_strtoupper($d->name,'UTF-8')." ".$d->firstname;
                $val .= "</td><td>".$d->nb."</td><td>".round($d->rating,1)."</td>\n";
                $val .= "  </tr>\n";
                $counter=$d->team;
            }
        } else {
            $val .= "        <tr>\n";
            $val .= "<td colspan='4'>" . Language::title('notPlayed') . "</td>\n";
            $val .= "        </tr>\n";
        }
        $val .= "  </table>\n";
        return $val;
    }
}
?>