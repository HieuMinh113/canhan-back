<?php

return [
    'ranks' => [
        'bronze' => [
            'min_points' => 0,
            'discount' => 0,        
            'point_rate' => 0.1,    
        ],
        'silver' => [
            'min_points' => 1000,
            'discount' => 5,     
            'point_rate' => 0.1,
        ],
        'gold' => [
            'min_points' => 5000,
            'discount' => 6,     
            'point_rate' => 0.15,   
        ],
        'platinum' => [
            'min_points' => 10000,
            'discount' => 7,
            'point_rate' => 0.2,    
        ],
        'diamond' => [
            'min_points' => 20000,
            'discount' => 8,
            'point_rate' => 0.25,  
        ],
    ],
];
