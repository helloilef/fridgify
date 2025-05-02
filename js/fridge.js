document.addEventListener("DOMContentLoaded", () => {
  // Handle the "Add Item" form
  const addItemBtn = document.getElementById("add-item");
  const addItemForm = document.getElementById("add-item-form");
  const closeAddItemButton = document.querySelector(".close-btn");

  if (addItemBtn && addItemForm) {
    addItemBtn.addEventListener("click", (event) => {
      event.preventDefault();
      addItemForm.classList.remove("hidden"); // Show the "Add Item" form
      console.log("Add Item button clicked, form is now visible.");
    });
  }

  if (closeAddItemButton && addItemForm) {
    closeAddItemButton.addEventListener("click", () => {
      addItemForm.classList.add("hidden"); // Hide the "Add Item" form
      console.log("Add Item form closed.");
    });
  }

  // Handle the "Update Item" form
  const container = document.querySelector(".fridge-inventory__catalogue1");
  const updateForm = document.getElementById("update-item-form");
  const closeUpdateButton = document.querySelector(".close-btn-up");

  if (container) {
    container.addEventListener("click", (event) => {
      console.log("Event target:", event.target);

      const button = event.target.closest(".update-button");
      if (button) {
        console.log("Update button clicked.2");

        // Get item details from data attributes
        const id = button.getAttribute("data-id");
        const name = button.getAttribute("data-name");
        const image = button.getAttribute("data-image");
        const quantity = button.getAttribute("data-quantity");
        const rating = button.getAttribute("data-rating");
        const price = button.getAttribute("data-price");

        console.log("Data attributes:", {
          id,
          name,
          image,
          quantity,
          rating,
          price,
        });

        // Call the openUpdateForm function
        openUpdateForm(id, name, image, quantity, rating, price);
      } else {
        console.log("No update-button found for the clicked target.");
      }
    });
  }

  if (closeUpdateButton && updateForm) {
    closeUpdateButton.addEventListener("click", () => {
      updateForm.classList.add("hidden"); // Hide the "Update Item" form
      console.log("Update form closed.");
    });
  }
});

// Define the openUpdateForm function
function openUpdateForm(id, name, image, quantity, rating, price) {
  console.log("openUpdateForm called with:", {
    id,
    name,
    image,
    quantity,
    rating,
    price,
  });

  const updateForm = document.getElementById("update-item-form");

  // Populate the form fields with the item's details
  document.getElementById("update-id").value = id;
  document.getElementById("newtitle").value = name;
  document.getElementById("newquantity").value = quantity;
  document.getElementById("newrating").value = rating;
  document.getElementById("newprice").value = price;

  // Optional: Display the current image in the form
  const imagePreview = document.getElementById("image-preview");
  if (imagePreview) {
    imagePreview.src = image;
    imagePreview.style.display = "block";
  }

  // Show the update form
  updateForm.classList.remove("hidden");
  console.log("Update form is now visible.");
}

document.addEventListener("DOMContentLoaded", () => {
  const profileIcon = document.getElementById("profile-icon");
  const dropdownMenu = document.getElementById("dropdown-menu");

  if (profileIcon && dropdownMenu) {
    console.log("Profile Icon:", profileIcon);
    console.log("Dropdown Menu:", dropdownMenu);

    profileIcon.addEventListener("click", (event) => {
      console.log("Profile icon clicked.");
      event.stopPropagation(); // Prevent event bubbling
      dropdownMenu.classList.toggle("active"); // Toggle visibility of the dropdown menu
    });

    // Close the dropdown if clicked outside
    document.addEventListener("click", (event) => {
      if (
        !profileIcon.contains(event.target) &&
        !dropdownMenu.contains(event.target)
      ) {
        dropdownMenu.classList.remove("active");
        console.log("Clicked outside, dropdown menu closed.");
      }
    });
  } else {
    console.warn("Profile icon or dropdown menu not found in the DOM.");
  }
});
