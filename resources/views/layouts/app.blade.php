<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Chart.js - deferred for non-blocking -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>

    <!-- Theme Styles -->
    <style>
        /* Saturation filter */
        :root {
            --color-saturation: 100%;
            --saturation-value: 1;
            --theme-primary: #3b82f6;
            --theme-primary-dark: #2563eb;
            --theme-primary-light: #eff6ff;
            --theme-accent: #6366f1;
        }

        /* Apply saturation filter to main content */
        .saturation-wrapper {
            filter: saturate(var(--saturation-value));
        }

        /* ========== IMPACTFUL THEME SYSTEM ========== */

        /* THEME: CORPORATIVO (Default) - White nav, dark text */
        /* No background override needed - nav is already white by default */
        /* Text is already dark from nav-link.blade.php base classes */

        /* ALL OTHER THEMES: Make nav text and icons WHITE */
        html[data-theme="natural"] nav *,
        html[data-theme="elegante"] nav *,
        html[data-theme="calido"] nav *,
        html[data-theme="oceano"] nav *,
        html[data-theme="corp2"] nav * {
            color: white !important;
        }

        /* But keep dropdown content dark (dropdowns have white background) */
        html[data-theme="natural"] nav [x-show] *,
        html[data-theme="elegante"] nav [x-show] *,
        html[data-theme="calido"] nav [x-show] *,
        html[data-theme="oceano"] nav [x-show] *,
        html[data-theme="corp2"] nav [x-show] * {
            color: #374151 !important;
        }

        /* Keep profile button text dark (has white background) */
        html[data-theme="natural"] nav .bg-white *,
        html[data-theme="elegante"] nav .bg-white *,
        html[data-theme="calido"] nav .bg-white *,
        html[data-theme="oceano"] nav .bg-white *,
        html[data-theme="corp2"] nav .bg-white * {
            color: #374151 !important;
        }

        /* THEME: NATURAL (Green/Emerald) - Fresh and organic */
        html[data-theme="natural"] {
            --theme-primary: #059669;
            --theme-primary-dark: #047857;
            --theme-primary-light: #ecfdf5;
            --theme-accent: #10b981;
        }

        html[data-theme="natural"] nav {
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        }

        html[data-theme="natural"] nav>div>div>div>a,
        html[data-theme="natural"] nav>div>div>div>a * {
            color: white !important;
        }

        /* Nav icons should be white */
        html[data-theme="natural"] nav .fa-bell,
        html[data-theme="natural"] nav .fa-adjust,
        html[data-theme="natural"] nav .text-gray-600,
        html[data-theme="natural"] nav .text-gray-400,
        html[data-theme="natural"] nav button>i {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Keep text dark in white background buttons (profile button) */
        html[data-theme="natural"] nav .bg-white,
        html[data-theme="natural"] nav .bg-white * {
            color: #374151 !important;
        }

        html[data-theme="natural"] nav [x-show],
        html[data-theme="natural"] nav [x-show] *:not(.text-blue-600):not(.text-green-500):not(.text-red-600) {
            color: #374151 !important;
        }

        /* Restore dropdown text colors for natural theme */
        html[data-theme="natural"] nav [x-show] {
            background: white !important;
        }

        html[data-theme="natural"] nav [x-show] * {
            color: #374151 !important;
        }

        html[data-theme="natural"] nav [x-show] .text-blue-600,
        html[data-theme="natural"] nav [x-show] .hover\:text-blue-700:hover {
            color: #059669 !important;
        }

        html[data-theme="natural"] nav [x-show] .text-gray-500 {
            color: #6b7280 !important;
        }

        html[data-theme="natural"] nav [x-show] .text-gray-900 {
            color: #111827 !important;
        }

        html[data-theme="natural"] nav .border-indigo-400 {
            border-color: #a7f3d0 !important;
        }

        html[data-theme="natural"] .bg-blue-600 {
            background-color: #059669 !important;
        }

        html[data-theme="natural"] .bg-blue-600:hover,
        html[data-theme="natural"] .hover\:bg-blue-700:hover {
            background-color: #047857 !important;
        }

        html[data-theme="natural"] .bg-blue-500 {
            background-color: #10b981 !important;
        }

        html[data-theme="natural"] .bg-blue-50,
        html[data-theme="natural"] .bg-blue-100 {
            background-color: #ecfdf5 !important;
        }

        html[data-theme="natural"] .text-blue-600,
        html[data-theme="natural"] .text-blue-700,
        html[data-theme="natural"] .text-blue-500 {
            color: #059669 !important;
        }

        html[data-theme="natural"] .hover\:text-blue-600:hover,
        html[data-theme="natural"] .hover\:text-blue-700:hover {
            color: #047857 !important;
        }

        html[data-theme="natural"] .border-blue-500,
        html[data-theme="natural"] .focus\:border-blue-500:focus {
            border-color: #059669 !important;
        }

        html[data-theme="natural"] .focus\:ring-blue-100:focus {
            --tw-ring-color: #d1fae5 !important;
        }

        html[data-theme="natural"] .from-blue-600 {
            --tw-gradient-from: #059669 !important;
        }

        html[data-theme="natural"] .to-blue-700 {
            --tw-gradient-to: #047857 !important;
        }

        html[data-theme="natural"] .bg-indigo-600,
        html[data-theme="natural"] .bg-indigo-500 {
            background-color: #059669 !important;
        }

        html[data-theme="natural"] .text-indigo-500,
        html[data-theme="natural"] .text-indigo-600 {
            color: #059669 !important;
        }

        html[data-theme="natural"] input[type="range"] {
            accent-color: #059669 !important;
        }

        html[data-theme="natural"] .shadow {
            box-shadow: 0 1px 3px rgba(5, 150, 105, 0.1), 0 1px 2px rgba(5, 150, 105, 0.06) !important;
        }

        /* THEME: ELEGANTE (Purple/Violet) - Sophisticated and premium */
        html[data-theme="elegante"] {
            --theme-primary: #7c3aed;
            --theme-primary-dark: #6d28d9;
            --theme-primary-light: #f5f3ff;
            --theme-accent: #8b5cf6;
        }

        html[data-theme="elegante"] nav {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%) !important;
        }

        html[data-theme="elegante"] nav>div>div>div>a,
        html[data-theme="elegante"] nav>div>div>div>a * {
            color: white !important;
        }

        /* Nav icons should be white */
        html[data-theme="elegante"] nav .fa-bell,
        html[data-theme="elegante"] nav .fa-adjust,
        html[data-theme="elegante"] nav .text-gray-600,
        html[data-theme="elegante"] nav .text-gray-400,
        html[data-theme="elegante"] nav button>i {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Keep text dark in white background buttons (profile button) */
        html[data-theme="elegante"] nav .bg-white,
        html[data-theme="elegante"] nav .bg-white * {
            color: #374151 !important;
        }

        /* Restore dropdown text colors for elegante theme */
        html[data-theme="elegante"] nav [x-show] {
            background: white !important;
        }

        html[data-theme="elegante"] nav [x-show],
        html[data-theme="elegante"] nav [x-show] *:not(.text-blue-600):not(.text-green-500):not(.text-red-600) {
            color: #374151 !important;
        }

        html[data-theme="elegante"] nav [x-show] .text-blue-600,
        html[data-theme="elegante"] nav [x-show] .hover\:text-blue-700:hover {
            color: #7c3aed !important;
        }

        html[data-theme="elegante"] nav [x-show] .text-gray-500 {
            color: #6b7280 !important;
        }

        html[data-theme="elegante"] nav [x-show] .text-gray-900 {
            color: #111827 !important;
        }

        html[data-theme="elegante"] nav .border-indigo-400 {
            border-color: #c4b5fd !important;
        }

        html[data-theme="elegante"] .bg-blue-600 {
            background-color: #7c3aed !important;
        }

        html[data-theme="elegante"] .bg-blue-600:hover,
        html[data-theme="elegante"] .hover\:bg-blue-700:hover {
            background-color: #6d28d9 !important;
        }

        html[data-theme="elegante"] .bg-blue-500 {
            background-color: #8b5cf6 !important;
        }

        html[data-theme="elegante"] .bg-blue-50,
        html[data-theme="elegante"] .bg-blue-100 {
            background-color: #f5f3ff !important;
        }

        html[data-theme="elegante"] .text-blue-600,
        html[data-theme="elegante"] .text-blue-700,
        html[data-theme="elegante"] .text-blue-500 {
            color: #7c3aed !important;
        }

        html[data-theme="elegante"] .hover\:text-blue-600:hover,
        html[data-theme="elegante"] .hover\:text-blue-700:hover {
            color: #6d28d9 !important;
        }

        html[data-theme="elegante"] .border-blue-500,
        html[data-theme="elegante"] .focus\:border-blue-500:focus {
            border-color: #7c3aed !important;
        }

        html[data-theme="elegante"] .focus\:ring-blue-100:focus {
            --tw-ring-color: #ede9fe !important;
        }

        html[data-theme="elegante"] .from-blue-600 {
            --tw-gradient-from: #7c3aed !important;
        }

        html[data-theme="elegante"] .to-blue-700 {
            --tw-gradient-to: #6d28d9 !important;
        }

        html[data-theme="elegante"] .bg-indigo-600,
        html[data-theme="elegante"] .bg-indigo-500 {
            background-color: #7c3aed !important;
        }

        html[data-theme="elegante"] .text-indigo-500,
        html[data-theme="elegante"] .text-indigo-600 {
            color: #7c3aed !important;
        }

        html[data-theme="elegante"] input[type="range"] {
            accent-color: #7c3aed !important;
        }

        html[data-theme="elegante"] .shadow {
            box-shadow: 0 1px 3px rgba(124, 58, 237, 0.1), 0 1px 2px rgba(124, 58, 237, 0.06) !important;
        }

        /* THEME: CALIDO (Orange/Amber) - Warm and energetic */
        html[data-theme="calido"] {
            --theme-primary: #ea580c;
            --theme-primary-dark: #c2410c;
            --theme-primary-light: #fff7ed;
            --theme-accent: #f97316;
        }

        html[data-theme="calido"] nav {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%) !important;
        }

        html[data-theme="calido"] nav>div>div>div>a,
        html[data-theme="calido"] nav>div>div>div>a * {
            color: white !important;
        }

        /* Nav icons should be white */
        html[data-theme="calido"] nav .fa-bell,
        html[data-theme="calido"] nav .fa-adjust,
        html[data-theme="calido"] nav .text-gray-600,
        html[data-theme="calido"] nav .text-gray-400,
        html[data-theme="calido"] nav button>i {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Keep text dark in white background buttons (profile button) */
        html[data-theme="calido"] nav .bg-white,
        html[data-theme="calido"] nav .bg-white * {
            color: #374151 !important;
        }

        /* Restore dropdown text colors for calido theme */
        html[data-theme="calido"] nav [x-show] {
            background: white !important;
        }

        html[data-theme="calido"] nav [x-show],
        html[data-theme="calido"] nav [x-show] *:not(.text-blue-600):not(.text-green-500):not(.text-red-600) {
            color: #374151 !important;
        }

        html[data-theme="calido"] nav [x-show] .text-blue-600,
        html[data-theme="calido"] nav [x-show] .hover\:text-blue-700:hover {
            color: #ea580c !important;
        }

        html[data-theme="calido"] nav [x-show] .text-gray-500 {
            color: #6b7280 !important;
        }

        html[data-theme="calido"] nav [x-show] .text-gray-900 {
            color: #111827 !important;
        }

        html[data-theme="calido"] nav .border-indigo-400 {
            border-color: #fed7aa !important;
        }

        html[data-theme="calido"] .bg-blue-600 {
            background-color: #ea580c !important;
        }

        html[data-theme="calido"] .bg-blue-600:hover,
        html[data-theme="calido"] .hover\:bg-blue-700:hover {
            background-color: #c2410c !important;
        }

        html[data-theme="calido"] .bg-blue-500 {
            background-color: #f97316 !important;
        }

        html[data-theme="calido"] .bg-blue-50,
        html[data-theme="calido"] .bg-blue-100 {
            background-color: #fff7ed !important;
        }

        html[data-theme="calido"] .text-blue-600,
        html[data-theme="calido"] .text-blue-700,
        html[data-theme="calido"] .text-blue-500 {
            color: #ea580c !important;
        }

        html[data-theme="calido"] .hover\:text-blue-600:hover,
        html[data-theme="calido"] .hover\:text-blue-700:hover {
            color: #c2410c !important;
        }

        html[data-theme="calido"] .border-blue-500,
        html[data-theme="calido"] .focus\:border-blue-500:focus {
            border-color: #ea580c !important;
        }

        html[data-theme="calido"] .focus\:ring-blue-100:focus {
            --tw-ring-color: #ffedd5 !important;
        }

        html[data-theme="calido"] .from-blue-600 {
            --tw-gradient-from: #ea580c !important;
        }

        html[data-theme="calido"] .to-blue-700 {
            --tw-gradient-to: #c2410c !important;
        }

        html[data-theme="calido"] .bg-indigo-600,
        html[data-theme="calido"] .bg-indigo-500 {
            background-color: #ea580c !important;
        }

        html[data-theme="calido"] .text-indigo-500,
        html[data-theme="calido"] .text-indigo-600 {
            color: #ea580c !important;
        }

        html[data-theme="calido"] input[type="range"] {
            accent-color: #ea580c !important;
        }

        html[data-theme="calido"] .shadow {
            box-shadow: 0 1px 3px rgba(234, 88, 12, 0.1), 0 1px 2px rgba(234, 88, 12, 0.06) !important;
        }

        /* THEME: OCEANO (Cyan/Teal) - Cool and refreshing */
        html[data-theme="oceano"] {
            --theme-primary: #0891b2;
            --theme-primary-dark: #0e7490;
            --theme-primary-light: #ecfeff;
            --theme-accent: #06b6d4;
        }

        html[data-theme="oceano"] nav {
            background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%) !important;
        }

        html[data-theme="oceano"] nav>div>div>div>a,
        html[data-theme="oceano"] nav>div>div>div>a * {
            color: white !important;
        }

        /* Nav icons should be white */
        html[data-theme="oceano"] nav .fa-bell,
        html[data-theme="oceano"] nav .fa-adjust,
        html[data-theme="oceano"] nav .text-gray-600,
        html[data-theme="oceano"] nav .text-gray-400,
        html[data-theme="oceano"] nav button>i {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Keep text dark in white background buttons (profile button) */
        html[data-theme="oceano"] nav .bg-white,
        html[data-theme="oceano"] nav .bg-white * {
            color: #374151 !important;
        }

        /* Restore dropdown text colors for oceano theme */
        html[data-theme="oceano"] nav [x-show] {
            background: white !important;
        }

        html[data-theme="oceano"] nav [x-show],
        html[data-theme="oceano"] nav [x-show] *:not(.text-blue-600):not(.text-green-500):not(.text-red-600) {
            color: #374151 !important;
        }

        html[data-theme="oceano"] nav [x-show] .text-blue-600,
        html[data-theme="oceano"] nav [x-show] .hover\:text-blue-700:hover {
            color: #0891b2 !important;
        }

        html[data-theme="oceano"] nav [x-show] .text-gray-500 {
            color: #6b7280 !important;
        }

        html[data-theme="oceano"] nav [x-show] .text-gray-900 {
            color: #111827 !important;
        }

        html[data-theme="oceano"] nav .border-indigo-400 {
            border-color: #a5f3fc !important;
        }

        html[data-theme="oceano"] .bg-blue-600 {
            background-color: #0891b2 !important;
        }

        html[data-theme="oceano"] .bg-blue-600:hover,
        html[data-theme="oceano"] .hover\:bg-blue-700:hover {
            background-color: #0e7490 !important;
        }

        html[data-theme="oceano"] .bg-blue-500 {
            background-color: #06b6d4 !important;
        }

        html[data-theme="oceano"] .bg-blue-50,
        html[data-theme="oceano"] .bg-blue-100 {
            background-color: #ecfeff !important;
        }

        html[data-theme="oceano"] .text-blue-600,
        html[data-theme="oceano"] .text-blue-700,
        html[data-theme="oceano"] .text-blue-500 {
            color: #0891b2 !important;
        }

        html[data-theme="oceano"] .hover\:text-blue-600:hover,
        html[data-theme="oceano"] .hover\:text-blue-700:hover {
            color: #0e7490 !important;
        }

        html[data-theme="oceano"] .border-blue-500,
        html[data-theme="oceano"] .focus\:border-blue-500:focus {
            border-color: #0891b2 !important;
        }

        html[data-theme="oceano"] .focus\:ring-blue-100:focus {
            --tw-ring-color: #cffafe !important;
        }

        html[data-theme="oceano"] .from-blue-600 {
            --tw-gradient-from: #0891b2 !important;
        }

        html[data-theme="oceano"] .to-blue-700 {
            --tw-gradient-to: #0e7490 !important;
        }

        html[data-theme="oceano"] .bg-indigo-600,
        html[data-theme="oceano"] .bg-indigo-500 {
            background-color: #0891b2 !important;
        }

        html[data-theme="oceano"] .text-indigo-500,
        html[data-theme="oceano"] .text-indigo-600 {
            color: #0891b2 !important;
        }

        html[data-theme="oceano"] input[type="range"] {
            accent-color: #0891b2 !important;
        }

        html[data-theme="oceano"] .shadow {
            box-shadow: 0 1px 3px rgba(8, 145, 178, 0.1), 0 1px 2px rgba(8, 145, 178, 0.06) !important;
        }

        /* THEME: CORPORATIVO 2 (Blue/Sky) - Modern and vibrant blue */
        html[data-theme="corp2"] {
            --theme-primary: #1881f0;
            --theme-primary-dark: #191b53;
            --theme-primary-light: #e8f4ff;
            --theme-accent: #1881f0;
        }

        html[data-theme="corp2"] nav {
            background: linear-gradient(135deg, #191b53 0%, #02042a 100%) !important;
        }

        html[data-theme="corp2"] nav>div>div>div>a,
        html[data-theme="corp2"] nav>div>div>div>a * {
            color: white !important;
        }

        /* Nav icons should be white */
        html[data-theme="corp2"] nav .fa-bell,
        html[data-theme="corp2"] nav .fa-adjust,
        html[data-theme="corp2"] nav .text-gray-600,
        html[data-theme="corp2"] nav .text-gray-400,
        html[data-theme="corp2"] nav button>i {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Keep text dark in white background buttons (profile button) */
        html[data-theme="corp2"] nav .bg-white,
        html[data-theme="corp2"] nav .bg-white * {
            color: #374151 !important;
        }

        /* Restore dropdown text colors for corp2 theme */
        html[data-theme="corp2"] nav [x-show] {
            background: white !important;
        }

        html[data-theme="corp2"] nav [x-show],
        html[data-theme="corp2"] nav [x-show] *:not(.text-blue-600):not(.text-green-500):not(.text-red-600) {
            color: #374151 !important;
        }

        html[data-theme="corp2"] nav [x-show] .text-blue-600,
        html[data-theme="corp2"] nav [x-show] .hover\:text-blue-700:hover {
            color: #1881f0 !important;
        }

        html[data-theme="corp2"] nav [x-show] .text-gray-500 {
            color: #6b7280 !important;
        }

        html[data-theme="corp2"] nav [x-show] .text-gray-900 {
            color: #111827 !important;
        }

        html[data-theme="corp2"] nav .border-indigo-400 {
            border-color: #1881f0 !important;
        }

        /* Buttons - Deep blue theme */
        html[data-theme="corp2"] .bg-blue-600 {
            background-color: #1881f0 !important;
        }

        html[data-theme="corp2"] .bg-blue-600:hover,
        html[data-theme="corp2"] .hover\:bg-blue-700:hover {
            background-color: #191b53 !important;
        }

        html[data-theme="corp2"] .bg-blue-500 {
            background-color: #1881f0 !important;
        }

        /* Light backgrounds */
        html[data-theme="corp2"] .bg-blue-50,
        html[data-theme="corp2"] .bg-blue-100 {
            background-color: #e8f4ff !important;
        }

        /* Text colors */
        html[data-theme="corp2"] .text-blue-600,
        html[data-theme="corp2"] .text-blue-700,
        html[data-theme="corp2"] .text-blue-500 {
            color: #1881f0 !important;
        }

        html[data-theme="corp2"] .hover\:text-blue-600:hover,
        html[data-theme="corp2"] .hover\:text-blue-700:hover {
            color: #191b53 !important;
        }

        /* Borders */
        html[data-theme="corp2"] .border-blue-500,
        html[data-theme="corp2"] .focus\:border-blue-500:focus {
            border-color: #1881f0 !important;
        }

        html[data-theme="corp2"] .focus\:ring-blue-100:focus {
            --tw-ring-color: #b3d9ff !important;
        }

        /* Gradients */
        html[data-theme="corp2"] .from-blue-600 {
            --tw-gradient-from: #1881f0 !important;
        }

        html[data-theme="corp2"] .to-blue-700 {
            --tw-gradient-to: #191b53 !important;
        }

        /* Indigo replacements */
        html[data-theme="corp2"] .bg-indigo-600,
        html[data-theme="corp2"] .bg-indigo-500 {
            background-color: #1881f0 !important;
        }

        html[data-theme="corp2"] .text-indigo-500,
        html[data-theme="corp2"] .text-indigo-600 {
            color: #1881f0 !important;
        }

        /* Range inputs */
        html[data-theme="corp2"] input[type="range"] {
            accent-color: #1881f0 !important;
        }

        /* Shadows with blue tint */
        html[data-theme="corp2"] .shadow {
            box-shadow: 0 1px 3px rgba(25, 27, 83, 0.15), 0 1px 2px rgba(25, 27, 83, 0.1) !important;
        }

        /* Cards with subtle blue tint */
        html[data-theme="corp2"] .bg-white {
            background-color: #fafbff !important;
        }

        /* Keep dropdowns pure white */
        html[data-theme="corp2"] nav [x-show],
        html[data-theme="corp2"] [x-show].shadow-xl {
            background-color: #ffffff !important;
        }

        /* Icon backgrounds */
        html[data-theme="corp2"] .bg-blue-100 {
            background-color: #e8f4ff !important;
        }

        html[data-theme="corp2"] .text-blue-800 {
            color: #02042a !important;
        }

        /* Dark mode - Soft and comfortable */
        html.dark-mode {
            --dm-bg-main: #1a1b1e;
            --dm-bg-card: #25262b;
            --dm-bg-elevated: #2c2d32;
            --dm-text-primary: #ffffff;
            --dm-text-secondary: #c0c0c5;
            --dm-border: #373a40;
        }

        html.dark-mode body,
        html.dark-mode .min-h-screen {
            background: var(--dm-bg-main) !important;
        }

        html.dark-mode .bg-gray-50,
        html.dark-mode .bg-gray-100 {
            background: var(--dm-bg-main) !important;
        }

        html.dark-mode .bg-white {
            background: var(--dm-bg-card) !important;
        }

        /* Dark mode nav - with higher specificity to work with themes */
        html.dark-mode nav,
        html.dark-mode[data-theme] nav {
            background: var(--dm-bg-card) !important;
            border-color: var(--dm-border) !important;
        }

        /* Override theme nav text colors for dark mode */
        html.dark-mode[data-theme] nav * {
            color: var(--dm-text-primary) !important;
        }

        html.dark-mode .text-gray-900,
        html.dark-mode .text-gray-800 {
            color: var(--dm-text-primary) !important;
        }

        html.dark-mode .text-gray-700,
        html.dark-mode .text-gray-600 {
            color: var(--dm-text-secondary) !important;
        }

        html.dark-mode .text-gray-500,
        html.dark-mode .text-gray-400 {
            color: #9ca3af !important;
        }

        html.dark-mode .border-gray-200,
        html.dark-mode .border-gray-100 {
            border-color: var(--dm-border) !important;
        }

        html.dark-mode .shadow-sm,
        html.dark-mode .shadow-md {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3) !important;
        }

        /* Dark mode - Form elements */
        html.dark-mode input,
        html.dark-mode select,
        html.dark-mode textarea {
            background: var(--dm-bg-elevated) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-text-primary) !important;
        }

        html.dark-mode input::placeholder,
        html.dark-mode textarea::placeholder {
            color: var(--dm-text-secondary) !important;
        }

        /* Dark mode - Dropdowns */
        html.dark-mode .shadow-xl,
        html.dark-mode .shadow-lg,
        html.dark-mode [x-show] {
            background: var(--dm-bg-elevated) !important;
            border-color: var(--dm-border) !important;
        }

        /* Dark mode - Keep bright text white */
        html.dark-mode .text-white {
            color: #ffffff !important;
        }

        /* Dark mode - Buttons */
        html.dark-mode .bg-blue-600 {
            background: #4c6ef5 !important;
        }

        html.dark-mode .hover\:bg-blue-700:hover {
            background: #4263eb !important;
        }

        /* Dark mode - Labels and text elements */
        html.dark-mode label {
            color: var(--dm-text-primary) !important;
        }

        html.dark-mode .font-semibold,
        html.dark-mode .font-medium {
            color: var(--dm-text-primary) !important;
        }

        /* Ensure theme cards and dropdown text are visible in dark mode */
        html.dark-mode .theme-card span,
        html.dark-mode .text-xs.font-medium {
            color: var(--dm-text-secondary) !important;
        }

        html.dark-mode .uppercase {
            color: var(--dm-text-secondary) !important;
        }

        /* Dark mode - Links and status badges */
        html.dark-mode .text-blue-600,
        html.dark-mode .text-blue-700 {
            color: #748ffc !important;
        }

        html.dark-mode .bg-blue-50 {
            background: #1c2333 !important;
        }

        html.dark-mode .bg-amber-50 {
            background: #2d2620 !important;
        }

        html.dark-mode .text-amber-700 {
            color: #ffc078 !important;
        }

        html.dark-mode .bg-red-50 {
            background: #2d2020 !important;
        }

        html.dark-mode .text-red-700 {
            color: #ffa8a8 !important;
        }

        html.dark-mode .bg-green-50 {
            background: #1d2d20 !important;
        }

        html.dark-mode .text-green-700 {
            color: #8ce99a !important;
        }

        /* Dark mode - Purple and Cyan badges (Asignada, Propia) */
        html.dark-mode .bg-purple-50 {
            background: #2d2033 !important;
        }

        html.dark-mode .text-purple-700 {
            color: #d0bfff !important;
        }

        html.dark-mode .border-purple-200 {
            border-color: #5c4a6e !important;
        }

        html.dark-mode .bg-cyan-50 {
            background: #1a2d2d !important;
        }

        html.dark-mode .text-cyan-700 {
            color: #99e9f2 !important;
        }

        html.dark-mode .border-cyan-200 {
            border-color: #3d5c5c !important;
        }

        html.dark-mode .hover\:bg-gray-50:hover,
        html.dark-mode .hover\:bg-gray-100:hover {
            background: var(--dm-bg-elevated) !important;
        }

        /* Dark mode - Priority indicators */
        html.dark-mode .bg-red-500 {
            background: #e03131 !important;
        }

        html.dark-mode .bg-amber-500 {
            background: #fab005 !important;
        }

        html.dark-mode .bg-green-500 {
            background: #40c057 !important;
        }

        /* Dark mode - Statistics badges (Users table) */
        html.dark-mode .bg-blue-100 {
            background: #1c2333 !important;
        }

        html.dark-mode .text-blue-800 {
            color: #74c0fc !important;
        }

        html.dark-mode .bg-green-100 {
            background: #1d2d20 !important;
        }

        html.dark-mode .text-green-800 {
            color: #8ce99a !important;
        }

        html.dark-mode .bg-purple-100 {
            background: #2d2033 !important;
        }

        html.dark-mode .text-purple-800 {
            color: #d0bfff !important;
        }
    </style>

    <!-- Load settings from localStorage before render -->
    <script>
        (function () {
            // Dark mode
            var darkMode = localStorage.getItem('dark-mode') === 'true';
            if (darkMode) {
                document.documentElement.classList.add('dark-mode');
            }
            // Saturation
            var saturation = localStorage.getItem('color-saturation') || '100';
            var saturationValue = saturation / 100;
            document.documentElement.style.setProperty('--color-saturation', saturation + '%');
            document.documentElement.style.setProperty('--saturation-value', saturationValue);
            // Theme
            var theme = localStorage.getItem('app-theme');
            if (theme && theme !== 'corporativo') {
                document.documentElement.setAttribute('data-theme', theme);
            }
        })();
    </script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 saturation-wrapper">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>
    </div>
    @yield('scripts')

    <!-- Theme Switcher Script -->
    <script>
        // Set saturation level
        function setSaturation(value) {
            var saturationValue = value / 100;
            document.documentElement.style.setProperty('--color-saturation', value + '%');
            document.documentElement.style.setProperty('--saturation-value', saturationValue);
            localStorage.setItem('color-saturation', value);

            // Update display
            var display = document.getElementById('saturation-value');
            if (display) display.textContent = value + '%';
        }

        // Toggle dark mode
        function toggleDarkMode() {
            var html = document.documentElement;
            var isOn = html.classList.toggle('dark-mode');
            localStorage.setItem('dark-mode', isOn);
            updateDarkModeButton(isOn);
        }

        // Update dark mode button visuals
        function updateDarkModeButton(isOn) {
            // Desktop toggle
            var toggle = document.getElementById('dark-mode-toggle');
            var knob = document.getElementById('dark-mode-knob');
            if (toggle && knob) {
                if (isOn) {
                    toggle.classList.remove('bg-gray-300');
                    toggle.classList.add('bg-indigo-600');
                    knob.classList.remove('translate-x-1');
                    knob.classList.add('translate-x-6');
                } else {
                    toggle.classList.remove('bg-indigo-600');
                    toggle.classList.add('bg-gray-300');
                    knob.classList.remove('translate-x-6');
                    knob.classList.add('translate-x-1');
                }
            }
            // Mobile toggle
            var mobileToggle = document.getElementById('mobile-dark-toggle');
            var mobileKnob = document.getElementById('mobile-dark-knob');
            if (mobileToggle && mobileKnob) {
                if (isOn) {
                    mobileToggle.classList.remove('bg-gray-300');
                    mobileToggle.classList.add('bg-indigo-600');
                    mobileKnob.classList.remove('translate-x-1');
                    mobileKnob.classList.add('translate-x-6');
                } else {
                    mobileToggle.classList.remove('bg-indigo-600');
                    mobileToggle.classList.add('bg-gray-300');
                    mobileKnob.classList.remove('translate-x-6');
                    mobileKnob.classList.add('translate-x-1');
                }
            }

        }

        // Set theme
        function setTheme(themeName) {
            document.documentElement.removeAttribute('data-theme');
            if (themeName !== 'corporativo') {
                document.documentElement.setAttribute('data-theme', themeName);
            }
            localStorage.setItem('app-theme', themeName);

            // Update theme card visuals
            document.querySelectorAll('.theme-card').forEach(card => {
                card.classList.remove('ring-2', 'ring-offset-2', 'ring-gray-500', 'border-gray-400');
                card.classList.add('border-gray-200');
            });
            document.querySelectorAll(`[data-theme-name="${themeName}"]`).forEach(card => {
                card.classList.remove('border-gray-200');
                card.classList.add('ring-2', 'ring-offset-2', 'ring-gray-500', 'border-gray-400');
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            // Saturation
            var saturation = localStorage.getItem('color-saturation') || '100';
            var slider = document.getElementById('saturation-slider');
            var mobileSlider = document.getElementById('mobile-saturation-slider');
            var display = document.getElementById('saturation-value');
            if (slider) slider.value = saturation;
            if (mobileSlider) mobileSlider.value = saturation;
            if (display) display.textContent = saturation + '%';

            // Dark mode
            var darkMode = localStorage.getItem('dark-mode') === 'true';
            updateDarkModeButton(darkMode);

            // Theme
            var theme = localStorage.getItem('app-theme') || 'corporativo';
            setTheme(theme);
        });
    </script>
</body>

</html>