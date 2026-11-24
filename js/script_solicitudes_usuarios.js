document.addEventListener("DOMContentLoaded", () => {
  const list = document.getElementById("requests-list");
  const cards = [...document.querySelectorAll(".request-card")];
  const filters = [...document.querySelectorAll(".request-filter")];
  const pagination = document.getElementById("requests-pagination");
  const emptyState = document.getElementById("requests-empty");
  const sortSelect = document.getElementById("sort-order");
  const pageSize = 4;
  let activeFilter = "all";
  let currentPage = 1;

  function matchingCards() {
    return cards.filter((card) => activeFilter === "all" || card.dataset.status === activeFilter);
  }

  function sortCards() {
    if (!list) return;
    const mode = sortSelect?.value || "recent";
    const sorted = [...cards].sort((a, b) => {
      if (mode === "name") return (a.dataset.name || "").localeCompare(b.dataset.name || "", "es");
      const difference = new Date(a.dataset.date) - new Date(b.dataset.date);
      return mode === "oldest" ? difference : -difference;
    });
    sorted.forEach((card) => list.append(card));
  }

  function renderPagination(pageCount) {
    if (!pagination) return;
    pagination.replaceChildren();
    const createButton = (label, page, options = {}) => {
      const button = document.createElement("button");
      button.type = "button";
      button.dataset.page = String(page);
      button.setAttribute("aria-label", options.label || `Página ${label}`);
      button.textContent = label;
      if (options.icon) button.innerHTML = `<i class="fa-solid ${options.icon}" aria-hidden="true"></i>`;
      if (page === currentPage) {
        button.classList.add("active");
        button.setAttribute("aria-current", "page");
      }
      if (options.disabled) button.disabled = true;
      pagination.append(button);
    };
    createButton("", "previous", { icon: "fa-chevron-left", label: "Página anterior", disabled: currentPage <= 1 });
    for (let page = 1; page <= pageCount; page += 1) createButton(String(page), page);
    createButton("", "next", { icon: "fa-chevron-right", label: "Página siguiente", disabled: currentPage >= pageCount });
    pagination.hidden = pageCount <= 1;
  }

  function render() {
    const matching = matchingCards();
    const pageCount = Math.ceil(matching.length / pageSize);
    currentPage = Math.max(1, Math.min(currentPage, pageCount || 1));
    const start = (currentPage - 1) * pageSize;
    const visibleCards = new Set(matching.slice(start, start + pageSize));
    cards.forEach((card) => { card.hidden = !visibleCards.has(card); });
    if (emptyState) emptyState.hidden = matching.length > 0;

    const counts = cards.reduce((result, card) => {
      result[card.dataset.status] = (result[card.dataset.status] || 0) + 1;
      return result;
    }, {});
    filters.forEach((button) => {
      const status = button.dataset.filter;
      const count = status === "all" ? cards.length : (counts[status] || 0);
      const countElement = button.querySelector(".filter-count");
      if (countElement) countElement.textContent = String(count);
    });
    renderPagination(pageCount);
  }

  filters.forEach((button) => {
    button.addEventListener("click", () => {
      activeFilter = button.dataset.filter || "all";
      currentPage = 1;
      filters.forEach((item) => {
        const active = item === button;
        item.classList.toggle("active", active);
        item.setAttribute("aria-selected", String(active));
      });
      render();
    });
  });

  sortSelect?.addEventListener("change", () => {
    currentPage = 1;
    sortCards();
    render();
  });

  pagination?.addEventListener("click", (event) => {
    const button = event.target.closest("button[data-page]");
    if (!button || button.disabled) return;
    const requestedPage = button.dataset.page;
    if (requestedPage === "previous") currentPage -= 1;
    else if (requestedPage === "next") currentPage += 1;
    else currentPage = Number(requestedPage);
    render();
    document.querySelector(".requests-panel")?.scrollIntoView({ behavior: "smooth", block: "start" });
  });

  const dialog = document.getElementById("request-dialog");
  const dialogBody = document.getElementById("request-dialog-body");
  document.querySelectorAll("[data-details]").forEach((button) => {
    button.addEventListener("click", () => {
      const card = button.closest(".request-card");
      if (!card) return;
      const name = card.querySelector(".request-pet-info h2")?.textContent.trim() || "la mascota";
      const status = card.querySelector(".request-status strong")?.textContent.trim() || "En proceso";
      const date = card.querySelector(".request-submission time")?.textContent.trim() || "";
      const modalTitle = document.getElementById("request-dialog-title");
      if (modalTitle) modalTitle.textContent = `Solicitud para ${name}`;
      if (dialogBody) dialogBody.textContent = `Estado: ${status}. Solicitud enviada el ${date}. Si necesitas ayuda con el seguimiento, contacta a hola@monpet.com.`;
      button.closest("details")?.removeAttribute("open");
      if (typeof dialog?.showModal === "function") dialog.showModal();
      else dialog?.setAttribute("open", "");
    });
  });

  dialog?.addEventListener("click", (event) => {
    if (event.target === dialog) dialog.close();
  });

  const header = document.querySelector(".header-container");
  const nav = document.querySelector(".main-nav");
  if (header && nav) {
    nav.id = "requests-navigation";
    const menuButton = document.createElement("button");
    menuButton.type = "button";
    menuButton.className = "mobile-menu-toggle";
    menuButton.setAttribute("aria-label", "Abrir menú");
    menuButton.setAttribute("aria-expanded", "false");
    menuButton.setAttribute("aria-controls", nav.id);
    menuButton.innerHTML = '<i class="fa-solid fa-bars" aria-hidden="true"></i>';
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

  sortCards();
  render();
});
