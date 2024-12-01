document.addEventListener('DOMContentLoaded', function() {
    // Form submission handler
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Validate password
            const password = formData.get('password');
            if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(password)) {
                showAlert('Password must have at least 8 characters, including one number, one letter, and one capital letter', 'error');
                return;
            }
            
            // Send data to server
            fetch('api/users.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('User added successfully', 'success');
                    addUserForm.reset();
                } else {
                    showAlert(data.error || 'Error adding user', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred while adding the user', 'error');
            });
        });
    }

    // Logout handler
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            fetch('api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'logout' }),
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'index.html';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // Helper function to show alerts
    function showAlert(message, type) {
        const alertId = type === 'success' ? 'successAlert' : 'errorAlert';
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.textContent = message;
            alert.style.display = 'block';
            
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }
    }

    // Check authentication status
    fetch('api/auth.php', {credentials: 'same-origin'})
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
});