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

// Add event listener to the form
document.getElementById("addUserForm").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent default form submission

    // Clear previous validation states
    clearInvalid();

    const firstname = document.getElementById("firstname");
    const lastname = document.getElementById("lastname");
    const email = document.getElementById("email");
    const password = document.getElementById("password");

    let isValid = true;

    // Validation logic
    if (firstname.value.trim() === "") {
        isValid = false;
        invalid(firstname, "First Name is required.");
    }

    if (lastname.value.trim() === "") {
        isValid = false;
        invalid(lastname, "Last Name is required.");
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value.trim())) {
        isValid = false;
        invalid(email, "Invalid email format.");
    }

    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    if (!passwordRegex.test(password.value.trim())) {
        isValid = false;
       invalid(password, "Password must be at least 8 characters long, include a number, an uppercase letter, and a lowercase letter.");
    }

    //If the form is invalid, stop submission
    if (!isValid) {  
        return;
    }

    // If the form is valid, submit data via AJAX
    const formData = new FormData(this);
    fetch("add-user.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert(data.message || "User added successfully!"); // Success notification
                this.reset(); // Reset the form
                clearInvalid(); // Clear validation states
            } else {
                alert(data.error || "An error occurred while submitting the form."); // Server-side error notification
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("An unexpected error occurred. Please try again."); // AJAX error notification
        });
});

// Helper function to add error class and message
function invalid(input, message) {
    input.classList.add("input-invalid"); // Add red border to invalid field

    // Create and append error message element
    const errorMessage = document.createElement("div");
    errorMessage.classList.add("error-message");
    errorMessage.textContent = message;
    input.parentNode.appendChild(errorMessage);
}


// Check authentication status  ************pending **************
fetch('auth.php', {credentials: 'same-origin'})
.then(response => response.json())
.then(data => {
    if (!data.authenticated) {
        document.getElementById('authMessage').style.display = 'block';
        document.getElementById('mainContent').style.display = 'none';
    } else {
        document.getElementById('authMessage').style.display = 'none';
        document.getElementById('mainContent').style.display = 'block';
    }
})
.catch(error => console.error('Error:'));
