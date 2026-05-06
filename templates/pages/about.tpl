{include file='partials/header.tpl'}

<main class="main-container-sm">
    <div class="panel">
        <h1 class="text-primary mb-2">About Steam Card Shop</h1>
        <p class="font-large mb-2">
            Welcome to my portfolio project! This application is a fully custom-built, full-stack e-commerce platform designed to demonstrate production-ready software engineering principles.
        </p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <h2 class="mb-2">Engineering Highlights</h2>
        <ul style="line-height: 1.8; margin-bottom: 20px; color: #333; background-color: #f4f4f4;">
            <li><strong>Custom MVC Architecture:</strong> Built entirely from scratch using strictly typed, object-oriented controllers, models, and services enforcing the Separation of Concerns (SoC).</li>
            <li><strong>Inversion of Control (IoC):</strong> Powered by PHP-DI for PSR-11 compliant dependency injection and automatic autowiring.</li>
            <li><strong>Atomic Database Operations:</strong> Resolves critical concurrency and race conditions during checkout using atomic SQL updates and PDO Transactions.</li>
            <li><strong>Optimized Querying:</strong> Eliminates the N+1 database query problem via Eager Loading for relational categories.</li>
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