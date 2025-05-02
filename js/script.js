// Existing code for login functionality
if (document.getElementById("login")) {
  import("./auth.js").then(({ showLoginForm, hideLoginForm }) => {
    const loginBtn = document.getElementById("login");
    const closeBtn = document.querySelector(".close-btn");
    const loginForm = document.getElementById("login-form");

    loginBtn.addEventListener("click", showLoginForm);
    closeBtn.addEventListener("click", hideLoginForm);
  });
}
