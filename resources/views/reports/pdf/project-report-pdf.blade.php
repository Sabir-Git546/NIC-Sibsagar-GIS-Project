<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Project Report</title>

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .header p {
            margin-top: 5px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        table th {
            background: #f2f2f2;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }

    </style>

</head>

<body>

    {{-- HEADER --}}
    <div class="header">

        <h2>
            Government of Assam
        </h2>

        <h3>
            Sibsagar District Administration
        </h3>

        <p>
            Project Report
        </p>

    </div>


    {{-- REPORT TABLE --}}
    <table>

        <thead>

            <tr>

                <th>#</th>

                <th>Project</th>

                <th>Department</th>

                <th>Status</th>

                <th>Created By</th>

                <th>Date</th>

            </tr>

        </thead>

        <tbody>

            @foreach($reports as $index => $report)

                <tr>

                    <td>
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $report->projectname }}
                    </td>

                    <td>
                        {{ $report->departmentname ?? '-' }}
                    </td>

                    <td>
                        {{ $report->status }}
                    </td>

                    <td>
                        {{ $report->createdby }}
                    </td>

                    <td>

                        {{ \Carbon\Carbon::parse($report->createdat)->format('d-m-Y') }}

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>


    {{-- FOOTER --}}
    <div class="footer">

        Generated on:

        {{ now()->format('d-m-Y h:i A') }}

    </div>

</body>

</html>