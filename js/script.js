// import images as relative image path won't work with vite/vercel.
import check from "../assets/check.svg";
import star from "../assets/star.svg";
import sushi12 from "../assets/sushi-12.png";
import sushi11 from "../assets/sushi-11.png";
import sushi10 from "../assets/sushi-10.png";

if (document.getElementById("login")) {
  import("./auth.js").then(({ showLoginForm, hideLoginForm }) => {
    const loginBtn = document.getElementById("login");
    const closeBtn = document.querySelector(".close-btn");
    const loginForm = document.getElementById("login-form");

    loginBtn.addEventListener("click", showLoginForm);
    closeBtn.addEventListener("click", hideLoginForm);
  });
}

import AOS from "aos";
import "aos/dist/aos.css";

// init AOS animation
AOS.init({
  duration: 1000,
  offset: 100,
});
