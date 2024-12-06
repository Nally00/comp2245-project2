// Main application script for BugMe Issue Tracker
document.addEventListener('DOMContentLoaded', function() {
    // Form validation helper functions
    const validators = {
        password: (value) => {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
            return {
                isValid: regex.test(value),
                message: 'Password must contain at least 8 characters, one number, one letter, and one capital letter'
            };
        },
        email: (value) => {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return {
                isValid: regex.test(value),
                message: 'Please enter a valid email address'
            };
        }
    };

    // Show alert messages
    function showAlert(message, type = 'error') {
        const alertElement = document.getElementById(`${type}Alert`);
        if (alertElement) {
            alertElement.textContent = message;
            alertElement.style.display = 'block';
            
            // Hide alert after 5 seconds
            setTimeout(() => {
                alertElement.style.display = 'none';
            }, 5000);
        }
    }

    // Add User Form Handler
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Validate password
            const passwordCheck = validators.password(formData.get('password'));
            if (!passwordCheck.isValid) {
                showAlert(passwordCheck.message);
                return;
            }
            
            // Validate email
            const emailCheck = validators.email(formData.get('email'));
            if (!emailCheck.isValid) {
                showAlert(emailCheck.message);
                return;
            }

            fetch('add_user.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('User added successfully', 'success');
                    addUserForm.reset();
                    
                    // Refresh user list if it exists on the page
                    if (typeof refreshUsersList === 'function') {
                        refreshUsersList();
                    }
                } else {
                    const errors = Array.isArray(data.error) ? data.error.join('\n') : data.error;
                    showAlert(errors);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred while adding the user');
            });
        });
    }

    // Users List Handler
    const usersTableBody = document.getElementById('usersTableBody');
    if (usersTableBody) {
        // Function to format date
        function formatDate(dateString) {
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            };
            return new Date(dateString).toLocaleString('en-US', options);
        }

        // Function to refresh users list
        window.refreshUsersList = function() {
            fetch('users_list.php', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    usersTableBody.innerHTML = data.users.map(user => `
                        <tr class="user-row ${user.status === 'recent' ? 'recent' : ''}">
                            <td>${escapeHtml(user.firstname)} ${escapeHtml(user.lastname)}</td>
                            <td class="user-email">${escapeHtml(user.email)}</td>
                            <td class="date-created">${formatDate(user.created_at)}</td>
                            <td>
                                ${user.status === 'recent' ? 
                                    '<span class="user-status recent">Recently Added</span>' : 
                                    ''}
                            </td>
                        </tr>
                    `).join('');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error loading users list');
            });
        };

        // Helper function to escape HTML and prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Initial load of users list
        refreshUsersList();

        // Refresh the list every 30 seconds
        setInterval(refreshUsersList, 30000);
    }

    // Navigation active state handler
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath.split('/').pop()) {
            link.classList.add('active');
        }
    });
});
