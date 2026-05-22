<h5 class="mb-3">

    Status:
    
    <span class="text-primary">
        {{ ucfirst($status) }}
    </span>

</h5>

<table class="table table-bordered table-hover">

    <thead class="table-dark">

        <tr>

            <th>ID</th>

            <th>Project Name</th>

            <th>Department</th>

            <th>Status</th>

        </tr>

    </thead>

    <tbody>

        @forelse($projects as $project)

            <tr>

                <td>
                    {{ $project->projectid }}
                </td>

                <td>
                    {{ $project->projectname }}
                </td>

                <td>
                    {{ $project->deptname }}
                </td>

                <td>

                    @php
                        $projectStatus =
                            strtolower(
                                $project->status ?? ''
                            );
                    @endphp

                    @if($projectStatus == 'completed')

                        <span class="badge bg-success">
                            Completed
                        </span>

                    @elseif($projectStatus == 'ongoing')

                        <span class="badge bg-primary">
                            Ongoing
                        </span>

                    @elseif($projectStatus == 'planning')

                        <span class="badge bg-warning text-dark">
                            Planning
                        </span>

                    @else

                        <span class="badge bg-secondary">
                            Unknown
                        </span>

                    @endif

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="4"
                    class="text-center text-muted">

                    No projects found

                </td>

            </tr>

        @endforelse

    </tbody>

</table>


@if($projects->hasPages())

    <div class="mt-3 d-flex justify-content-center">

        {{ $projects->links('pagination::bootstrap-5') }}

    </div>

@endif