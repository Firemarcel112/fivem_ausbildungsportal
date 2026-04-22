<?php

namespace App\Http\Controllers;

use App\DTO\QualificationRequirementViewData;
use App\Models\Qualifications\Requirement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RequirementsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): mixed
    {

        $requirements = Requirement::query()
            ->with([
                'qualification',
                'fraction',
            ])
            ->joinOrderedQualifications()
            ->whereHas('qualification', function (Builder $query) {
                /** @var Builder<\App\Models\Qualification> $query */
                /** @phpstan-ignore-next-line */
                return $query->isVisible();
            })
            ->get()
            ->groupBy('qualification.name')
            ->map(function (Collection $item) {
                /** @var Collection<int, Requirement> $item */
                $items = $item->map(function (Requirement $item) {
                    return QualificationRequirementViewData::fromModel($item);
                })
                    ->groupBy('fraction_name');

                return $items;
            });

        return view('requirements', compact('requirements'));
    }
}
