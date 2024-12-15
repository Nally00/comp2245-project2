document.addEventListener("DOMContentLoaded", function () {

    const issueTitle = document.getElementById("issue-title");
    const issueNumber = document.getElementById("issue-number");
    const issueDescription = document.getElementById("issue-description");
    const createdDetails = document.getElementById("created-details");
    const updatedDetails = document.getElementById("updated-details");
    const status = document.getElementById("status");
    const assignedTo = document.getElementById("assignedTo");
    const priority = document.getElementById("priority");
    const closedBtn = document.getElementById("mark-closed");
    const inProgressBtn = document.getElementById("mark-progress");

    // get issue ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const issueId = urlParams.get("issue_id");

    //Check if ID is found 
    if (!issueId) {
        //if no ID redirect to dashboard
        alert("Issue ID not found.");
        window.location.href = "dashboard.html"; 
        return;
    }

     // Fetch issue details on page load
     fetchIssueDetails();
   

    // Function to fetch and display issue details
    function fetchIssueDetails() {
        //get issues based on ID
        fetch(`issue_details.php?issue_id=${issueId}`)
            .then((response) => {
                if (!response.ok) throw new Error("Failed to fetch issue details.");
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    // Populate page with issue details
                    issueTitle.textContent = data.issue.title;
                    issueNumber.textContent= `Issue#${data.issue.id}`;
                    issueDescription.textContent = data.issue.description;
                    createdDetails.textContent = `Created: ${data.issue.created_at} by ${data.issue.created_by}`;
                    updatedDetails.textContent = `Last Updated: ${data.issue.updated_at}`;
                    assignedTo.innerHTML = `<strong>Assigned To:</strong><br> ${data.issue.assigned_to}`;
                    priority.innerHTML = `<strong>Priority:</strong><br> ${data.issue.priority}`;
                    status.innerHTML = `<strong>Status:</strong><br> ${data.issue.status}`;
                } else {
                    //Handle errors 
                    alert(data.error || "An error occurred while loading issue details.");
                    window.location.href = "dashboard.html"; 
                }
            })
            .catch((error) => {
                console.error("Error fetching issue details:", error);
                alert("An error occurred whilefetching issue details");
                window.location.href = "dashboard.html";
            });
    }

    // Add event listeners for buttons
    closedBtn.addEventListener("click", () => changeStatus("Closed"));
    inProgressBtn.addEventListener("click", () => changeStatus("In Progress"));

   

    // Function to update the issue status
    function changeStatus(newStatus) {
        //request to update status 
        fetch("issue_status.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ issue_id: issueId, status: newStatus }),
        })
            .then((response) => {
                if (!response.ok) throw new Error("Failed to update issue status.");
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    alert(`Issue status updated to ${newStatus}.`);
                    fetchIssueDetails();
                } else {
                    alert(data.error || "An error occurred while updating the issue.");
                }
            })
            .catch((error) => {
                console.error("Error updating issue status:", error);
                alert("An error occurred while updating issue status.");
            });
    }
  
    
});
