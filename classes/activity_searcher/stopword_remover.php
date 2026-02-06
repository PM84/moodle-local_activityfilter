<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_activityfilter\activity_searcher;

use NlpTools\Documents\TokensDocument;
use NlpTools\Tokenizers\PennTreeBankTokenizer;
use voku\helper\StopWords;

defined('MOODLE_INTERNAL') || die();

require(__DIR__ . '/../../vendor/autoload.php');

class stopword_remover implements i_text_compressor {
    public function compress(string $text): string {
        $text = strtolower($text);
        $tokens = $this->tokenize($text);
        $this->apply_exclude_stopwords($tokens);
        $shorttext = implode(" ", $tokens->getDocumentData());
        return preg_replace('/\s+([.,!?;:])/', '$1', $shorttext);
    }

    public function tokenize(string $text): TokensDocument {
        $tokenizer = new PennTreeBankTokenizer();
        $tokens = $tokenizer->tokenize($text);
        return new TokensDocument($tokens);
    }

    public function apply_exclude_stopwords(TokensDocument $tokens): void {
        $stopwords = new StopWords();
        $words = array_merge(
            $stopwords->getStopWordsFromLanguage('en'),
            $stopwords->getStopWordsFromLanguage('de')
        );

        $transformation = new \NlpTools\Utils\StopWords($words);
        $tokens->applyTransformation($transformation);
    }
}
