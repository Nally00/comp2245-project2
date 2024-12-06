// Helper function to clear inline validation states
function clearInvalid() {
    // Remove invalid class from all inputs
    document.querySelectorAll(".input-invalid").forEach((input) => {
        input.classList.remove("input-invalid"); // Remove red border
    });

    // Remove all error messages
    document.querySelectorAll(".error-message").forEach((error) => {
        error.remove(); // Remove error message elements
    });
}

// Helper function to add error class and message
function invalid(input, message) {
    input.classList.add("input-invalid"); // Add red border to invalid field

    // Create and append error message element
    const errorMessage = document.createElement("div");
    errorMessage.classList.add("error-message");
    errorMessage.textContent = message;
    input.parentNode.appendChild(errorMessage);
}

// Add event listener for the login form
document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent default form submission

    // Clear previous validation states
    clearInvalid();

    // Get form fields
    const email = document.getElementById("email");
    const password = document.getElementById("password");

    let isValid = true;

    // Validation logic
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

    // If the form is invalid, stop submission
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
                // Handle server-side errors
                alert( "Invalid email or password.");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("An unexpected error occurred. Please try again.");
        });
});
