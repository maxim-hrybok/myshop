# Steam Card Shop - Full-Stack PHP E-Commerce Platform


A full-stack e-commerce web application built from the ground up using modern PHP, a custom MVC architecture, and industry-standard tools. This project demonstrates advanced software engineering principles, clean architecture, and modern DevOps practices without relying on heavy frameworks like Laravel or Symfony.
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

*   **Infrastructure:** Docker & Docker Compose
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

You can run this entire platform locally in minutes using Docker. You do not need PHP, Apache, or MariaDB installed on your host machine.

### 1. Create a `docker-compose.yml` file
Create a folder for the project, and create a `docker-compose.yml` file inside it with the following content:

```yaml
version: '3.8'

services:
  web:
    image: 6272715work/steamshop:latest
    container_name: steamshop_web
    ports:
      - "8080:80"
    environment:
      - APP_ENV=development
      - DB_HOST=db
      - DB_NAME=project
      - DB_USER=root
      - DB_PASS=secret
      - DB_CHARSET=utf8mb4
      - RECAPTCHA_SITE_KEY= ###https://www.google.com/recaptcha/admin
      - RECAPTCHA_SECRET_KEY= ###https://www.google.com/recaptcha/admin
      - db
    networks:
      - steam-net

  db:
    # pre-seeded database image
    image: 6272715work/steamshop-db:latest
    container_name: steamshop_db
    ports:
      - "3307:3306"
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: project
    volumes:
      - db_data:/var/lib/mysql
    networks:
      - steam-net

volumes:
  db_data:

networks:
  steam-net:
    driver: bridge
```

### 2. Start the Application
Open your terminal in the folder containing your docker-compose.yml and run:

docker-compose up -d

### 3. Access the Platform
Frontend: Navigate to http://localhost:8080
Admin Access: Log in using admin@gmail.com / admin

---
*Developed by Maxim Hrybok.*
    
