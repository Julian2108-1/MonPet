document.addEventListener("DOMContentLoaded", () => {
  const storageKey = "monpet-perfil";
  const photoKey = "monpet-foto-perfil";
  const favoriteKey = "monpet-favoritos";
  const defaultProfile = {
    name: "Laura García",
    email: "laura.garcia@email.com",
    phone: "300 123 4567",
    documentType: "Cédula de ciudadanía",
    documentNumber: "1.234.567.890",
    birthDate: "1998-03-15",
    address: "Cra 15 # 123 - 45",
    city: "Bogotá, Cundinamarca"
  };
  const fieldLabels = {
    name: "Nombre completo",
    email: "Correo electrónico",
    phone: "Teléfono",
    documentType: "Tipo de documento",
    documentNumber: "Número de documento",
    birthDate: "Fecha de nacimiento",
    address: "Dirección",
    city: "Ciudad y departamento"
  };
  const dialog = document.getElementById("edit-profile-dialog");
  const form = document.getElementById("edit-profile-form");
  const message = document.getElementById("profile-save-message");
  const photo = document.getElementById("profile-photo");
  const photoInput = document.getElementById("photo-file");
  let profile = { ...defaultProfile };

  const readStorage = (key, fallback) => {
    try {
      const value = window.localStorage.getItem(key);
      return value === null ? fallback : value;
    } catch (error) {
      return fallback;
    }
  };

  try {
    const storedProfile = JSON.parse(readStorage(storageKey, "null"));
    if (storedProfile && typeof storedProfile === "object" && !Array.isArray(storedProfile)) {
      profile = { ...profile, ...storedProfile };
    }
  } catch (error) {
    profile = { ...defaultProfile };
  }

  const setProfileText = () => {
    document.querySelectorAll(".profile-name").forEach((element) => { element.textContent = profile.name; });
    document.querySelectorAll(".profile-email").forEach((element) => { element.textContent = profile.email; });
    document.querySelector('[data-profile-field="name"]').textContent = profile.name;
    document.querySelector('[data-profile-field="email"]').textContent = profile.email;
    document.querySelector('[data-profile-field="phone"]').textContent = profile.phone;
    document.querySelector('[data-profile-field="documentType"]').textContent = profile.documentType;
    document.querySelector('[data-profile-field="documentNumber"]').textContent = profile.documentNumber;
    document.querySelector('[data-profile-field="address"]').textContent = profile.address;
    document.querySelector('[data-profile-field="city"]').textContent = profile.city;
    document.querySelector('[data-profile-field="birthDate"]').textContent = formatBirthDate(profile.birthDate);
    photo.alt = "Foto de perfil de " + profile.name;
    document.querySelectorAll(".header-avatar").forEach((avatar) => { avatar.alt = "Foto de perfil de " + profile.name; });
  };

  function formatBirthDate(value) {
    if (!value) return "No especificada";
    const parts = value.split("-").map(Number);
    if (parts.length !== 3 || parts.some(Number.isNaN)) return value;
    const date = new Date(parts[0], parts[1] - 1, parts[2]);
    return new Intl.DateTimeFormat("es-CO", { day: "numeric", month: "long", year: "numeric" }).format(date);
  }

  setProfileText();
  const storedPhoto = readStorage(photoKey, "");
  if (storedPhoto) {
    photo.src = storedPhoto;
    document.querySelectorAll(".header-avatar").forEach((avatar) => { avatar.src = storedPhoto; });
  }

  try {
    const favorites = JSON.parse(readStorage(favoriteKey, "[]"));
    document.getElementById("favorite-count").textContent = Array.isArray(favorites) ? favorites.length : "0";
  } catch (error) {
    document.getElementById("favorite-count").textContent = "0";
  }

  const openDialog = () => {
    if (!dialog) return;
    Object.entries(profile).forEach(([key, value]) => {
      const input = form.elements.namedItem(key);
      if (input) input.value = value || "";
    });
    message.textContent = "";
    if (typeof dialog.showModal === "function") dialog.showModal();
    else dialog.setAttribute("open", "");
  };
  const closeDialog = () => {
    if (dialog?.open && typeof dialog.close === "function") dialog.close();
    else dialog?.removeAttribute("open");
  };

  document.getElementById("open-edit-profile")?.addEventListener("click", openDialog);
  document.getElementById("open-edit-profile-menu")?.addEventListener("click", openDialog);
  document.getElementById("close-edit-profile")?.addEventListener("click", closeDialog);
  document.getElementById("cancel-edit-profile")?.addEventListener("click", closeDialog);
  dialog?.addEventListener("click", (event) => {
    if (event.target === dialog) closeDialog();
  });

  form?.addEventListener("submit", (event) => {
    event.preventDefault();
    const values = new FormData(form);
    const updatedProfile = {};
    Object.keys(fieldLabels).forEach((key) => { updatedProfile[key] = String(values.get(key) || "").trim(); });
    if (!updatedProfile.name || !updatedProfile.email) {
      message.textContent = "Ingresa tu nombre y un correo válido.";
      return;
    }
    try {
      window.localStorage.setItem(storageKey, JSON.stringify(updatedProfile));
      profile = updatedProfile;
      setProfileText();
      message.textContent = "Perfil actualizado correctamente.";
      window.setTimeout(closeDialog, 450);
    } catch (error) {
      message.textContent = "No se pudieron guardar los cambios en este navegador.";
    }
  });

  document.getElementById("choose-photo")?.addEventListener("click", () => photoInput?.click());
  photoInput?.addEventListener("change", () => {
    const file = photoInput.files?.[0];
    if (!file) return;
    if (!file.type.startsWith("image/")) {
      photoInput.value = "";
      return;
    }
    const reader = new FileReader();
    reader.addEventListener("load", () => {
      const image = new Image();
      image.addEventListener("load", () => {
        const canvas = document.createElement("canvas");
        const maxDimension = 480;
        const scale = Math.min(1, maxDimension / Math.max(image.naturalWidth, image.naturalHeight));
        canvas.width = Math.max(1, Math.round(image.naturalWidth * scale));
        canvas.height = Math.max(1, Math.round(image.naturalHeight * scale));
        canvas.getContext("2d")?.drawImage(image, 0, 0, canvas.width, canvas.height);
        const resizedPhoto = canvas.toDataURL("image/jpeg", 0.82);
        try {
          window.localStorage.setItem(photoKey, resizedPhoto);
          photo.src = resizedPhoto;
          photo.alt = "Foto de perfil de " + profile.name;
          document.querySelectorAll(".header-avatar").forEach((avatar) => { avatar.src = resizedPhoto; });
        } catch (error) {
          window.alert("La imagen es demasiado grande para guardarla en este navegador. Elige una imagen más pequeña.");
        }
      });
      image.src = String(reader.result);
    });
    reader.readAsDataURL(file);
  });

  const header = document.querySelector(".header-container");
  const nav = document.querySelector(".main-nav");
  if (header && nav) {
    nav.id = "profile-navigation";
    const menuButton = document.createElement("button");
    menuButton.type = "button";
    menuButton.className = "mobile-menu-toggle";
    menuButton.setAttribute("aria-label", "Abrir menú");
    menuButton.setAttribute("aria-expanded", "false");
    menuButton.setAttribute("aria-controls", nav.id);
    menuButton.innerHTML = '<i class="fa-solid fa-bars" aria-hidden="true"></i>';
    header.insertBefore(menuButton, nav);
    menuButton.addEventListener("click", () => {
      const isOpen = menuButton.getAttribute("aria-expanded") === "true";
      menuButton.setAttribute("aria-expanded", String(!isOpen));
      menuButton.setAttribute("aria-label", isOpen ? "Abrir menú" : "Cerrar menú");
      nav.classList.toggle("is-open", !isOpen);
    });
    nav.querySelectorAll("a").forEach((link) => link.addEventListener("click", () => {
      nav.classList.remove("is-open");
      menuButton.setAttribute("aria-expanded", "false");
      menuButton.setAttribute("aria-label", "Abrir menú");
    }));
  }
});
