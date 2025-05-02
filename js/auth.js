document.addEventListener("DOMContentLoaded", () => {
  console.log("DOM fully loaded and parsed");

  // Correctly target the login and signup forms
  const loginForm = document.getElementById("login-form");
  const signupForm = document.getElementById("signup-form");
  const modalOverlay = document.querySelector(".modal-overlay"); // Modal overlay
  const showSignupLink = document.getElementById("show-signup");
  const showLoginLink = document.getElementById("show-login");
  const loginButton = document.getElementById("login"); // Login button in the header
  const closeButtons = document.querySelectorAll(".close-btn"); // Close buttons for forms

  console.log("Login form:", loginForm);
  console.log("Signup form:", signupForm);
  console.log("Login button:", loginButton);

  // If the forms are not present, exit the script
  if (!loginForm || !signupForm) {
    console.log("Login or signup form not found. Exiting script.");
    return;
  }

  // Handle login button click
  if (loginButton) {
    loginButton.addEventListener("click", (event) => {
      event.preventDefault(); // Prevent default navigation
      console.log("Login button clicked");

      if (loginForm) loginForm.classList.remove("hidden"); // Show the login form
      if (signupForm) signupForm.classList.add("hidden"); // Hide the signup form (if visible)
      if (modalOverlay) modalOverlay.classList.add("active"); // Show the overlay
    });
  }

  // Switching between login and sign-up forms
  if (showSignupLink && showLoginLink) {
    showSignupLink.addEventListener("click", () => {
      console.log("Switching to signup form");
      if (loginForm) loginForm.classList.add("hidden");
      if (signupForm) signupForm.classList.remove("hidden");
    });

    showLoginLink.addEventListener("click", () => {
      console.log("Switching to login form");
      if (signupForm) signupForm.classList.add("hidden");
      if (loginForm) loginForm.classList.remove("hidden");
    });
  }

  // Handle close button clicks
  closeButtons.forEach((closeButton) => {
    closeButton.addEventListener("click", () => {
      console.log("Close button clicked");
      if (loginForm) loginForm.classList.add("hidden"); // Hide the login form
      if (signupForm) signupForm.classList.add("hidden"); // Hide the signup form
      if (modalOverlay) modalOverlay.classList.remove("active"); // Hide the overlay
    });
  });

  // Handle login
  loginForm
    ?.querySelector("form")
    ?.addEventListener("submit", async (event) => {
      event.preventDefault();
      console.log("Login form submitted");

      const username = document.getElementById("username").value;
      const password = document.getElementById("password").value;

      console.log("Login data:", { username, password });

      try {
        const response = await fetch("login.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ username, password }),
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log("Login response:", result);

        if (result.success) {
          alert("Login successful!");
          window.location.href = result.redirect || "index.php";
        } else {
          alert(result.message || "Login failed.");
        }
      } catch (error) {
        console.error("Error during login:", error);
        alert("An error occurred during login. Please try again.");
      }
    });

  // Handle sign-up
  signupForm
    ?.querySelector("form")
    ?.addEventListener("submit", async (event) => {
      event.preventDefault();
      console.log("Signup form submitted");

      // Collect form data
      const name = document.getElementById("signup-name").value;
      const age = document.getElementById("signup-age").value;
      const username = document.getElementById("signup-username").value;
      const password = document.getElementById("signup-password").value;
      const role = document.getElementById("signup-role").value;

      console.log("Signup data:", { name, age, username, password, role });

      try {
        const response = await fetch("signup.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ name, age, username, password, role }),
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log("Signup response:", result);

        if (result.success) {
          alert("Sign-up successful! You can now log in.");
          signupForm.classList.add("hidden");
          loginForm.classList.remove("hidden");
        } else {
          alert(result.message || "Sign-up failed.");
        }
      } catch (error) {
        console.error("Error during signup:", error);
        alert("An error occurred during sign-up. Please try again.");
      }
    });
});

document.addEventListener("DOMContentLoaded", () => {
  const profileIcon = document.getElementById("profile-icon");
  const dropdownMenu = document.getElementById("dropdown-menu");

  if (profileIcon && dropdownMenu) {
    // Toggle the dropdown menu when the profile icon is clicked
    profileIcon.addEventListener("click", (event) => {
      event.stopPropagation(); // Prevent the click from propagating to the document
      dropdownMenu.classList.toggle("hidden");
    });

    // Close the dropdown menu if clicked outside
    document.addEventListener("click", (event) => {
      if (
        !profileIcon.contains(event.target) &&
        !dropdownMenu.contains(event.target)
      ) {
        dropdownMenu.classList.add("hidden");
      }
    });
  }
});
