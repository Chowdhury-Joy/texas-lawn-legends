<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $createServices = Service::query()->active()->createSuite()->ordered()->get();
        $careServices = Service::query()->active()->careSuite()->ordered()->get();

        $testimonials = Testimonial::query()->featured()->latest()->get();

        // Neighborhood tabs are driven by the configured service areas plus any
        // neighborhoods that already have featured proof attached.
        $neighborhoods = collect(['Kessler Park', 'Bishop Arts', 'Highland Park', 'University Park', 'Oak Lawn'])
            ->merge($testimonials->pluck('neighborhood'))
            ->unique()
            ->values();

        return view('home', compact('createServices', 'careServices', 'testimonials', 'neighborhoods'));
    }
}
