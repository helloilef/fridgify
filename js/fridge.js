// Fetch fridge items from the JSON file
async function fetchFridgeItems() {
  const response = await fetch("json/fridge-ing.json");
  return response.json(); // Converts the response stream into usable JS data
}

// Render a single fridge item card
function renderItemCard(item) {
  const card = document.createElement("article");
  card.className = "fridge-inventory__card";
  card.innerHTML = `
    <span class="close-btn">&times;</span>
    <img class="fridge-inventory__card-image" src="${item.image}" alt="${
    item.title
  }" />
    <h4 class="fridge-inventory__card-title">${item.title}</h4>
    <div class="fridge-inventory__card-details flex-between">
      <div class="fridge-inventory__card-rating">
        <img src="assets/star.svg" alt="star" />
        <p>${item.rating}</p>
      </div>
      <p class="fridge-inventory__card-price">$${item.price.toFixed(2)}</p>
      <br/>  
    </div>
    <button class="update-btn">Update</button>
    
  `;
  return card;
}

// Handle the "Add Item" form
const addItemForm = document.getElementById("add-item-form");
const addItemBtn = document.getElementById("add-item");
const closeBtn = document.querySelector(".close-btn");

function showAddItemForm() {
  if (addItemForm) {
    addItemForm.style.display = "block";
  }
}

function hideAddItemForm() {
  if (addItemForm) {
    addItemForm.style.display = "none";
  }
}

// Add event listeners for the "Add Item" button and close button
if (addItemBtn) {
  addItemBtn.addEventListener("click", (e) => {
    e.preventDefault(); // Prevent default link behavior
    showAddItemForm();
  });
}

if (closeBtn) {
  closeBtn.addEventListener("click", hideAddItemForm);
}

// Handle deleting items using event delegation
const container = document.querySelector(".fridge-inventory__catalogue1");

// Handle the "Add Item" form
const updateItemForm = document.getElementById("update-item-form");
const updateItemBtn = document.getElementById("update-item");
const closeBtnUp = document.querySelector(".close-btn-up");

function showUpdateItemForm() {
  if (addItemForm) {
    updateItemForm.style.display = "block";
  }
}

function hideUpdateItemForm() {
  if (addItemForm) {
    updateItemForm.style.display = "none";
  }
}

if (container) {
  container.addEventListener("click", (e) => {
    if (e.target.classList.contains("close-btn")) {
      const card = e.target.closest(".fridge-inventory__card");
      if (card) {
        card.remove(); // Remove the card from the DOM
      }
    }
  });

  // Fetch and render fridge items
  fetchFridgeItems().then((data) => {
    container.innerHTML = ""; // Clear existing content
    data.forEach((item) => {
      container.appendChild(renderItemCard(item));
    });
  });
}
