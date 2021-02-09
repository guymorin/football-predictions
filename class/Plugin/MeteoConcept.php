<?php 
/**
 * 
 * Class MeteoConcept
 * Manage MeteoConcept plugin
 */
namespace FootballPredictions\Plugin;
use FootballPredictions\Language;
class MeteoConcept
{
    public static $cloud;
    public static $cloudText;
    private static $team1Weather;
    private static $team2Weather;
    
    public function __construct(){
        
    }
    
    public static function getTeamWeather($number){
        if($number==1) return self::$team1Weather;
        if($number==2) return self::$team2Weather;
    }
    
    public static function setWeather($pdo, $diff, $mv1, $mv2, $weather_code){
        $req = "SELECT url, token
                FROM plugin_meteo_concept_preferences;";
        $data = $pdo->prepare($req,[]);
        $url = $data->url;
        $token = $data->token;
        
        // Call API
        $api=$url.$diff."?token=".$token."&insee=".$weather_code;
        $weatherData = file_get_contents($api);
        $rain=0;
        
        if ($weatherData !== false){
            $decoded = json_decode($weatherData);
            /*$city = $decoded->city;*/
            $forecast = $decoded->forecast;
            $rain = $forecast->rr1;
        }
        
        switch($rain){
            case ($rain==0):
                self::$cloud="&#x1F323;";// Sun
                self::$cloudText=Language::title('weatherSun');
                break;
            case ($rain>=0 && $rain<1):
                self::$cloud="&#x1F324;";// Low rain
                self::$cloudText=Language::title('weatherLowRain');
                $weather=0;
                // if market value of team 2 is higher then point for team 2 (low rain)
                if($mv2 > $mv1){
                    self::$team2Weather=$weather;
                    self::$team2Weather=0;
                }
                // else if market value is equal then point for both team
                elseif($mv2 == $mv1){
                    self::$team1Weather=$weather;
                    self::$team2Weather=$weather;
                }
                // else it means market value of team 1 is higher then point for team 1
                else {
                    self::$team1Weather=$weather;
                    self::$team2Weather=0;
                }
                break;
            case ($rain>=1&&$rain<3):
                self::$cloud="&#x1F326;";// Middle rain
                self::$cloudText=Language::title('weatherMiddleRain');
                $weather=1;
                // if market value of team 2 is higher then points for team 1
                if($mv2 > $mv1){
                    self::$team1Weather=$weather;
                    self::$team2Weather=0;
                }
                // else if market value is equal then points for both team
                elseif($mv2 == $mv1){
                    self::$team1Weather=$weather;
                    self::$team2Weather=$weather;
                }
                // else it means market value of team 1 is higher then points for team 2
                else {
                    self::$team1Weather=0;
                    self::$team2Weather=$weather;
                }
                break;
            case ($rain>=3):
                self::$cloud="&#x1F327;";//High rain
                self::$cloudText=Language::title('weatherHighRain');
                $weather=2;
                // if market value of team 2 is higher then points for team 1
                if($mv2 > $mv1){
                    self::$team1Weather=$weather;
                    self::$team2Weather=0;
                }
                // else if market value is equal then points for both team
                elseif($mv2 == $mv1){
                    self::$team1Weather=$weather;
                    self::$team2Weather=$weather;
                }
                // else it means market value of team 1 is higher then points for team 2
                else {
                    self::$team1Weather=0;
                    self::$team2Weather=$weather;
                }
                break;
        }
    }
}

?>