<?php
use FootballPredictions\Theme;

function getMessage($val,$count){
    switch($val){
        case "account":
    		$val = _("Account");
    		break;
        case "addTeamTo":
    		$val = _("Add team to ");
    		break;
        case "added":
    		$val = _("Added");
    		break;
        case "administration":
    		$val = _("administration");
    		break;
        case "administrator":
    		$val = _("administrator");
    		break;
        case "away":
    		$val = _("Away");
    		break;
        case "bestPlayers":
    		$val = _("Best players");
    		break;
        case "bestPlayersByTeam":
    		$val = _("Best players by team");
    		break;
        case "bet":
            $val = ngettext("Bet","Bets",$count);
    		break;
        case "betValueText":
    		$val = _("Good bet value");
    		break;
        case "careful":
    		$val = _("Careful game!");
    		break;
        case "carefulToo":
    		$val = _("Too careful game!");
    		break;
        case "championship":
    		$val = _("Championship");
    		break;
        case "contributor":
    		$val = _("contributor");
    		break;
        case "create":
    		Theme::icon('create')." ".$val = _("Create");
    		break;
        case "created":
    		$val = _("Created");
    		break;
        case "createAChampionship":
    		$val = _("Create a championship");
    		break;
        case "createAMatch":
    		$val = _("Create a match");
    		break;
        case "createAMatchday":
    		$val = _("Create a matchday");
    		break;
        case "createAnAccount":
    		$val = _("Create an account");
    		break;
        case "createAnAdmin":
    		$val = _("Create an administrator account");
    		break;
        case "createAPlayer":
    		$val = _("Create a player");
    		break;
        case "createASeason":
    		$val = _("Create a season");
    		break;
        case "createATeam":
    		$val = _("Create a team");
    		break;
        case "createDatabase":
    		$val = _("Create the database");
    		break;
        case "createTheMatchdays":
    		$val = _("Create the matchdays");
    		break;
        case "criterionSum":
    		$val = _("Criterion sum");
    		break;
        case "currentForm":
    		$val = _("Current form");
    		break;
        case "currentFormText":
    		$val = _("Victory in the last match (+1)");
    		break;
        case "dashboard":
    		$val = _("Dashboard");
    		break;
        case "date":
    		$val = _("Date");
    		break;
        case "defender":
    		$val = _("Defender");
    		break;
        case "delete":
    		Theme::icon('delete')." ".$val = _("Delete");
    		break;
        case "deleted":
    		$val = _("Deleted");
    		break;
        case "draw":
    		$val = _("D");
    		break;
        case "earning":
            $val = ngettext("Earning","Earnings",$count);
    		break;
        case "earningByBet":
    		$val = _("Earning by bet");
    		break;
        case "emailAddress":
    		$val = _("E-mail address");
    		break;
        case "error":
    		$val = _("Error");
    		break;
        case "errorAlnum":
    		$val = _("alphanumeric characters only");
    		break;
        case "errorConnection":
    		$val = _("problem with the database connection");
    		break;
        case "errorDate":
    		$val = _("invalid date");
    		break;
        case "errorDigit":
    		$val = _("integer numeric characters only");
    		break;
        case "errorExists":
    		$val = _("this name already exists");
    		break;
        case "errorExport":
    		$val = _("problem with the export");
    		break;
        case "errorFields":
    		$val = _("some fields have not been filled in correctly");
    		break;
        case "errorImport":
    		$val = _("problem with the import");
    		break;
        case "errorNotField":
    		$val = _("field not filled");
    		break;
        case "errorNum":
    		$val = _("numeric characters only");
    		break;
        case "errorPassword":
    		$val = _("invalid username or password");
    		break;
        case "errorPasswordNoMatch":
    		$val = _("those passwords didn't match");
    		break;
        case "errorPath":
    		$val = _("invalid path");
    		break;
        case "errorPosition":
    		$val = _("invalid position");
    		break;
        case "errorResult":
    		$val = _("invalid result");
    		break;
        case "errorSeasonName":
    		$val = _("9 characters max.");
    		break;
        case "errorWritable":
    		$val = _("Writable access needed on the installation repository");
    		break;
        case "exit":
    		$val = _("Exit");
    		break;
        case "exited":
    		$val = _("Exited");
    		break;
        case "firstname":
    		$val = _("Firstname");
    		break;
        case "forward":
    		$val = _("Forward");
    		break;
        case "general":
    		$val = _("General");
    		break;
        case "goalkeeper":
    		$val = _("Goalkeeper");
    		break;
        case "home":
    		$val = _("Home");
    		break;
        case "homeAwayText":
    		$val = _("The standing of the home or away team is good or bad (+ 1 / -1)");
    		break;
        case "homepage":
    		$val = _("Homepage");
    		break;
        case "install":
    		$val = _("Install");
    		break;
        case "installComplete":
            $val = _("Installation complete");
            break;
        case "installHost":
    		$val = _("Host");
    		break;
        case "installName":
    		$val = _("Name");
    		break;
        case "installUser":
    		$val = _("User");
    		break;
        case "installPass":
    		$val = _("Password");
    		break;
        case "language":
    		$val = _("Language");
    		break;
        case "listAccounts":
    		$val = _("List of accounts");
    		break;
        case "lastSave":
    		$val = _("Last backup");
    		break;
        case "listChampionships":
    		$val = _("List of championship");
    		break;
        case "listMatchdays":
    		$val = _("List of matchdays");
    		break;
        case "login":
    		$val = _("Username");
    		break;
        case "logon":
    		$val = _("Log on");
    		break;
        case "logoff":
    		$val = _("Log off");
    		break;
        case "lose":
    		$val = _("L");
    		break;
        case "marketValue":
    		$val = _("Marketvalue");
    		break;
        case "marketValueText":
    		$val = _("The market value of the team is hight or low (+N)");
    		break;
        case "matchday":
    		$val = _("Matchday");
    		break;
        case "matchdayNext":
    		$val = _("Next matchday");
    		break;
        case "matchdays":
    		$val = _("Matchdays");
    		break;
        case "matchgame":
    		$val = _("Matchgame");
    		break;
        case "matchNumber":
    		$val = _("Number of matchgame");
    		break;
        case "matchPlayed":
    		$val = _("Played matchgames");
    		break;
        case "MD":
    		$val = _("MD");
    		break;
        case "midfielder":
    		$val = _("Midfielder");
    		break;
        case "modify":
    		Theme::icon('modify')." ".$val = _("Modify");
    		break;
        case "modified":
    		$val = _("Modified");
    		break;
        case "modifyAChampionship":
    		$val = _("Modify a championship");
    		break;
        case "modifyAMatch":
    		$val = _("Modify a matchgame");
    		break;
        case "modifyAnAccount":
    		$val = _("Modify an account");
    		break;
        case "modifyAPlayer":
    		$val = _("Modify a player");
    		break;
        case "modifyPredictions":
    		$val = _("Modify predictions");
    		break;
        case "modifyASeason":
    		$val = _("Modify a season");
    		break;
        case "modifyATeam":
    		$val = _("Modify a team");
    		break;
        case "modifyAMatchday":
    		$val = _("Modify a matchday");
    		break;
        case "motivation":
    		$val = _("Motivation");
    		break;
        case "myAccount":
    		$val = _("My account");
    		break;
        case "name":
    		$val = _("Name");
    		break;
        case "next":
    		$val = _("Next");
    		break;
        case "no":
    		$val = _("No");
    		break;
        case "notPlayed":
    		$val = _("Not played");
    		break;
        case "noChampionship":
    		$val = _("No championship");
    		break;
        case "noMatch":
    		$val = _("No matchgame");
    		break;
        case "noMatchday":
    		$val = _("No matchday");
    		break;
        case "noSeason":
    		$val = _("No season");
    		break;
        case "noTeam":
    		$val = _("No team");
    		break;
        case "number":
    		$val = _("Number");
    		break;
        case "matchdayNumber":
    		$val = _("Number of matchdays");
    		break;
        case "odds":
    		$val = _("Odds");
    		break;
        case "oddsAveragePlayed":
    		$val = _("Average played odds");
    		break;
        case "password":
    		$val = _("Password");
    		break;
        case "passwordConfirm":
    		$val = _("Confirm password");
    		break;
        case "physicalForm":
    		$val = _("Physical form");
    		break;
        case "player":
    		$val = _("Player");
    		break;
        case "position":
    		$val = _("Position");
    		break;
        case "prediction":
    		$val = _("Prediction");
    		break;
        case "predictions":
    		$val = _("Predictions");
    		break;
        case "predictionsHistory":
    		$val = _("Predictions history");
    		break;
        case "predictionsHistoryText":
    		$val = _("Old results on the same criteria");
    		break;
        case "previous":
    		$val = _("Previous");
    		break;
        case "profit":
            $val = _("Profit");
            break;
        case "profitSum":
            $val = _("Profit sum");
            break;
        case "profitByMatchday":
    		$val = _("Profit by matchday");
    		break;
        case "profitEvolution":
    		$val = _("Profit evolution");
    		break;
        case "pts":
    		$val = _("Pts");
    		break;
        case "quickNav":
    		$val = _("Quick nav");
    		break;
        case "rating":
    		$val = _("Rating");
    		break;
        case "ratingAverage":
    		$val = _("Average rating");
    		break;
        case "redCards":
    		$val = _("Red cards");
    		break;
        case "result":
    		$val = _("Result");
    		break;
        case "results":
    		$val = _("Results");
    		break;
        case "ROI":
    		$val = _("ROI");
    		break;
        case "ROIisLosing":
    		$val = _("ROI is losing!");
    		break;
        case "ROIisNeutral":
    		$val = _("ROI is neutral!");
    		break;
        case "ROIwins":
    		$val = _("ROI wins!");
    		break;
        case "ROIisExcellent":
    		$val = _("ROI is excellent!");
    		break;
        case "role":
    		$val = _("Role");
    		break;
        case "save":
    		$val = _("Save");
    		break;
        case "saved":
    		$val = _("Saved");
    		break;
        case "season":
    		$val = _("Season");
    		break;
        case "select":
    		$val = _("Select");
    		break;
        case "selectAnotherSeason":
    		$val = _("Select another season");
    		break;
        case "selected":
    		$val = _("Selected");
    		break;
        case "selectTheChampionship":
    		$val = _("Select a championship");
    		break;
        case "selectTheMatchday":
    		$val = _("Select a matchday");
    		break;
        case "selectThePlayer":
    		$val = _("Select a player");
    		break;
        case "selectTheSeason":
    		$val = _("Select a season");
    		break;
        case "selectTheTeams":
    		$val = _("Select the teams");
    		break;
        case "speculative":
    		$val = _("Speculative game!");
    		break;
        case "speculativeToo":
    		$val = _("Too speculative game!");
    		break;
        case "statistics":
    		$val = _("Statistics");
    		break;
        case "standing":
    		$val = _("Standing");
    		break;
        case "success":
            $val = ngettext("Success","Successes",$count);
            break;
        case "successRate":
    		$val = _("Success rate");
    		break;
        case "swithToAuto":
    		$val = _("Switch to semi-automatical");
    		break;
        case "swithToManual":
    		$val = _("Switch to manual mode");
    		break;
        case "team":
    		$val = _("Team");
    		break;
        case "teams":
    		$val = _("Teams");
    		break;
        case "teamOfTheWeek":
    		$val = _("Team of the week");
    		break;
        case "theme":
    		$val = _("Theme");
    		break;
        case "trend":
    		$val = _("Trend");
    		break;
        case "trendText":
    		$val = _("In the last three matches, if team 1 has min 5 points and team 2 has max 1 point (+1/-1)");
    		break;
        case "site":
    		$val = _("FP");
    		break;
        case "siteSubTitle":
    		$val = _("Football predictions");
    		break;
        case "siteData":
    		$val = _("Website datas");
    		break;
        case "yes":
    		$val = _("Yes");
    		break;
        case "warningExists":
    		$val = _("Warning: this name already exists!");
            break;
        case "weather":
    		$val = _("Weather");
    		break;
        case "weatherHighRain":
    		$val = _("High rain favors the less technical team (+2)");
    		break;
        case "weatherLowRain":
    		$val = _("Low rain is neutral (0)");
    		break;
        case "weatherMiddleRain":
    		$val = _("Middle rain favors the less technical team (+1)");
    		break;
        case "weatherSun":
    		$val = _("Sun is neutral (0)");
    		break;
        case "weathercode":
    		$val = _("Weather code");
    		break;
        case "win":
    		$val = _("W");
    		break;
        default:
    }
    return $val;
}
?>