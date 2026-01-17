<?php 

namespace App;

class Helpers
{
    /** *  
     * @param array<int, int> $numbers 
     * */
    public static function getAverage(array $array)
    {
        if(count($array)) {
            $sum = 0;
            foreach($array as $num) {
                if (is_int($num) || is_float($num)) {
                    $sum += $num;
                }
            }
            return $sum/count($array);
        }
        return null;
    }

    public static function randomAlphaNumeric(int $length): string
    {
        $length = max(0, $length);
        if ($length === 0) return '';

        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabetLength = strlen($alphabet);

        $bytes = random_bytes($length);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $alphabet[ord($bytes[$i]) % $alphabetLength];
        }

        return $result;
    }

    public static function joinWords(array $words): string
    {
        $count = count($words);
    
        if ($count === 0) return '';
        if ($count === 1) return $words[0];
        if ($count === 2) return $words[0] . ' and ' . $words[1];
    
        // For 3 or more
        $last = array_pop($words);
        return implode(', ', $words) . ' and ' . $last;
    }

    public static function getCoordinatesFromGoogle($locationName)
    {
        if (!$locationName) {
            return [null, null];
        }
    
        $apiKey = env('GOOGLE_MAPS_API_KEY'); // store key in .env
    
        try {
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($locationName) . "&key={$apiKey}";
            $response = file_get_contents($url);
    
            if (!$response) {
                return [null, null];
            }
    
            $json = json_decode($response, true);
    
            if (!empty($json['results'][0]['geometry']['location'])) {
                $loc = $json['results'][0]['geometry']['location'];
                return [$loc['lng'], $loc['lat']];
            }
        } catch (\Exception $e) {
            return [null, null];
        }
    
        return [null, null];
    }
    
    
    /**
     * Generate a random coordinate within a given radius from the provided lat/long.
     *
     * @param float|null $lat
     * @param float|null $lng
     * @param float $radiusKm The radius around the location to generate coordinates.
     * @return array [longitude|null, latitude|null]
     */
    public static function getCoordinatesWithinLocation($lat = null, $lng = null, $radiusKm = 1.0)
    {
        // If no usable coordinate is supplied, return null
        if ($lat === null || $lng === null) {
            return [null, null];
        }

        // Convert radius from Km to degrees
        $radiusInDegrees = $radiusKm / 111.32; // approx conversion factor

        // Generate a random point within the radius
        $u = (float) mt_rand() / mt_getrandmax();
        $v = (float) mt_rand() / mt_getrandmax();

        $w = $radiusInDegrees * sqrt($u);
        $t = 2 * pi() * $v;

        // Offset adjustments
        $latOffset = $w * cos($t);
        $lngOffset = $w * sin($t) / cos(deg2rad($lat));

        // Final randomized coordinates
        $randomLat = $lat + $latOffset;
        $randomLng = $lng + $lngOffset;

        return [$randomLng, $randomLat];
    }

    public static function generatePhoneNumber()
    {
        // Allowed prefixes (digits 1–3)
        $prefixes = ["080", "081", "070", "090"];
        $prefix = $prefixes[array_rand($prefixes)];
    
        // 4th digit allowed: 2–9
        $fourthDigit = rand(2, 9);
    
        // Remaining digits (positions 5–11) -> 7 digits
        $lastSeven = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
    
        // Combine them
        return $prefix . $fourthDigit . $lastSeven;
    }

    
}