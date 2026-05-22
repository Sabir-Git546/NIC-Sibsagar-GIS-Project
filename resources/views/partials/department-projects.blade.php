<h5 class="mb-3">

    Department: {{ $deptname }}

</h5>

<table class="table table-bordered table-hover">

    <thead class="table-dark">

        <tr>

            <th>ID</th>

            <th>Project Name</th>

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

                    @php
                        $status = strtolower($project->status ?? '');
                    @endphp

                    @if($status == 'completed')

                        <span class="badge bg-success">
                            Completed
                        </span>

                    @elseif($status == 'ongoing')

                        <span class="badge bg-primary">
                            Ongoing
                        </span>

                    @elseif($status == 'planning')

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

                <td colspan="3"
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