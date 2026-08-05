<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\PrAnalyzerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GithubWebhookController extends Controller
{
    private PrAnalyzerInterface $prAnalyzer;

    // Dependency Inversion : On injecte l'interface, pas la classe concrète.
    public function __construct(PrAnalyzerInterface $prAnalyzer)
    {
        $this->prAnalyzer = $prAnalyzer;
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (!$this->isValidPullRequestEvent($payload)) {
            return response()->json(['message' => 'Événement ignoré, ce n\'est pas une PR valide.'], 200);
        }

        $diffContent = $payload['pull_request']['diff_url'] ?? 'Mocked diff content for testing';
        $aqlRules = ['camelCase', 'solid', 'pureFunctions', 'cyclomaticComplexity'];

        $report = $this->prAnalyzer->analyzeDiff($diffContent, $aqlRules);

        return response()->json([
            'status' => 'success',
            'report' => $report
        ], 200);
    }

    /**
     * Vérifie si l'événement d'entrée est pertinent pour l'analyse.
     */
    private function isValidPullRequestEvent(array $payload): bool
    {
        $action = $payload['action'] ?? '';
        $validActions = ['opened', 'synchronize', 'reopened'];

        return isset($payload['pull_request']) && in_array($action, $validActions, true);
    }
}
