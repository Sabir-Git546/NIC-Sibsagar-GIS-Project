document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const searchInput =
        document.getElementById('projectSearch');

    const statusFilter =
        document.getElementById('statusFilter');

    const deptFilter =
        document.getElementById('deptFilter');

    const resetBtn =
        document.getElementById('resetFilter');

    const rows =
        document.querySelectorAll(
            '#projectTable tbody tr'
        );

    /*
    |--------------------------------------------------------------------------
    | Filter Function
    |--------------------------------------------------------------------------
    */

    function filterTable() {

        // Search text
        const searchValue =
            searchInput.value.toLowerCase().trim();

        // Status dropdown
        const statusValue =
            statusFilter.value.toLowerCase().trim();

        // Department dropdown
        const deptValue =
            deptFilter.value.toLowerCase().trim();

        /*
        |--------------------------------------------------------------------------
        | Loop Through Rows
        |--------------------------------------------------------------------------
        */

        rows.forEach(row => {

            // Skip empty rows
            if (row.cells.length === 1) {

                return;
            }

            // Entire row text
            const text =
                row.innerText.toLowerCase();

            /*
            |--------------------------------------------------------------------------
            | Correct Column Indexes
            |--------------------------------------------------------------------------
            |
            | 0 = ID
            | 1 = Project Name
            | 2 = Status
            | 3 = Department
            | 4 = Location
            | 5 = Description
            | 6 = Created By
            | 7 = Created At
            |
            */

            // Status column
            const statusCell =
                row.cells[2]?.innerText.toLowerCase() || '';

            // Department column
            const deptCell =
                row.cells[3]?.innerText.toLowerCase() || '';

            /*
            |--------------------------------------------------------------------------
            | Match Conditions
            |--------------------------------------------------------------------------
            */

            // Universal search
            const matchesSearch =
                searchValue === '' ||
                text.includes(searchValue);

            // Status filter
            const matchesStatus =
                statusValue === '' ||
                statusCell.includes(statusValue);

            // Department filter
            const matchesDept =
                deptValue === '' ||
                deptCell.includes(deptValue);

            /*
            |--------------------------------------------------------------------------
            | Final Visibility
            |--------------------------------------------------------------------------
            */

            if (
                matchesSearch &&
                matchesStatus &&
                matchesDept
            ) {

                row.style.display = '';

            } else {

                row.style.display = 'none';
            }

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    // Live search
    if (searchInput) {

        searchInput.addEventListener(
            'keyup',
            filterTable
        );
    }

    // Status filter
    if (statusFilter) {

        statusFilter.addEventListener(
            'change',
            filterTable
        );
    }

    // Department filter
    if (deptFilter) {

        deptFilter.addEventListener(
            'change',
            filterTable
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Button
    |--------------------------------------------------------------------------
    */

    if (resetBtn) {

        resetBtn.addEventListener('click', function () {

            searchInput.value = '';

            statusFilter.value = '';

            deptFilter.value = '';

            filterTable();
        });
    }

});