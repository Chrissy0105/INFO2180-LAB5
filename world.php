<?php
$host = 'localhost';
$username = 'lab5_user';
$password = 'password123'; // Replace with your MySQL password
$dbname = 'world';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $country = isset($_GET['country']) ? trim($_GET['country']) : '';
    $lookup = isset($_GET['lookup']) ? $_GET['lookup'] : 'country';

    if ($lookup === 'cities') {
        // Lookup cities
        $stmt = $conn->prepare("
            SELECT c.name AS city, c.district, c.population
            FROM cities c
            JOIN countries cs ON c.country_code = cs.code
            WHERE cs.name LIKE :country
            ORDER BY c.population DESC
        ");
        $stmt->execute(['country' => "%$country%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            echo "<table id='results-table'>";
            echo "<tr><th>City</th><th>District</th><th>Population</th></tr>";
            foreach ($results as $row) {
                $popClass = '';
                if ($row['population'] > 100000) {
                    $popClass = 'high-pop';
                } elseif ($row['population'] < 20000) {
                    $popClass = 'low-pop';
                }

                echo "<tr>";
                echo "<td data-label='City'>{$row['city']}</td>";
                echo "<td data-label='District'>{$row['district']}</td>";
                echo "<td data-label='Population' class='$popClass'>{$row['population']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No cities found for '$country'.";
        }

    } else {
        // Lookup countries
        $stmt = $conn->prepare("
            SELECT name, continent, independence_year, head_of_state, population
            FROM countries
            WHERE name LIKE :country
            ORDER BY population DESC
        ");
        $stmt->execute(['country' => "%$country%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            echo "<table id='results-table'>";
            echo "<tr><th>Country</th><th>Continent</th><th>Independence Year</th><th>Head of State</th><th>Population</th></tr>";
            foreach ($results as $row) {
                $yearClass = '';
                if (!empty($row['independence_year'])) {
                    if ($row['independence_year'] < 1900) {
                        $yearClass = 'old-country';
                    } elseif ($row['independence_year'] >= 2000) {
                        $yearClass = 'new-country';
                    }
                }

                echo "<tr>";
                echo "<td data-label='Country'>{$row['name']}</td>";
                echo "<td data-label='Continent'>{$row['continent']}</td>";
                echo "<td data-label='Independence Year' class='$yearClass'>{$row['independence_year']}</td>";
                echo "<td data-label='Head of State'>{$row['head_of_state']}</td>";
                // Population bar
                $popPercent = min(100, $row['population'] / 1000000); // scale for bar
                echo "<td data-label='Population'>
                        <div class='bar-container'>
                            <div class='bar' style='width: {$popPercent}%;'></div>
                            <span>{$row['population']}</span>
                        </div>
                      </td>";
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
