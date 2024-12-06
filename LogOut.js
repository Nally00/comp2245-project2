document.addEventListener('DOMContentLoaded', function () {
    const logoutBtn = document.getElementById('LogoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();

            fetch('LogOut.php', {
                method: 'POST',
                credentials: 'same-origin', // Include session cookies
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Logout Failed');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Redirect to the login page
                        window.location.href = 'LogIn.html';
                    } else {
                        console.error('Logout failed:', data.error);
                        alert('Logout failed. Try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An unexpected error occurred.Try again.');
                });
        });
    }
});
