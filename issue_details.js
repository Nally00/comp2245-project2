// JavaScript Code to Fetch Issue Details via AJAX

document.addEventListener("DOMContentLoaded", function () {
    const issueDetailsContainer = document.getElementById("issue-details");

    let issueId = 1;

    // Fetch the issue details using AJAX
    function fetchIssueDetails(issueId) {
        fetch(`http://localhost/comp2245-project2/issue_details.php?issue_id=${issueId}`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Failed to fetch issue details");
                }
                return response.json();
            })
            .then((data) => {
                // Populate the HTML with the fetched details
                document.getElementById("issue-title").textContent = `${data.title}`;
                document.getElementById("issue-number").textContent = `Issue #${data.issue_number}`;
                document.getElementById("issue-description").textContent = data.description;
                document.getElementById("issue-type").textContent = data.type;
                document.getElementById("issue-priority").textContent = data.priority;
                document.getElementById("issue-status").textContent = data.status;
                document.getElementById("assigned-to").textContent = data.assigned_to;
                document.getElementById("created-details").textContent = `Issue created on ${data.date_created} by ${data.created_by}`;
                document.getElementById("updated-details").textContent = `Last updated on ${data.date_updated}`;
            })
            .catch((error) => {
                console.error('Fetch error:', error);
                document.getElementById("issue-details-content").textContent = "Error fetching issue details.";
            });
    }

    // Example: Call fetchIssueDetails with a specific issue ID
    //const issueId = 1; // Replace this with the actual issue ID to fetch
    fetchIssueDetails(issueId);

    $.ajax({
        url: "http://localhost/comp2245-project2/issue_details.php?issue_id=1",
        method: "GET",
        success: function(data) {
            // Handle the response data
        },
        error: function(xhr, status, error) {
            console.error("AJAX request failed:", status, error);
        }
    });
    
});
