<?php

namespace local_activityfilter\activity_searcher;

interface i_text_compressor {
    public function compress(string $text): string;
}