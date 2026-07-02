<?php
/**
 * Calculates the great-circle distance between two coordinates
 * using the Haversine formula.
 *
 * @param float $lat1 Latitude of point 1 (degrees)
 * @param float $lon1 Longitude of point 1 (degrees)
 * @param float $lat2 Latitude of point 2 (degrees)
 * @param float $lon2 Longitude of point 2 (degrees)
 * @return float Distance in kilometers
 */
function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadiusKm = 6371;

    // Convert degrees to radians
    $lat1Rad = deg2rad($lat1);
    $lon1Rad = deg2rad($lon1);
    $lat2Rad = deg2rad($lat2);
    $lon2Rad = deg2rad($lon2);

    $deltaLat = $lat2Rad - $lat1Rad;
    $deltaLon = $lon2Rad - $lon1Rad;

    // Haversine formula
    $a = sin($deltaLat / 2) ** 2 +
         cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distanceKm = $earthRadiusKm * $c;

    return round($distanceKm, 2);
}

/**
 * Estimates delivery time based on distance.
 * Assumes an average delivery speed (adjustable).
 *
 * @param float $distanceKm
 * @param float $avgSpeedKmh Average speed in km/h (default 20 km/h for city scooter delivery)
 * @return int Estimated minutes
 */
function estimateDeliveryMinutes($distanceKm, $avgSpeedKmh = 20) {
    $hours = $distanceKm / $avgSpeedKmh;
    $minutes = $hours * 60;

    // Add a fixed 10-minute buffer for food prep time
    $minutes += 10;

    return (int) ceil($minutes);
}
?>