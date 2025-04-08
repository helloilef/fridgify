// fridge.js

export function setupCardInteraction() {
  const catalogue = document.querySelector(".fridge-inventory__catalogue");
  const cards = Array.from(
    catalogue.querySelectorAll(".fridge-inventory__card")
  );

  cards.forEach((card, index) => {
    card.addEventListener("click", () => {
      // Remove current 'active-card'
      cards.forEach((c) => c.classList.remove("active-card"));

      // Add active class to clicked card
      card.classList.add("active-card");

      // Reorder DOM: move clicked card to the center (middle index)
      const newOrder = [...cards.slice(0, index), ...cards.slice(index + 1)];

      catalogue.innerHTML = ""; // Clear all
      const middleIndex = Math.floor(newOrder.length / 2);
      newOrder.slice(0, middleIndex).forEach((c) => catalogue.appendChild(c));
      catalogue.appendChild(card); // Place clicked card in the center
      newOrder.slice(middleIndex).forEach((c) => catalogue.appendChild(c));
    });
  });
}
