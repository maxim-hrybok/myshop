# Steam Card Shop - Full-Stack PHP E-Commerce Platform

A full-stack e-commerce web application built from the ground up using modern PHP, a custom MVC architecture, and industry-standard tools. This project is a practical demonstration of core web development principles, database design, and application architecture.

**Live Demo:** [**https://somesite.ct.ws/**](https://somesite.ct.ws/)

---

## Key Features

This project is more than a simple website; it's a complete e-commerce solution with distinct user and admin functionalities.

#### Customer-Facing Features:
*   **Product Catalog:** Browse a grid of all available products.
*   **Advanced Filtering & Search:** Filter products by category and search by name.
*   **Pagination:** Efficiently navigate through large product lists.
*   **Shopping Cart:** A fully persistent session-based shopping cart.
*   **User Authentication:** Secure user registration and login system with brute-force protection.
*   **Order History:** Registered users can view a complete history of their past orders and their status.
*   **Stock Management:** Products that are out of stock cannot be added to the cart.

#### Administrative Panel Features:
*   **Secure Admin Area:** The admin dashboard is accessible only to authenticated admin users.
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
*   **Frontend:** HTML5, CSS3, Smarty Templating Engine
*   **Key Libraries:**
    *   `nikic/fast-route`: A high-performance request router.
    *   `vlucas/phpdotenv`: For secure management of environment variables.
    *   `smarty/smarty`: A reliable and popular template engine that allows you to separate logic from presentation.
*   **Architectural Patterns & Concepts:**
    *   **MVC-S (Model-View-Controller-Services):** The code is structured to ensure a separation of concerns: separate models for data processing, views for visualization, controllers for handling user requests, and services for processing the received data.
    *   **Front Controller Pattern:** All web requests are routed through a single `index.php` entry point, which handles bootstrapping and request dispatching.
    *   **Clean URLs:** Implemented using `.htaccess` to create user-friendly URLs.
    *   **Security:**
        *   **SQL Injection Prevention:** Uses prepared statements (via PDO) for all database queries.
        *   **Password Security:** Employs the modern `Argon2id` hashing algorithm for all user passwords.
        *   **XSS Prevention:** All dynamic output is escaped within the Smarty templates.
        *   **Brute-Force Protection:** The login system tracks failed attempts by IP address and implements a temporary lockout.

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
    *   Rename the `.env.example` file to `.env` (if you provide one, otherwise create it).
    *   Update the `.env` file with your database credentials:
    ```ini
    DB_HOST=localhost:port(default 3306)
    DB_NAME=your_database_name
    DB_USER=your_database_user
    DB_PASS=your_database_pass
    DB_CHARSET=utf8mb4
    ```

6.  **Web Server Configuration:**
    *   Ensure that Apache's `mod_rewrite` module is enabled.

7.  **You're Ready!**
    *   Navigate to your local development URL (e.g., `http://localhost/`).
    *   **Admin Login:** You can log in with the admin credentials (`admin@gmail.com`, 'admin').

    
