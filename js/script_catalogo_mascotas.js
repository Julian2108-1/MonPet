document.addEventListener("DOMContentLoaded", () => {
  const typeButtons = [...document.querySelectorAll(".type-btn")];
  const typeInput = document.getElementById("pet-type-filter");
  const form = document.getElementById("filters-form");
  const clearButton = document.getElementById("clear-filters-btn");
  const grid = document.getElementById("pets-grid");
  const cards = [...document.querySelectorAll(".pet-card")];
  const count = document.getElementById("results-count");
  const emptyState = document.getElementById("empty-state");
  const minAgeRange = document.getElementById("ageMin");
  const ageRange = document.getElementById("ageRange");
  const minAgeLabel = document.getElementById("age-min-label");
  const maxAgeLabel = document.getElementById("age-max-label");
  const citySelect = document.getElementById("city-filter") || document.querySelector(".filters-sidebar select");
  const sortSelect = document.getElementById("sort");
  const nav = document.querySelector(".main-nav");
  const header = document.querySelector(".header-container");
  const metadata = [
    ["perros", "grande", "macho", 2, "compartir", "buddy", "2026-11-20"],
    ["gatos", "mediano", "hembra", 1, "ciudad-latina", "luna", "2026-11-18"],
    ["perros", "grande", "macho", 3, "ciudadela-sucre", "max", "2026-11-16"],
    ["gatos", "pequeno", "hembra", 0.67, "leon-xiii", "mia", "2026-11-14"],
    ["perros", "mediano", "macho", 0.33, "ducales", "rocky", "2026-11-12"],
    ["gatos", "mediano", "hembra", 2, "la-florida", "nala", "2026-11-10"],
    ["perros", "mediano", "macho", 1, "san-nicolas", "toby", "2026-11-08"],
    ["gatos", "pequeno", "macho", 0.25, "danubio", "simba", "2026-11-06"]
  ];

  cards.forEach((card, index) => {
    if (!card.dataset.type && metadata[index]) {
      const values = metadata[index];
      [card.dataset.type, card.dataset.size, card.dataset.gender, card.dataset.age, card.dataset.city, card.dataset.name, card.dataset.created] = values;
    }
  });

  const selectType = (type, updateInput = true) => {
    typeButtons.forEach((button) => {
      const active = button.dataset.type === type;
      button.classList.toggle("active", active);
      button.setAttribute("aria-pressed", String(active));
    });
    if (updateInput && typeInput) typeInput.value = type;
  };

  const ageText = (years) => {
    if (years >= 10) return "10+ años";
    if (years <= 0) return "0 meses";
    if (years < 1) return Math.max(1, Math.round(years * 12)) + " meses";
    return years + (years === 1 ? " año" : " años");
  };

  const updateAgeLabels = () => {
    if (!minAgeRange || !ageRange) return;
    let minimum = Number(minAgeRange.value);
    let maximum = Number(ageRange.value);
    if (minimum > maximum) {
      if (document.activeElement === minAgeRange) {
        minimum = maximum;
        minAgeRange.value = String(minimum);
      } else {
        maximum = minimum;
        ageRange.value = String(maximum);
      }
    }
    if (minAgeLabel) minAgeLabel.textContent = ageText(minimum);
    if (maxAgeLabel) maxAgeLabel.textContent = ageText(maximum);
  };

  function applyFilters() {
    const type = typeInput?.value || "";
    const sizes = [...(form?.querySelectorAll('input[name="size[]"]:checked') || [])].map((input) => input.value);
    const genders = [...(form?.querySelectorAll('input[name="gender[]"]:checked') || [])].map((input) => input.value);
    const city = citySelect?.value || "";
    const minAge = Number(minAgeRange?.value || 0);
    const maxAge = Number(ageRange?.value || 10);
    let visible = 0;

    cards.forEach((card) => {
      const matches = (!type || card.dataset.type === type)
        && (!sizes.length || sizes.includes(card.dataset.size))
        && (!genders.length || genders.includes(card.dataset.gender))
        && (!city || card.dataset.city === city)
        && Number(card.dataset.age) >= minAge && Number(card.dataset.age) <= maxAge;
      card.hidden = !matches;
      if (matches) visible += 1;
    });

    if (count) count.innerHTML = "<strong>" + visible + "</strong> " + (visible === 1 ? "mascota disponible" : "mascotas disponibles");
    if (emptyState) emptyState.hidden = visible > 0;
    const pagination = document.querySelector(".pagination");
    if (pagination) pagination.hidden = visible === 0;
  }

  function sortCards() {
    if (!grid || !sortSelect) return;
    const mode = sortSelect.value;
    const sorted = [...cards].sort((a, b) => {
      if (mode === "nombre") return a.dataset.name.localeCompare(b.dataset.name, "es");
      const order = a.dataset.created.localeCompare(b.dataset.created);
      return mode === "antiguos" ? order : -order;
    });
    sorted.forEach((card) => grid.appendChild(card));
  }

  typeButtons.forEach((button) => {
    button.addEventListener("click", () => selectType(button.dataset.type));
  });

  form?.addEventListener("submit", (event) => {
    if (window.location.pathname.endsWith(".php")) return;
    event.preventDefault();
    applyFilters();
  });

  clearButton?.addEventListener("click", () => {
    if (window.location.pathname.endsWith(".php")) {
      window.location.href = window.location.pathname;
      return;
    }
    form?.reset();
    selectType("perros", false);
    if (typeInput) typeInput.value = "";
    if (sortSelect) sortSelect.value = "recientes";
    if (minAgeRange) minAgeRange.value = "0";
    if (ageRange) ageRange.value = "10";
    updateAgeLabels();
    sortCards();
    cards.forEach((card) => { card.hidden = false; });
    if (count) count.innerHTML = "<strong>" + cards.length + "</strong> mascotas disponibles";
    if (emptyState) emptyState.hidden = true;
    const pagination = document.querySelector(".pagination");
    if (pagination) pagination.hidden = false;
  });

  [minAgeRange, ageRange].forEach((range) => range?.addEventListener("input", updateAgeLabels));

  sortSelect?.addEventListener("change", () => {
    sortCards();
    if (window.location.pathname.endsWith(".php")) {
      const params = new URLSearchParams(window.location.search);
      params.set("orden", sortSelect.value);
      window.location.search = params.toString();
    }
  });

  document.querySelectorAll(".favorite-btn").forEach((button) => {
    button.addEventListener("click", () => {
      const active = button.getAttribute("aria-pressed") !== "true";
      button.setAttribute("aria-pressed", String(active));
      button.classList.toggle("active", active);
      const icon = button.querySelector("i");
      icon?.classList.toggle("fa-solid", active);
      icon?.classList.toggle("fa-regular", !active);
    });
  });

  document.querySelectorAll(".view-btn").forEach((button) => {
    button.addEventListener("click", () => {
      const listView = button.title.toLowerCase().includes("lista");
      document.querySelectorAll(".view-btn").forEach((item) => {
        const active = item === button;
        item.classList.toggle("active", active);
        item.setAttribute("aria-pressed", String(active));
      });
      grid?.classList.toggle("list-view", listView);
    });
  });

  if (header && nav) {
    const menuButton = document.createElement("button");
    menuButton.type = "button";
    menuButton.className = "mobile-menu-toggle";
    menuButton.setAttribute("aria-label", "Abrir menú");
    menuButton.setAttribute("aria-expanded", "false");
    menuButton.setAttribute("aria-controls", "main-navigation");
    menuButton.innerHTML = '<i class="fa-solid fa-bars" aria-hidden="true"></i>';
    nav.id = "main-navigation";
    header.insertBefore(menuButton, nav);
    menuButton.addEventListener("click", () => {
      const open = menuButton.getAttribute("aria-expanded") === "true";
      menuButton.setAttribute("aria-expanded", String(!open));
      menuButton.setAttribute("aria-label", open ? "Abrir menú" : "Cerrar menú");
      nav.classList.toggle("is-open", !open);
    });
    nav.querySelectorAll("a").forEach((link) => link.addEventListener("click", () => {
      nav.classList.remove("is-open");
      menuButton.setAttribute("aria-expanded", "false");
      menuButton.setAttribute("aria-label", "Abrir menú");
    }));
  }

  const params = new URLSearchParams(window.location.search);
  if (params.has("tipo") && typeInput) {
    typeInput.value = params.get("tipo") || "";
    if (typeInput.value) selectType(typeInput.value);
  }
  if (form) {
    params.getAll("size[]").forEach((value) => {
      const input = form.querySelector('input[name="size[]"][value="' + value + '"]');
      if (input) input.checked = true;
    });
    params.getAll("gender[]").forEach((value) => {
      const input = form.querySelector('input[name="gender[]"][value="' + value + '"]');
      if (input) input.checked = true;
    });
  }
  if (citySelect && params.has("city")) citySelect.value = params.get("city");
  if (minAgeRange && params.has("edad_min")) minAgeRange.value = params.get("edad_min");
  if (ageRange && params.has("edad_max")) {
    ageRange.value = params.get("edad_max");
  }
  updateAgeLabels();
  if (sortSelect && params.has("orden")) sortSelect.value = params.get("orden");
  if (emptyState) emptyState.hidden = cards.length > 0;
  const pagination = document.querySelector(".pagination");
  if (pagination && cards.length === 0) pagination.hidden = true;
  if (count && cards.length === 0) count.innerHTML = "<strong>0</strong> mascotas disponibles";
  sortCards();
});
