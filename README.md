# Steam Card Shop - Full-Stack PHP E-Commerce Platform

A full-stack e-commerce web application built from the ground up using modern PHP, a custom MVC architecture, and industry-standard tools. This project is a practical demonstration of core web development principles, database design, security, and application architecture.

**Live Demo:** [**https://somesite.ct.ws/**](https://somesite.ct.ws/)

---

## Key Features

This project is more than a simple website; it's a complete e-commerce solution with distinct user and admin functionalities, engineered for performance and scalability.

#### Customer-Facing Features:
*   **Product Catalog & Optimization:** Browse a grid of all available products with automatically generated, lightweight thumbnails for fast page loading.
*   **Interactive Comment System:** Users can leave reviews and comments with full native Emoji support (`utf8mb4`). Comments are protected against XSS and routed through an admin moderation queue.
*   **Advanced Filtering & Search:** Filter products by category and search by name.
*   **Pagination:** Efficiently navigate through large product lists.
*   **Shopping Cart:** A fully persistent session-based shopping cart.
*   **User Authentication:** Secure user registration and login system protected by **Google reCAPTCHA v2** and an IP-based brute-force lockout system.
*   **Order History:** Registered users can view a complete history of their past orders and their status.
*   **Concurrency-Safe Checkout:** Products cannot be oversold. The checkout process uses atomic database operations to guarantee accurate stock reduction even under high traffic.

#### Administrative Panel Features:
*   **Secure Admin Area:** The admin dashboard is accessible only to authenticated admin users.
*   **Comment Moderation:** Dedicated dashboard to review, approve, or reject user comments before they appear publicly.
*   **Full Product Management (CRUD):** Admins can Create, Read, Update, and Delete any product.
*   **Category Management (CRUD):** Easily add, edit, and remove product categories.
*   **Advanced Order Management:** View all customer orders, filter them by status (Pending, Completed, Cancelled), and search by Order ID or User ID.
*   **Order Status Updates:** Admins can update the status of any order (e.g., from "Pending" to "Completed").
*   **Inventory Tracking:** The admin dashboard clearly shows current stock levels, highlighting low-stock items.

---

## Technology Stack & Architectural Concepts

This project was developed with a focus on “clean architecture” and maintainability, using a modern PHP stack without relying on full-featured frameworks such as Laravel or Symfony, with the aim of gaining a deep understanding of the core components and gaining practical experience.

*   **Backend:** PHP 8+
*   **Database:** MariaDB / MySQL
*   **Web Server:** Apache (with `mod_rewrite` for clean URLs)
*   **Frontend:** HTML5, CSS3, Smarty Templating Engine (Strictly decoupled, near zero inline CSS)
*   **Key Libraries:**
    *   `php-di/php-di`: PSR-11 compliant Dependency Injection Container for Autowiring.
    *   `nikic/fast-route`: A high-performance request router.
    *   `vlucas/phpdotenv`: For secure management of environment variables.
    *   `smarty/smarty`: A reliable template engine separating logic from presentation (extended with custom security plugins).
*   **Architectural Patterns & Engineering Highlights:**
    *   **Inversion of Control (IoC):** Implementation of a PSR-11 Dependency Injection Container (PHP-DI) to completely automate object instantiation and manage the dependency tree without manual wiring.
    *   **MVC-S Architecture:** Strict Separation of Concerns (SoC) using Models, Views, Controllers, and Services.
    *   **Eager Loading:** Eliminates the classic N+1 database query problem by efficiently mapping relational categories to products in single, batched queries.
    *   **Atomic Database Operations:** Resolves critical e-commerce race conditions during checkout using atomic SQL updates (`rowCount()` validation) and PDO Transactions.
    *   **Dynamic Image Processing:** Secure, server-side image upload validation and on-the-fly resizing (Thumbnails & Full-Res) using the native GD library to drastically reduce bandwidth.
    *   **Graceful Degradation:** Features adaptive architecture (e.g., falling back from `cURL` to secure native streams for Google APIs if extensions are missing on the host server).
    *   **Security First:** 
        *   **SQL Injection Prevention:** 100% coverage using PDO Prepared Statements.
        *   **Password Security:** State-of-the-art `Argon2id` hashing algorithm.
        *   **XSS Prevention:** All dynamic output is escaped within Smarty templates.
        *   **File Upload Security:** Strict MIME-type validation via `getimagesize()` to prevent malicious payload uploads.

---

## Local Setup & Installation

To run this project locally, follow these steps:

1.  **Prerequisites:**
    *   A local server environment (XAMPP, WAMP, MAMP, or Docker).
    *   PHP 8.0 or higher.
    *   MariaDB or MySQL.
    *   Composer installed globally.

2.  **Clone the Repository:**
    ```bash
    git clone https://github.com/maxim-hrybok/myshop.git
    cd myshop
    ```

3.  **Install Dependencies:**
    ```bash
    composer install
    ```

4.  **Database Setup:**
    *   Create a new database in your database manager (e.g., `phpMyAdmin`).
    *   Import the `database/backup.sql` file into your newly created database.

5.  **Environment Configuration:**
    *   Copy the `.env.example` file to `.env` (this file includes Google's official Test Keys for easy local setup).
    *   Update the `.env` file with your database credentials:
    ```ini
    APP_ENV=development
    
    DB_HOST=localhost:3306
    DB_NAME=your_database_name
    DB_USER=your_database_user
    DB_PASS=your_database_pass
    DB_CHARSET=utf8mb4
    
    # Official Google Test Keys (Automatically pass locally without setup)
    RECAPTCHA_SITE_KEY=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI
    RECAPTCHA_SECRET_KEY=6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe
    ```
6.  **Directory Permissions:**
    *   Ensure the following directories are writable by your web server for template caching and image uploads:
        *   `/cache/`
        *   `/templates_c/`
        *   `/public/uploads/products/`


7.  **Web Server Configuration:**
    *   Ensure that Apache's `mod_rewrite` module is enabled.

8.  **You're Ready!**
    *   Navigate to your local development URL (e.g., `http://localhost/`).
    *   **Admin Login:** You can log in with the admin credentials (`admin@gmail.com`, `admin`).

---
*Developed by Maxim Hrybok.*
    
