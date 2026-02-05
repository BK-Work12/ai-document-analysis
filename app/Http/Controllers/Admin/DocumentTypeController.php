<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequirement;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $documentTypes = DocumentRequirement::orderBy('sort_order')->get();
        return view('admin.document-types.index', compact('documentTypes'));
    }

    public function create()
    {
        return view('admin.document-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doc_type' => 'required|string|unique:document_requirements,doc_type|max:100',
            'description' => 'nullable|string|max:500',
            'required' => 'boolean',
            'active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['required'] = $request->input('required') == 1 ? true : false;
        $validated['active'] = $request->input('active') == 1 ? true : false;
        $validated['sort_order'] = $validated['sort_order'] ?? DocumentRequirement::max('sort_order') + 1;

        DocumentRequirement::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Document type created successfully!']);
        }

        return redirect()->route('admin.document-types.index')
            ->with('success', 'Document type created successfully!');
    }

    public function edit(DocumentRequirement $documentType)
    {
        return view('admin.document-types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentRequirement $documentType)
    {
        $validated = $request->validate([
            'doc_type' => 'required|string|unique:document_requirements,doc_type,' . $documentType->id . '|max:100',
            'description' => 'nullable|string|max:500',
            'required' => 'boolean',
            'active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['required'] = $request->input('required') == 1 ? true : false;
        $validated['active'] = $request->input('active') == 1 ? true : false;

        $documentType->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Document type updated successfully!']);
        }

        return redirect()->route('admin.document-types.index')
            ->with('success', 'Document type updated successfully!');
    }

    public function destroy(DocumentRequirement $documentType)
    {
        $documentType->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Document type deleted successfully!']);
        }

        return redirect()->route('admin.document-types.index')
            ->with('success', 'Document type deleted successfully!');
    }

    public function reorder(Request $request)
    {
        $items = $request->validate([
            'items' => 'array',
            'items.*' => 'integer',
        ])['items'];

        foreach ($items as $index => $id) {
            DocumentRequirement::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
