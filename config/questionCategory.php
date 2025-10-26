<?php
// config/questionCategory.php
return [
    'active_hour' => (int) env('CATEGORY_ACTIVE_HOUR', 10),
    'active_minute' => (int) env('CATEGORY_ACTIVE_MINUTE', 0),
    'inactive_hour' => (int) env('CATEGORY_INACTIVE_HOUR', 23),
    'inactive_minute' => (int) env('CATEGORY_INACTIVE_MINUTE', 0),
];
