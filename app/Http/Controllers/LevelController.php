<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SectionRequest;
use App\Models\Level;
use App\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LevelController extends Controller
{
    public function index(Request $request)
    {

        $query = Section::with(['level']);

        $sections = $query->paginate(10)->withQueryString();

        return Inertia::render('level/Index', [
            'sections' => $sections
        ]);
    }

    public function show(int $sectionID)
    {
        $section = Section::with(['level'])->find($sectionID);

        return Inertia::render('level/Show', [
            'section' => $section
        ]);
    }

    public function create()
    {
        return Inertia::render('level/Create', [
            'gradeLevels' => Level::orderBy('id')->get()
        ]);
    }

    public function store(Request $request)
    {
        Section::create([
            'level_id' => $request->level_id,
            'name' => $request->section_name,
            'description' => $request->section_description
        ]);

        return redirect()->route('levels.index');
    }

    public function edit(int $sectionID)
    {
        $section = Section::with(['level'])->find($sectionID);

        return Inertia::render('level/Edit', [
            'section' => $section,
            'levels' => Level::all()
        ]);
    }

    public function update(Request $request, int $sectionID)
    {
        $section = Section::find($sectionID);

        $section->update([
            'level_id' => $request->input('level_id'),
            'name' => $request->input('section_name'),
            'description' => $request->input('section_description')
        ]);

        return redirect()->route('levels.show', $sectionID);
    }
}
