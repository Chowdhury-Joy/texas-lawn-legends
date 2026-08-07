<?php

namespace App\Filament\Pages;

use App\Enums\ProjectStatus;
use App\Models\Crew;
use App\Models\Project;
use App\Models\User;
use App\Support\ProductFeatures;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ManageSchedule extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Crew Schedule';

    protected static ?string $title = 'Crew Schedule & Job Assignments';

    protected string $view = 'filament.pages.manage-schedule';

    public string $filterRange = 'this_month';

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return ($user?->canAccessKey('resource.crews') ?? false)
            && ProductFeatures::allows('resource.crews');
    }

    public function assignCrew(int $projectId, $crewId = null): void
    {
        abort_unless(static::canAccess(), 403);

        $project = Project::find($projectId);

        if (empty($crewId)) {
            $project?->update(['crew_id' => null]);

            return;
        }

        $crew = Crew::find($crewId);

        if ($project && $crew) {
            $project->update(['crew_id' => $crewId]);

            Notification::make()
                ->title("Assigned {$project->project_title} to {$crew->name}")
                ->success()
                ->send();
        }
    }

    public function getCrewsWithProjectsProperty()
    {
        $query = Project::query()
            ->whereIn('status', [ProjectStatus::Scheduled, ProjectStatus::Active]);

        match ($this->filterRange) {
            'this_week' => $query->whereBetween('started_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'next_week' => $query->whereBetween('started_at', [now()->addWeek()->startOfWeek(), now()->addWeek()->endOfWeek()]),
            'this_month' => $query->whereBetween('started_at', [now()->startOfMonth(), now()->endOfMonth()]),
            default => $query->where('started_at', '>=', now()->startOfWeek()),
        };

        $projects = $query->orderBy('started_at')->get();

        return Crew::query()->get()->map(function (Crew $crew) use ($projects) {
            return [
                'crew' => $crew,
                'projects' => $projects->where('crew_id', $crew->id),
            ];
        });
    }

    public function getCrewsProperty()
    {
        return Crew::all();
    }

    public function getUnassignedProjectsProperty()
    {
        return Project::query()
            ->whereNull('crew_id')
            ->whereIn('status', [ProjectStatus::Scheduled, ProjectStatus::Active])
            ->orderBy('started_at')
            ->get();
    }
}
