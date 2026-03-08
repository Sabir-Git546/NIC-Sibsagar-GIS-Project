@extends('layouts.master')

@section('content')
<div class="container mt-4">

    <h4>Upload Document for: {{ $project->projectname }}</h4>

    <form action="{{ route('projects.documents.store', $project->projectid) }}"
          method="POST" enctype="multipart/form-data">

        @csrf
        <div class="mb-3">
            <label class="form-label">Select Document (PDF / Word / Excel / Image)</label>
            <input type="file" name="document_file" class="form-control" required>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Upload</button>
            <a href="{{ route('projects.view', $project->projectid) }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>

    </form>
</div>

<script>
@if(session('uploaded'))
    let continueUpload = confirm("File uploaded successfully! Do you want to upload another file?");
    if(!continueUpload){
        window.location = "{{ route('projects.view', $project->projectid) }}";
    }
@endif
</script>
@endsection