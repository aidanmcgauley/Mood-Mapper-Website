// Log out the user
function logout() {
    // Clear the server-side session
    window.location.href = "src/logout.php";
    
    // Remove the logged_in key from the client-side sessionStorage
    sessionStorage.removeItem('logged_in');
}