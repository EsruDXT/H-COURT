// ============================================================
// login.js — validasi & UX untuk login.blade.php
// TIDAK menghubungi database/API apapun di sini.
// Proses cek email/password ke database sepenuhnya ditangani
// controller Laravel (lihat "action" pada <form>). Pesan error dari
// backend sudah dirender langsung oleh Blade (@error, $errors->first()).
// Skrip ini hanya menangani: validasi input di sisi browser,
// fitur "Ingat saya" (localStorage), dan indikator loading.
// ============================================================

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("signInForm");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const rememberCheckbox = document.getElementById("rememberMe");
  const submitBtn = document.getElementById("submitBtn");
  const submitText = document.getElementById("submitText");
  const emailError = document.getElementById("emailError");
  const passwordError = document.getElementById("passwordError");
  const generalError = document.getElementById("generalError");

  // --- Fitur "Ingat saya": isi ulang email tersimpan (murni di browser) ---
  // Hanya dipakai kalau backend belum mengisi email lewat old('email').
  const rememberedEmail = localStorage.getItem("rememberedEmail");
  if (rememberedEmail && !emailInput.value) {
    emailInput.value = rememberedEmail;
    rememberCheckbox.checked = true;
  }

  form.addEventListener("submit", handleSubmit);

  function handleSubmit(e) {
    clearErrors(); // sembunyikan error lama (termasuk error kiriman Laravel) sebelum validasi ulang

    const email = emailInput.value.trim();
    const password = passwordInput.value;

    if (!validate(email, password)) {
      e.preventDefault(); // batalkan submit selama input belum valid
      return;
    }

    // Simpan/hapus email untuk "Ingat saya" — hanya di browser, bukan ke database
    if (rememberCheckbox.checked) {
      localStorage.setItem("rememberedEmail", email);
    } else {
      localStorage.removeItem("rememberedEmail");
    }

    // Input valid: biarkan form dikirim secara native (POST) ke route login.
    // Controller Laravel yang memverifikasi ke database.
    setLoading(true);
  }

  function validate(email, password) {
    let valid = true;

    if (!email) {
      showFieldError(emailInput, emailError, "Email wajib diisi.");
      valid = false;
    } else if (!isValidEmail(email)) {
      showFieldError(emailInput, emailError, "Format email tidak valid.");
      valid = false;
    }

    if (!password) {
      showFieldError(passwordInput, passwordError, "Kata sandi wajib diisi.");
      valid = false;
    } else if (password.length < 6) {
      showFieldError(passwordInput, passwordError, "Kata sandi minimal 6 karakter.");
      valid = false;
    }

    return valid;
  }

  function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  function showFieldError(input, errorEl, message) {
    input.classList.add("border-red-400", "focus:ring-red-400");
    errorEl.textContent = message;
    errorEl.classList.remove("hidden");
  }

  function clearErrors() {
    [emailInput, passwordInput].forEach((el) =>
      el.classList.remove("border-red-400", "focus:ring-red-400")
    );
    [emailError, passwordError].forEach((el) => {
      el.textContent = "";
      el.classList.add("hidden");
    });
    generalError.classList.add("hidden");
  }

  function setLoading(isLoading) {
    submitBtn.disabled = isLoading;
    submitText.textContent = isLoading ? "Memproses..." : "Masuk";
  }
});