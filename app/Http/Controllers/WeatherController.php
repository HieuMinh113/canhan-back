<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Product;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $lat   = $request->input('lat');
        $lon   = $request->input('lon');
        $city  = $request->input('city', 'Ho Chi Minh');
        $apiKey = env('WEATHER_API_KEY');

        $query = ($lat && $lon) ? "{$lat},{$lon}" : $city;

        $response = Http::get("http://api.weatherapi.com/v1/current.json", [
            'key'  => $apiKey,
            'q'    => $query,
            'lang' => 'vi'
        ]);

        if ($response->successful()) {
            return response()->json($response->json()); 
        }

        return response()->json([
            'error'   => 'Không lấy được dữ liệu thời tiết',
            'details' => $response->body()
        ], $response->status());
    }

    public function suggestProducts(Request $request)
{
    $lat   = $request->input('lat');
    $lon   = $request->input('lon');
    $city  = $request->input('city', 'Ho Chi Minh');
    $apiKey = env('WEATHER_API_KEY');

    $query = ($lat && $lon) ? "{$lat},{$lon}" : $city;

    $response = Http::get("http://api.weatherapi.com/v1/current.json", [
        'key'  => $apiKey,
        'q'    => $query,
        'lang' => 'vi'
    ]);

    if (!$response->successful()) {
        return response()->json(['error' => 'Không lấy được thời tiết'], 500);
    }

    $weather   = $response->json();
    $temp      = $weather['current']['temp_c'] ?? null;
    $humidity  = $weather['current']['humidity'] ?? null;
    $condition = strtolower($weather['current']['condition']['text'] ?? '');

    // 🔑 Ánh xạ condition sang tag
    $weatherTagMap = [
        'nắng'       => 'sunny',
        'trời nắng'  => 'sunny',
        'có mây'     => 'sunny',
        'nhiều mây'  => 'rainy',
        'mưa'        => 'rainy',
        'mưa nhẹ'    => 'rainy',
        'mưa to'     => 'rainy',
        'tuyết'      => 'cold',
        'lạnh'       => 'cold',
    ];

    $tag = [];

    // ánh xạ trực tiếp từ condition
    foreach ($weatherTagMap as $key => $value) {
        if (str_contains($condition, $key)) {
            $tag[] = $value;
        }
    }

    // fallback theo nhiệt độ/độ ẩm
    if ($temp !== null) {
        if ($temp > 30) $tag[] = 'hot';
        elseif ($temp < 20) $tag[] = 'cold';
    }

    if ($humidity !== null && $humidity > 80) {
        $tag[] = 'rainy';
    }

    $tag = array_unique($tag);

    if (empty($tag)) {
        $products = Product::inRandomOrder()->take(7)->get();
    } else {
        $products = Product::whereIn('tag', $tag)->take(7)->get();
    }

    return response()->json([
        'weather'  => $weather,
        'tag'      => $tag,
        'products' => $products
    ]);
}
}
