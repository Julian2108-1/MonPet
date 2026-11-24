document.addEventListener("DOMContentLoaded", () => {
  const readKey = "monpet-notificaciones-leidas";
  const settingsKey = "monpet-notificaciones-preferencias";
  const cards = [...document.querySelectorAll(".notification-card")];
  const tabs = [...document.querySelectorAll(".notification-tab")];
  const emptyState = document.getElementById("notification-empty");
  const listEnd = document.getElementById("notifications-end");
  const readIds = new Set(readStoredArray(readKey));
  let activeFilter = "all";

  cards.forEach((card) => {
    const toggle = document.createElement("button");
    toggle.type = "button";
    toggle.className = "notification-read-toggle";
    toggle.setAttribute("aria-label", "Marcar notificación como leída");
    toggle.innerHTML = '<i class="fa-solid fa-check" aria-hidden="true"></i>';
    card.querySelector(".notification-title-line")?.append(toggle);
    toggle.addEventListener("click", () => markRead(card));
  });

  function readStoredArray(key) {
    try {
      const value = JSON.parse(window.localStorage.getItem(key) || "[]");
      return Array.isArray(value) ? value.filter((item) => typeof item === "string") : [];
    } catch (error) {
      return [];
    }
  }

  function saveReadIds() {
    try {
      window.localStorage.setItem(readKey, JSON.stringify([...readIds]));
    } catch (error) {
      // Keep the current page state even when browser storage is unavailable.
    }
  }

  function updateCounts() {
    const unread = cards.filter((card) => !readIds.has(card.dataset.id)).length;
    const read = cards.length - unread;
    document.querySelectorAll(".tab-count").forEach((count, index) => {
      count.textContent = String([cards.length, unread, read][index] || 0);
    });
    document.querySelectorAll("#header-unread-count, .sidebar-unread-count").forEach((count) => {
      count.textContent = String(unread);
      count.hidden = unread === 0;
    });
    const notificationLink = document.querySelector(".notification-btn");
    notificationLink?.setAttribute("aria-label", unread ? `Notificaciones, ${unread} sin leer` : "Notificaciones, no hay nuevas");
  }

  function render() {
    let visible = 0;
    cards.forEach((card) => {
      const isRead = readIds.has(card.dataset.id);
      card.classList.toggle("unread", !isRead);
      const toggle = card.querySelector(".notification-read-toggle");
      toggle?.setAttribute("aria-label", isRead ? "Marcar notificación como no leída" : "Marcar notificación como leída");
      toggle?.setAttribute("aria-pressed", String(isRead));
      const matches = activeFilter === "all"
        || (activeFilter === "unread" && !isRead)
        || (activeFilter === "read" && isRead);
      card.hidden = !matches;
      if (matches) visible += 1;
    });
    if (emptyState) emptyState.hidden = visible > 0;
    if (listEnd) listEnd.hidden = visible === 0;
    updateCounts();
  }

  function markRead(card) {
    const id = card.dataset.id;
    if (!id) return;
    if (readIds.has(id)) readIds.delete(id);
    else readIds.add(id);
    saveReadIds();
    render();
  }

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      activeFilter = tab.dataset.filter || "all";
      tabs.forEach((item) => {
        const active = item === tab;
        item.classList.toggle("active", active);
        item.setAttribute("aria-selected", String(active));
      });
      render();
    });
  });

  cards.forEach((card) => {
    card.addEventListener("click", (event) => {
      if (event.target.closest("a, button")) return;
      markRead(card);
    });
  });

  document.getElementById("mark-all-read")?.addEventListener("click", () => {
    cards.forEach((card) => readIds.add(card.dataset.id));
    saveReadIds();
    render();
  });

  let preferences = {};
  try {
    const stored = JSON.parse(window.localStorage.getItem(settingsKey) || "{}");
    if (stored && typeof stored === "object" && !Array.isArray(stored)) preferences = stored;
  } catch (error) {
    preferences = {};
  }
  const settingInputs = [...document.querySelectorAll("[data-setting]")];
  settingInputs.forEach((input) => {
    if (Object.hasOwn(preferences, input.dataset.setting)) input.checked = Boolean(preferences[input.dataset.setting]);
    input.addEventListener("change", () => {
      preferences[input.dataset.setting] = input.checked;
      const status = document.getElementById("settings-saved");
      try {
        window.localStorage.setItem(settingsKey, JSON.stringify(preferences));
        if (status) status.textContent = "Preferencias guardadas.";
      } catch (error) {
        if (status) status.textContent = "No se pudieron guardar las preferencias en este navegador.";
      }
    });
  });

  const header = document.querySelector(".header-container");
  const nav = document.querySelector(".main-nav");
  if (header && nav) {
    nav.id = "notifications-navigation";
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

  render();
});
