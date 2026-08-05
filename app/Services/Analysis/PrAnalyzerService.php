<?php

namespace App\Services\Analysis;

use App\Contracts\PrAnalyzerInterface;
use InvalidArgumentException;

class PrAnalyzerService implements PrAnalyzerInterface
{
    /**
     * Règle AQL 2 : Fonction pure (le résultat ne dépend que des entrées).
     * Règle AQL 1 : Variables en camelCase ($diffContent, $analysisResults).
     */
    public function analyzeDiff(string $diffContent, array $rules): array
    {
        if (empty($diffContent)) {
            throw new InvalidArgumentException("Le contenu du diff ne peut pas être vide.");
        }

        $analysisResults = [];

        foreach ($rules as $rule) {
            $analysisResults[] = $this->evaluateRule($diffContent, $rule);
        }

        return $analysisResults;
    }

    /**
     * Évalue une règle individuelle.
     * Règle AQL 3 : Complexité cyclomatique minimale.
     */
    private function evaluateRule(string $diffContent, string $rule): array
    {
        $isCompliant = !str_contains($diffContent, 'TODO: fail_' . $rule);
        $complianceScore = $isCompliant ? 100 : 0;

        return [
            'ruleName' => $rule,
            'isCompliant' => $isCompliant,
            'score' => $complianceScore,
        ];
    }
}
