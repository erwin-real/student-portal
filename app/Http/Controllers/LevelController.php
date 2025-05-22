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

        // $query = Level::with(['sections']);
        $query = Section::with(['level']);


        // if ($search = $request->input('search')) {
        //     $query->where('name', 'like', "%{$search}%");
        // }

        // $query->where()

        $sections = $query->paginate(10)->withQueryString();

        // dd($sections);

        return Inertia::render('level/Index', [
            'sections' => $sections,
            // 'filters' => [
            //     'search' => $search
            // ]
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
        // // Product::create($request->validated() + ['user_id' => $request->user()->id]); // 1st way
        Section::create([
            'level_id' => $request->level_id,
            'name' => $request->section_name,
            'description' => $request->section_description
        ]); // 2nd way

        return redirect()->route('levels.index');
    }
}
