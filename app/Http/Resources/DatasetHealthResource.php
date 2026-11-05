<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DatasetHealthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $h = data_get($this->profile, 'health', []);

        return [
            'score'           => $h['score'] ?? null,
            'issues'          => $h['issues'] ?? null,
            'criticalIssues'  => $h['critical_issues'] ?? null,
            'columnsAffected' => $h['columns_affected'] ?? null,
            'checksPassed'    => $h['checks_passed'] ?? null,
            'checksTotal'     => $h['checks_total'] ?? null,
        ];
    }
}