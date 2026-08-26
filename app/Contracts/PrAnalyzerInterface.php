<?php

namespace App\Contracts;

interface PrAnalyzerInterface
{
    /**
     * Analyse le contenu d'un diff et retourne un rapport basé sur des règles.
     */
    public function analyzeDiff(string $diffContent, array $rules): array;
}
