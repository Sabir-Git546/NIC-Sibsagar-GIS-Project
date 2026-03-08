<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectDocumentController extends Controller
{
    public function create($id)
    {
        $project = Project::findOrFail($id);
        return view('projects.documents.create', compact('project'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg'
        ]);

        // Save file
        $path = $request->file('document_file')->store('project_documents');

        // Save in DB
        DB::table('project_documents')->insert([
            'projectid' => $id,
            'file_name' => $request->file('document_file')->getClientOriginalName(),
            'file_path' => $path,
            'uploaded_at' => now()
        ]);

        return back()->with('uploaded', true);
    }
}