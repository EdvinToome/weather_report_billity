
<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8'");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

define("eventTimes", [20,23,02,05]);
define("desiredWeatherType", "Rain");
$coordinates = array(
    "Tallinn" => array(59.4717, 24.7382),
    "Tartu" => array(58.377240, 26.729420),
    "Narva" => array(59.380000, 28.200000),
    "Pärnu" => array(58.380000, 24.470000),
    "Jõhvi" => array(59.350000, 27.400000),
    "Jõgeva" => array(58.750000, 26.350000),
    "Põlva" => array(58.060000, 27.060000),
    "Valga" => array(57.783333, 26.050000)
);


$returnJson = array();
foreach($coordinates as $cityName => $cityCoordinates) {
$apiCallUrl = "https://api.openweathermap.org/data/2.5/forecast?lat=" . $cityCoordinates[0] . "&lon=" . $cityCoordinates[1] . "&appid=f63a322a0db5dbdc7af8118b77ad7797";
$weatherData = file_get_contents($apiCallUrl);
$weatherJson = json_decode($weatherData, true);

foreach ($weatherJson['list'] as $value) {
    #Get time in Tallinn timezone
    $dt = new DateTime("@" . $value['dt']);
    $dt->setTimeZone(new DateTimeZone('Europe/Tallinn'));
    if (in_array($dt->format('H'), eventTimes) && $value['weather'][0]['main'] == desiredWeatherType) {
         $event = array("city" => $cityName, "Date and Time" => $value['dt_txt'], "weather" => $value['weather'][0]['description'] );
        $returnJson[] = $event;
    }
}
}
echo json_encode($returnJson);

?>
