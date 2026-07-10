{include file='partials/header.tpl'}

<main class="main-container-sm">
    <div class="panel">
        <h1 class="text-primary mb-2">About Steam Card Shop</h1>
        <p class="font-large mb-2">
            Welcome to my portfolio project! This application is a fully custom-built, full-stack e-commerce platform designed to demonstrate production-ready software engineering principles.
        </p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <h2 class="mb-2">Engineering Highlights</h2>
        <ul style="line-height: 1.8; margin-bottom: 20px; color: #333; background-color: #f4f4f4; padding: 15px 20px 15px 40px; border-radius: 5px;">
            <li><strong>Service-Oriented Architecture:</strong> Uses "Thin Controllers" that delegate complex business logic to dedicated Services (e.g., <code>AuthService</code>, <code>ProductService</code>), strictly separating HTTP transport from business rules.</li>
            <li><strong>Repository Pattern:</strong> Database interactions are abstracted into Repository classes, ensuring the codebase is decoupled, scalable, and aligns with enterprise terminology.</li>
            <li><strong>Middleware Pipeline:</strong> Security checks (Auth, Admin, CSRF) are handled gracefully via an intercepting middleware pipeline, keeping controllers perfectly clean and allowing third-party API webhook integrations.</li>
            <li><strong>Inversion of Control (IoC) & DI:</strong> Powered by PHP-DI. Zero global variables. Everything from PDO connections to Configuration values (<code>ConfigService</code>) is lazily instantiated and injected via autowiring.</li>
            <li><strong>Decoupled Routing:</strong> HTTP routing is extracted to a dedicated configuration file, leaving the Front Controller (<code>index.php</code>) focused strictly on bootstrapping.</li>
            <li><strong>Atomic Database Operations:</strong> Resolves critical concurrency and race conditions during checkout using atomic SQL updates and PDO Transactions.</li>
            <li><strong>Security First:</strong> Hardened with PDO Prepared Statements against SQLi, Argon2id for password hashing, strict MIME-type validation for image uploads, and an IP-based brute-force lockout system.</li>
        </ul>

        <h2 class="mb-2">The Tech Stack</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
            <span style="background: #e2e3e5; padding: 5px 12px; border-radius: 4px; font-weight: bold; color: #444;">PHP 8.x</span>
            <span style="background: #e2e3e5; padding: 5px 12px; border-radius: 4px; font-weight: bold; color: #444;">MariaDB</span>
            <span style="background: #e2e3e5; padding: 5px 12px; border-radius: 4px; font-weight: bold; color: #444;">Smarty Templating</span>
            <span style="background: #e2e3e5; padding: 5px 12px; border-radius: 4px; font-weight: bold; color: #444;">FastRoute</span>
            <span style="background: #e2e3e5; padding: 5px 12px; border-radius: 4px; font-weight: bold; color: #444;">PHP-DI</span>
            <span style="background: #e2e3e5; padding: 5px 12px; border-radius: 4px; font-weight: bold; color: #444;">GD Library</span>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <div class="text-center mt-2">
            <h3 class="mb-2">Developed by Maxim Hrybok</h3>
            <a href="https://github.com/maxim-hrybok" target="_blank" class="btn-primary" style="margin-top: 10px;">Visit My GitHub</a>
        </div>
    </div>
</main>

{* include file='partials/footer.tpl' *}
</body>
</html>