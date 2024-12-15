// Helper function to clear invalid status
function clearInvalid() {
    // Remove invalid class from all inputs
    document.querySelectorAll(".input-invalid").forEach((input) => {
        input.classList.remove("input-invalid");
    });

    // Remove all error messages
    document.querySelectorAll(".error-message").forEach((error) => {
        error.remove();
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const newIssueForm = document.getElementById("new-issue-form");
    const assignedToDropdown = document.getElementById("assigned_to");

    // Fetch user names for the Assigned To dropdown
    function fetchUsers() {
        fetch("users.php")
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Failed to fetch users");
                }
                return response.json();
            })
            .then((data) => {
                if (data.success && Array.isArray(data.users)) {
                    // Add user options to the dropdown
                    
                    data.users.forEach((user) => {
                        const option = document.createElement("option");
                        option.value = user.id;
                        option.textContent = `${user.firstname} ${user.lastname}`;
                        assignedToDropdown.appendChild(option);
                    });
                } else {
                    console.error("No users found");
                }
            })
            .catch((error) => {
                console.error("Error fetching users:", error);
                alert("An error occurred while loading the users list. Please try again.");
            });
    }

    // Call on page load
    fetchUsers();


     // Form Submission Handler
     if (newIssueForm) {
        newIssueForm.addEventListener("submit", function (e) {
            e.preventDefault(); // Prevent default form submission

            clearInvalid(); // Clear previous invalid status

            //get input data from fields
            const title = document.getElementById("title");
            const description = document.getElementById("description");            
            const type = document.getElementById("type");
            const priority = document.getElementById("priority");
            const assignedTo = document.getElementById("assigned_to");


            let isValid = true;

            // Input Validation
            if (title.value.trim() === "") {
                isValid = false;
                invalid(title, "Title of issue is required");
            }

            if (description.value.trim() === "") {
                isValid = false;
                invalid(description, "Description is required.");
            }

            if (type.value.trim() === "") {
                isValid = false;
                invalid(type, "Select type of issue from the dropdown menu.");
            }

            if (priority.value.trim() === "") {
                isValid = false;
                invalid(priority, "Select priority of issue from the dropdown menu..");
            }

            if (assignedTo.value.trim() === "") {
                isValid = false;
                invalid(assignedTo, "Select a user from the dropdown menu.");
            }


            // If the form is invalid, don't submit
            if (!isValid) {
                return;
            }

            // If the form is valid, submit data via AJAX
            const formData = new FormData(this);
            fetch("new-issue-form.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                         // If the user was added successfully, show confirmation
                        alert(data.message || "Issue created successfully!"); 
                        this.reset(); // Reset the form
                        clearInvalid();

                    } else {
                        alert(data.error || "An error occurred while submitting the form.");
                    }
                })
                .catch((error) => {
                    // Handle unexpected errors
                    console.error("Error:", error);
                    alert("An unexpected error occurred. Please try again.");
                });
        });
    }
});

// Helper function to add invalid status
function invalid(input, message) {
    input.classList.add("input-invalid");

    const errorMessage = document.createElement("div");
    errorMessage.classList.add("error-message");
    errorMessage.textContent = message;

     // Add error message below input field
    input.parentNode.appendChild(errorMessage);
}
