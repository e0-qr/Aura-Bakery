<div align="center">

# Aura Bakery

### Full-stack bakery e-commerce store

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![SQLite](https://img.shields.io/badge/SQLite-003B57?style=flat&logo=sqlite&logoColor=white)](https://sqlite.org)
[![PDO](https://img.shields.io/badge/PDO-Prepared_Statements-2E6A57?style=flat)]()

*Two-person university project — 2025*

</div>

---

## What it does

A working online bakery: browse products, add to a session-based cart, check out, and have the order saved to the database — plus an admin panel to manage the product catalogue.

## What was built vs. what was used

To be transparent about the codebase: the visual layout uses a free open-source Bootstrap-based template (original source no longer identifiable — see [Credits](#credits)). Everything that makes the site actually function was written by the project team.

| | |
|---|---|
| **Built by the team** | Database schema, `db_connect.php`, product/category logic, session cart (`add_to_cart.php`, `cart.php`), checkout flow (`checkout.php`), order handling (`order_success.php`), contact form, admin panel (`admin.php`) |
| **From the template** | Page layout, CSS/SCSS styling, carousel and animation libraries |

## Database

Five related tables with foreign keys: `users`, `products`, `orders`, `order_items`, `inquiries`.

## Security

- **Prepared statements (PDO)** on every query — protects against SQL injection
- **Server-side price recalculation at checkout** — totals are recomputed from the database, not trusted from the browser, so prices can't be tampered with client-side

## Tech Stack

`PHP` · `SQLite` · `PDO` · `JavaScript` · `HTML` · `CSS`

## Running Locally

PHP with SQLite needs a PHP-capable server — it won't run on static hosts like Vercel or Netlify.

```bash
git clone https://github.com/e0-qr/Aura-Bakery.git
cd Aura-Bakery

# Start PHP's built-in server
php -S localhost:8000

# Then open in your browser:
# http://localhost:8000/setup_db.php   (run once, creates the database)
# http://localhost:8000/index.php      (the store)
```

Requires PHP 8.x with the `pdo_sqlite` extension enabled (included by default in most PHP installs).

## Credits

The front-end layout is adapted from a free open-source Bootstrap template (original source no longer identifiable). All back-end functionality — database schema, session cart, checkout flow, order handling, and the product admin panel — was written by the project team.

## Author

**Zainah Al-Amri** — [Portfolio](https://github.com/e0-qr) · [LinkedIn](https://www.linkedin.com/in/zainh1)
