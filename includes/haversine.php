<?php
/**
 * Calculates the great-circle distance between two coordinates
 * using the Haversine formula.
 */
function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadiusKm = 6371;

    $lat1Rad = deg2rad($lat1);
    $lon1Rad = deg2rad($lon1);
    $lat2Rad = deg2rad($lat2);
    $lon2Rad = deg2rad($lon2);

    $deltaLat = $lat2Rad - $lat1Rad;
    $deltaLon = $lon2Rad - $lon1Rad;

    $a = sin($deltaLat / 2) ** 2 +
         cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distanceKm = $earthRadiusKm * $c;

    return round($distanceKm, 2);
}

/**
 * Guesses how bad traffic is right now, based on the hour.
 * 1.0 = normal traffic (no slowdown)
 * higher number = worse traffic = slower delivery
 */
function getTrafficMultiplier() {
    $hour = (int) date('H'); // current hour, 0 to 23

    if ($hour >= 8 && $hour < 10) {
        return 1.5; // morning rush
    }
    if ($hour >= 17 && $hour < 20) {
        return 1.6; // evening rush (worst)
    }
    if ($hour >= 12 && $hour < 14) {
        return 1.2; // lunch time
    }
    return 1.0; // normal
}

/**
 * Estimates delivery time based on distance and current traffic.
 */
function estimateDeliveryMinutes($distanceKm, $avgSpeedKmh = 20) {
    $hours = $distanceKm / $avgSpeedKmh;
    $minutes = $hours * 60;

    // slow down the time based on traffic
    $minutes *= getTrafficMultiplier();

    // add 10 min buffer for food prep
    $minutes += 10;

    return (int) ceil($minutes);
}
?>