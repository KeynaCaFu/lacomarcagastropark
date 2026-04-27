<?php

namespace App\Services;

use App\Models\PlazaConfig;

class LocationService
{
    /**
     * Valida si unas coordenadas están dentro del radio de la plaza
     */
    public function isWithinPlaza($userLat, $userLng): bool
    {
        $config = PlazaConfig::first();
        if (!$config) return true; // Si no hay configuración, no restringimos

        $distance = $this->calculateDistance(
            $userLat, $userLng, 
            $config->latitude, $config->longitude
        );

        return $distance <= $config->radius_meters;
    }

    /**
     * Fórmula de Haversine para calcular distancia en metros
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radio de la Tierra en metros

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}