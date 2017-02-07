<?php

require_once '../vendor/autoload.php';

if ($_GET['h'] !== "cBN6kE4Hn8j691AmL9GV3y42YJs7EQ3QFDBAADf3vlhGt90u148vUPe4U0LL7yCu") exit();

use OpenCFP\Application;
use OpenCFP\Environment;

$basePath = realpath(dirname(__DIR__));
$environment = Environment::fromEnvironmentVariable();

$app = new Application($basePath, $environment);

$db = $app['db'];


header("Content-Type: text/plain");

$fp = fopen("php://output", 'w');

$header = false;

#if (isset($_GET['selected_speakers'])) {
#    $sql = "SELECT CONVERT(CONVERT(CONVERT(u.first_name USING latin1) USING binary) USING utf8mb4) AS first_name, CONVERT(CONVERT(CONVERT(u.last_name USING latin1) USING binary) USING utf8mb4) AS last_name, CONVERT(CONVERT(CONVERT(u.company USING latin1) USING binary) USING utf8mb4) AS company, u.email, u.twitter, u.airport, u.transportation, u.hotel, u.request_mentor, COUNT(t.id) AS num_talks, GROUP_CONCAT(CONCAT('\"', CONVERT(CONVERT(CONVERT(t.title USING latin1) USING binary) USING utf8mb4), '\" (', IF(t.type='regular', '50m talk', t.type), ')') SEPARATOR ', ') AS talk_titles FROM users u JOIN talks t ON t.user_id=u.id AND t.selected=1 GROUP BY u.id";
#} else if (isset($_GET['rejected_speakers'])) {
#    $sql = "SELECT CONVERT(CONVERT(CONVERT(u.first_name USING latin1) USING binary) USING utf8mb4) AS first_name, CONVERT(CONVERT(CONVERT(u.last_name USING latin1) USING binary) USING utf8mb4) AS last_name, CONVERT(CONVERT(CONVERT(u.company USING latin1) USING binary) USING utf8mb4) AS company, u.email FROM users u JOIN talks t ON t.user_id=u.id GROUP BY u.id HAVING SUM(t.selected)=0";
#} else {
#    $sql = "SELECT t.id AS talk_id, t.*, u.* FROM talks t JOIN users u ON u.id=t.user_id WHERE t.selected=1 ORDER BY t.category, t.type, t.title";
#}



$result = $db->query($sql, PDO::FETCH_NAMED);
foreach ($result as $row) {
    unset($row['id']);
    unset($row['updated_at']);
    unset($row['created_at']);
	if (isset($row['company'])) $row['company'] = str_replace("&amp;", "&", $row['company']);
	if (isset($row['talk_titles'])) $row['talk_titles'] = str_replace("&amp;", "&", $row['talk_titles']);
	if (isset($row['title'])) $row['title'] = str_replace("&amp;", "&", $row['title']);
    if (!$header) {
        fputcsv($fp, array_keys($row));
        $header = true;
    }
    fputcsv($fp, array_values($row));
}

