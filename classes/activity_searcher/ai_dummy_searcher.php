<?php

namespace local_activityfilter\activity_searcher;

use Exception;
use local_activityfilter\activity_searcher\contracts\i_activity_searcher;

class ai_dummy_searcher implements i_activity_searcher {
    public function filter_activities(string $request): array {
        $response = file_get_contents(__DIR__ . '/dummydata.json');
        $decoded = json_decode($response, true);
        if ($decoded === null) {
            throw new Exception("Json encode error: " . json_last_error_msg());
        }
        return $decoded;
    }
}
