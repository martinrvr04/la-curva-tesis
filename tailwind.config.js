// tailwind.config.js
import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/**/*.ts",
  ],
  theme: {
    container: {
      center: true,
      padding: { DEFAULT: "1rem", sm: "1rem" },
      screens: { "2xl": "1200px" }, // ancho del mockup
    },
    extend: {
      fontFamily: {
        sans: ["Inter", ...defaultTheme.fontFamily.sans],
        display: ["'Fraunces'", ...defaultTheme.fontFamily.serif], // títulos
      },
      colors: {
        sand: {
          50:  "#FFF9EE",
          100: "#FDF6EA", // fondo
          200: "#F7ECD9", // tarjetas
          300: "#EEDBBB",
          400: "#E1C18E",
          500: "#CDA366",
          600: "#B4814B",
          700: "#8F5E34",
          800: "#6E4727",
          900: "#4C321C",
        },
        accent: {
          50:  "#FFF6E8",
          100: "#FFE9C7",
          600: "#d97706",
          700: "#b45309",
          800: "#92400e",
        },
      },
      borderRadius: {
        "2xl": "1.2rem",
        "3xl": "1.6rem",
      },
      boxShadow: {
        soft: "0 6px 18px rgba(20, 8, 0, 0.08)",
        card: "0 8px 26px rgba(20, 8, 0, 0.10)",
      },
      backgroundImage: {
        "hero-overlay":
          "linear-gradient(to top, rgba(76,50,28,.65), rgba(76,50,28,.15), transparent)",
      },
    },
  },
  plugins: [forms, typography],
};
