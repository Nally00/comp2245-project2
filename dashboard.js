document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.filters button');
    const defaultFilter = document.getElementById('filter-all'); // Default button for "ALL"

    // All filter selected by default
    defaultFilter.classList.add('selected');

    // Add click event listener to all filter buttons
    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Ensure 'selected' class is cleared from all buttons
            filterButtons.forEach(btn => btn.classList.remove('selected'));

            // Add 'selected' class to the button clicked
            this.classList.add('selected');

            // Fetch issues for the selected filter 
            const filter = this.id.replace('filter-', '');
            fetchIssues(filter);
        });
    });

    //Default: show all issues
    fetchIssues('all');
});

// Fetch issues using AJAX 
function fetchIssues(filter) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'dashboard.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            document.getElementById('issues-table').innerHTML = xhr.responseText;
            attachRowClickListeners();
        }
    };
    xhr.send('filter=' + filter);
}

// Add row click event listeners 
function attachRowClickListeners() {
    const rows = document.querySelectorAll('.issues-table tbody tr');
    rows.forEach(row => {
        row.addEventListener('click', function () {
            const issueId = this.getAttribute('data-issue-id');
            window.location.href = `issue_details.html?issue_id=${issueId}`;
        });
    });
}
