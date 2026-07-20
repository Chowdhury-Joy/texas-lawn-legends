<?php

namespace App\Http\Controllers;

use App\Models\Project;

class DashboardController extends Controller
{
    /**
     * Render a client's private project dashboard, authenticated purely by the
     * unique hash in the URL. An unknown hash 404s via route-model binding.
     */
    public function show(Project $project)
    {
        $project->load([
            'milestones' => fn ($q) => $q->orderBy('id'),
            'progressPhotos' => fn ($q) => $q->latest('created_at'),
            'invoices' => fn ($q) => $q->latest('issue_date'),
        ]);

        // Group progress photos by the milestone step they were tagged to.
        $photosByStep = $project->progressPhotos->groupBy('milestone_step');

        return view('dashboard', [
            'project' => $project,
            'milestones' => $project->milestones,
            'photosByStep' => $photosByStep,
        ]);
    }
}
