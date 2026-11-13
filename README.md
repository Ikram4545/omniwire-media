# OmniWire Media (omniwiremedia.com)

> **Next-Generation Digital News, Interactive 3D Smartphone Specs Portal & Live Sports Hub**

[![Live Website](https://img.shields.io/badge/Production-Live%20Website-emerald?style=for-the-badge&logo=google-chrome)](https://omniwiremedia.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Three.js](https://img.shields.io/badge/Three.js-3D%20WebGL-black?style=for-the-badge&logo=three.js)](https://threejs.org)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v3-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)

---

## 🚀 Overview

**OmniWire Media** is an enterprise-grade digital news network and digital market platform engineered specifically for the Pakistani and global digital audience. It combines hard-hitting editorial reporting with high-utility interactive web engines:

1. **📱 3D Interactive Smartphone Hub (`/mobiles`)**:
   * Complete catalog of **52+ top flagship smartphones** (Apple iPhone, Samsung Galaxy, Infinix, Vivo, Xiaomi).
   * **Three.js 360° Hardware Viewer**: Rotate phones in 3D WebGL space with authentic manufacturer finishes (e.g., iPhone 16 Pro Max *Desert Titanium*, Samsung S24 Ultra *Titanium Violet*, Infinix GT 20 Pro *Cyber Mecha*).
   * **Real-Time FBR PTA Tax Calculator**: Instant customs duty calculation comparing **Passport vs. CNIC** registration.
   * **0% Markup Installment Plans**: 6-month and 12-month bank installment breakdowns.

2. **🏏 Live Sports & Cricket Hub (`/sports/live`)**:
   * Live ball-by-ball scorecards for ICC tournaments, PSL, and international matches.
   * Real-time match status, live stream player integration, and football scoreboard tables.

3. **📰 Editorial Intelligence & Daily Market Rates**:
   * 100+ in-depth verified news analyses across AI & Tech, Cybersecurity, Finance, and Digital Business.
   * Interactive market trackers for Gold Rates (24K/22K Tola) and Petroleum prices in Pakistan.
   * Contextual internal linking and full Schema.org (`Product`, `NewsArticle`, `BreadcrumbList`) markup.

---

## 🛠️ Architecture & Tech Stack

* **Backend**: Modular PHP 8.1+ with PDO SQLite and clean `.htaccess` routing.
* **Frontend**: Tailwind CSS, Lucide Icons, and Vanilla JavaScript.
* **3D Hardware Rendering**: Three.js (r128) WebGL with custom PBR materials, ACESFilmicToneMapping, dynamic studio rim lighting, and canvas screen reflection.
* **Database**: Lightweight zero-maintenance SQLite database with atomic schema migrations.
* **Caching & Acceleration**: In-memory cache layer, LiteSpeed server optimization, and Cloudflare edge delivery.

---

## 📂 Repository Structure

```
├── index.php                 # Core Portal Application & Homepage
├── mobile.php                # 3D Three.js Hardware Engine & PTA Tax Detail Page
├── mobiles.php               # 52+ Flagship Smartphone Catalog with Live Filters
├── sports-live.php           # Real-Time Live Sports & Cricket Scorecard Hub
├── .htaccess                 # Clean URL Rewrite Rules (/mobiles, /mobile/:slug, /sports/live)
├── sitemap.xml               # Complete 170+ Indexable URLs Sitemap
├── robots.txt                # Search Engine Bot Directives
├── database/                 # Structured Datasets & Schemas
│   └── master_mobile_dataset.json
├── static/                   # Static Assets
│   ├── css/
│   ├── js/
│   └── images/
│       ├── omniwire-logo.svg
│       └── mobiles/          # 300+ Verified 1200x1200px Studio Product Images
└── templates/                # Modular Header, Footer & UI Partials
```

---

## 🌐 Live Endpoints

* **Production URL**: [https://omniwiremedia.com](https://omniwiremedia.com)
* **Mobile Hub**: [https://omniwiremedia.com/mobiles](https://omniwiremedia.com/mobiles)
* **Live Sports**: [https://omniwiremedia.com/sports/live](https://omniwiremedia.com/sports/live)
* **iPhone 16 Pro Max (3D View)**: [https://omniwiremedia.com/mobile/apple-iphone-16-pro-max](https://omniwiremedia.com/mobile/apple-iphone-16-pro-max)
* **Samsung Galaxy S24 Ultra**: [https://omniwiremedia.com/mobile/samsung-galaxy-s24-ultra](https://omniwiremedia.com/mobile/samsung-galaxy-s24-ultra)
* **Infinix GT 20 Pro**: [https://omniwiremedia.com/mobile/infinix-gt-20-pro](https://omniwiremedia.com/mobile/infinix-gt-20-pro)

---

## 👨‍💻 Author & License

Maintained by **Ikram** ([@Ikram4545](https://github.com/Ikram4545)).
Licensed under the [MIT License](LICENSE).
