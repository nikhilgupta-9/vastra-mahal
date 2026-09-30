# 👑 The Vastra Mahal (द वस्त्र महल) - Luxury Women's Ethnic Wear & Dynamic Boutique

A modern, dynamic e-commerce catalog and boutique management system developed in **PHP** and **MySQL** tailored specifically for luxury women's ethnic fashion: **Suits, Sarees, Girl Suits, Lehengas, and Kurtis**.

---

## ✨ Key Highlights

- **🌸 Bespoke Collections:** Dynamic catalog featuring Handcrafted Sarees (Banarasi, Kanjivaram, Organza), Designer Suits & Anarkalis, Bridal & Festive Lehengas, and everyday Designer Kurtis.
- **👧 Dedicated "Girl Suits" Section:** Special showcase for young girls and teens featuring trendy Peplum Sharara sets, Punjabi Salwar suits, and festive designer wear.
- **📍 Smart Boutique QR & Location Integration:**
  - Integrated with the physical showroom Google Profile & Maps location: [View on Google Maps](https://share.google/4X3xcWrgXxZ754XWa).
  - Built-in Branded QR Code system allowing walk-in customers or social media visitors to scan and instantly access the digital catalog or get turn-by-turn driving directions to the boutique.
  - Dedicated mobile-friendly landing page (`store-qr.php`) and a printable showroom counter standee in the admin panel (`admin/store-qr.php`).
- **💬 Direct WhatsApp Consultation & Inquiries:**
  - One-click WhatsApp product inquiry pre-filled with product title and SKU.
  - Interactive Contact inquiry form storing messages dynamically in the database.
- **🛡️ Full-Featured Admin Panel:**
  - Dashboard analytics (total products, categories, active inquiries).
  - Product Management: Add, edit, toggle stock, upload bespoke high-res images, and delete.
  - Category Management: Dynamic category creation and management.
  - Inquiries Management: Review customer messages with 1-click WhatsApp instant response.
  - Storefront QR Standee Generator: Ready-to-print counter display.

---

## 🛠️ Technology Stack

- **Backend:** PHP 8.x
- **Database:** MySQL / MariaDB (PDO for secure prepared statements)
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5
- **Icons & Assets:** FontAwesome 6.5.1 + Bespoke AI Generated Ethnic Fashion Visuals

---

## 🚀 Quick Setup & Installation

### 1. Requirements
- XAMPP / WAMP / LAMP stack running **Apache** and **MySQL**.
- PHP 7.4 or higher (PHP 8.x recommended).

### 2. Database Import
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Create a new database named `vastra_mahal_db` with collation `utf8mb4_unicode_ci`.
3. Import the SQL dump located at:
   ```
   database/vastra_mahal_db.sql
   ```
   *(Or run the SQL file directly using MySQL command line)*.

### 3. Smart Multi-Environment Configuration (Local vs Production)
The application automatically detects whether it is running on **Localhost (XAMPP)** or a **Live Production Server**:
- **On Local (XAMPP/WAMP):** Works immediately out-of-the-box with default credentials (`root`, no password, `vastra_mahal_db`).
- **On Live Production Server (cPanel / Hosting / VPS):**
  Create `config/db.custom.php` (copy from `config/db.custom.example.php`) once on your server:
  ```php
  <?php
  return [
      'DB_HOST' => 'localhost',
      'DB_USER' => 'your_live_db_user',
      'DB_PASS' => 'your_live_db_password',
      'DB_NAME' => 'your_live_db_name',
  ];
  ```
  *(Or use a `.env` file in the root directory)*.
  
  > 🛡️ **Git Safe:** Both `config/db.custom.php` and `.env` are in `.gitignore`. You can safely push to and pull from GitHub without your live production password ever getting overwritten or committed!

### 4. Running the Website
Place the project folder in your web server root (e.g., `D:/xampp/htdocs/vastra-mahal`) and access:
- **Customer Storefront:** `http://localhost/vastra-mahal/`
- **Catalog & Products:** `http://localhost/vastra-mahal/products.php`
- **Girl Suits Collection:** `http://localhost/vastra-mahal/products.php?category=girl-suits`
- **QR Scan Landing Page:** `http://localhost/vastra-mahal/store-qr.php`
- **Admin Panel:** `http://localhost/vastra-mahal/admin/`

---

## 🔐 Default Admin Credentials

- **URL:** `http://localhost/vastra-mahal/admin/login.php`
- **Username:** `admin`
- **Password:** `admin123`

---

## 📂 Project Structure

```text
vastra-mahal/
├── admin/                     # Admin control panel
│   ├── includes/              # Admin header, navigation, footer
│   ├── index.php              # Dashboard
│   ├── products.php           # Product list
│   ├── product-add.php        # Create product
│   ├── product-edit.php       # Update product
│   ├── categories.php         # Manage categories
│   ├── inquiries.php          # Manage contact form inquiries
│   ├── store-qr.php           # Printable boutique QR counter standee
│   └── login.php / logout.php # Admin authentication
├── config/
│   └── db.php                 # MySQL PDO connection & constants
├── css/
│   ├── bootstrap.min.css
│   ├── vastra-mahal-luxury.css# Custom royal gold/burgundy boutique styling
│   └── style.css
├── database/
│   └── vastra_mahal_db.sql    # Complete database export with seed data
├── images/
│   ├── banners/               # Hero & showroom interior banners
│   ├── categories/            # High-resolution category visuals
│   ├── products/              # High-resolution ethnic wear photos
│   └── vastra-mahal-location-qr.png # Branded Google Maps QR code
├── includes/
│   ├── header.php             # Global storefront navigation & header
│   └── footer.php             # Storefront footer with shop location & links
├── webfonts/                  # FontAwesome offline font files
├── index.php                  # Homepage
├── categories.php             # Collections directory
├── products.php               # Product catalog & filters
├── product-detail.php         # Individual product view
├── about.php                  # Brand story & boutique showroom
├── contact.php                # Contact form & live map embed
└── store-qr.php               # Mobile QR scan landing page
```

---

## 📍 Boutique Location & Contact
- **Address:** Gandhi Market, Sagar Pur, New Delhi
- **Phone / WhatsApp:** +91 96251 37860
- **Email:** thevastramahal60@gmail.com
- **Google Profile / Store Location:** [https://share.google/4X3xcWrgXxZ754XWa](https://share.google/4X3xcWrgXxZ754XWa)

---

Developed with ❤️ for **The Vastra Mahal**.
