document.addEventListener('DOMContentLoaded', () => {
    // Select all filter buttons
    const filterButtons = document.querySelectorAll('.filters button');
    const defaultFilter = document.getElementById('filter-all');

    // Set All filter as  the default filter 
    defaultFilter.classList.add('selected');

    // Add click event listener to all filter buttons
    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Remove 'selected' class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('selected'));

            // Add 'selected' class to the clicked button
            this.classList.add('selected');

            // Fetch issues for the selected filter
            const filter = this.id.replace('filter-', '');
            fetchIssues(filter);
        });
    });

    // Default: show all issues
    fetchIssues('all');
});

// Fetch issues using AJAX
function fetchIssues(filter) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'dashboard.php', true); // Set up a POST request 
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
             // If the request is successful, update the table with the fetched issues
            document.getElementById('issues-table').innerHTML = xhr.responseText;
             // Add click event listeners to the table rows
            attachRowClickListeners();
        }
    };
    xhr.send('filter=' + filter);
}

// Function to add click event listeners to rows
function attachRowClickListeners() {
    const rows = document.querySelectorAll('.issues-table tbody tr');
    rows.forEach(row => {
        row.addEventListener('click', function () {
            const issueId = this.getAttribute('data-issue-id');
            //redirect to issue details
            window.location.href = `issue_details.html?issue_id=${issueId}`;
        });
    });
}
