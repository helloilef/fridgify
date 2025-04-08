// Selecting DOM elements
const loginBtn = document.getElementById("login");
const closeBtn = document.querySelector(".close-btn");
const loginForm = document.getElementById("login-form");

// Function to show login form
function showLoginForm() {
  loginForm.style.display = "block";
}

// Function to hide login form
function hideLoginForm() {
  loginForm.style.display = "none";
}

// Event listeners
loginBtn.addEventListener("click", showLoginForm);
closeBtn.addEventListener("click", hideLoginForm);

// Export functions if they need to be accessed elsewhere
export { showLoginForm, hideLoginForm };
