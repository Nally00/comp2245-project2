// Helper function to clear invalid status
function clearInvalid() {
    // Remove invalid class
    document.querySelectorAll(".input-invalid").forEach((input) => {
        input.classList.remove("input-invalid"); 
    });

    // Remove all error messages
    document.querySelectorAll(".error-message").forEach((error) => {
        error.remove(); 
    });
}

// Helper function to add invalid status 
function invalid(input, message) {
    input.classList.add("input-invalid"); 

    const errorMessage = document.createElement("div");
    errorMessage.classList.add("error-message");
    errorMessage.textContent = message;

    // Add the error message below input field
    input.parentNode.appendChild(errorMessage);
}

// Add event listener for the login form
document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent default form submission

    // Clear previous invalid status
    clearInvalid();

    // Get form fields
    const email = document.getElementById("email");
    const password = document.getElementById("password");

    let isValid = true;

    // Input Validation 
    if (!email.value.trim()) {
        isValid = false;
        invalid(email, "Email is required.");
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        isValid = false;
        invalid(email, "Invalid email format.");
    }

    if (!password.value.trim()) {
        isValid = false;
        invalid(password, "Password is required.");
    }

    // If the form is invalid, dont submit
    if (!isValid) {
        return;
    }

    // If the form is valid, submit data via AJAX
    fetch("LogIn.php", {
        method: "POST",
        body: new URLSearchParams({
            email: email.value.trim(),
            password: password.value,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Redirect to the dashboard on success
                window.location.href = "dashboard.html";
            } else {
                //Error if login fails
                alert( "Invalid email or password.");
            }
        })
        .catch((error) => {
            // Handle any unexpected errors
            console.error("Error:", error);
            alert("An unexpected error occurred. Please try again.");
        });
});
