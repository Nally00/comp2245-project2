document.addEventListener('DOMContentLoaded', function () {
    const logoutBtn = document.getElementById('LogoutBtn');
    if (logoutBtn) {
        // Add a click event listener to the logout button
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();

             // Send a POST request to log out user
            fetch('LogOut.php', {
                method: 'POST',
                credentials: 'same-origin', 
            })
                .then(response => {
                    // Check if the response is successful
                    if (!response.ok) {
                        throw new Error('Logout Failed');
                    }
                    return response.json();
                })
                .then(data => {
                    // if logout sucessfully , redirect to the login page
                    if (data.success) {                       
                        window.location.href = 'LogIn.html';
                    } else {
                        console.error('Logout failed:', data.error);
                        alert('Logout failed. Try again.');
                    }
                })
                .catch(error => {
                    // Handle any unexpected errors
                    console.error('Error:', error);
                    alert('An unexpected error occurred.Try again.');
                });
        });
    }
});
