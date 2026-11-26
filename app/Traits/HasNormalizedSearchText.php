<?php

namespace App\Traits;

use App\Services\Search\ArabicNormalizer;

trait HasNormalizedSearchText
{
    protected static function bootHasNormalizedSearchText(): void
    {
        static::saving(function ($model) {
            $model->refreshSearchText();
        });
    }

    public function refreshSearchText(): void
    {
        $parts = [];
        foreach ($this->searchableTextFields() as $field) {
            $value = $this->getAttribute($field);
            if (is_string($value) && trim($value) !== '') {
                $parts[] = $value;
            }
        }

        $normalized = ArabicNormalizer::normalize(implode(' ', $parts));
        $this->setAttribute('search_text', $normalized !== '' ? mb_substr($normalized, 0, 512) : null);
    }

    /**
     * @return array<int, string>
     */
    abstract protected function searchableTextFields(): array;
}
