<?php
$host = 'localhost';
$username = 'lab5_user';
$password = 'password123'; // Your lab MySQL password
$dbname = 'world';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $country = isset($_GET['country']) ? trim($_GET['country']) : '';
    $lookup = isset($_GET['lookup']) ? $_GET['lookup'] : 'country';

    if ($lookup === 'cities') {
        // Cities query
        $stmt = $conn->prepare("
            SELECT c.name AS city, c.district, c.population
            FROM cities c
            JOIN countries cs ON c.country_code = cs.code
            WHERE cs.name LIKE :country
        ");
        $stmt->execute(['country' => "%$country%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            echo "<table border='1'>";
            echo "<tr><th>City</th><th>District</th><th>Population</th></tr>";
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td>{$row['city']}</td>";
                echo "<td>{$row['district']}</td>";
                echo "<td>{$row['population']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No cities found for '$country'.";
        }

    } else {
        // Country query
        $stmt = $conn->prepare("
            SELECT name, continent, independence_year, head_of_state
            FROM countries
            WHERE name LIKE :country
        ");
        $stmt->execute(['country' => "%$country%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            echo "<table border='1'>";
            echo "<tr><th>Country</th><th>Continent</th><th>Independence Year</th><th>Head of State</th></tr>";
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['continent']}</td>";
                echo "<td>{$row['independence_year']}</td>";
                echo "<td>{$row['head_of_state']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No countries found matching '$country'.";
        }
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

