# PurePHPWithAuthentication

A simple, modern blog platform built with pure PHP and MySQL, featuring user authentication, post management, and a responsive design. This project is ideal for learning PHP web development, authentication, and CRUD operations without any frameworks.

## Features

- User registration and login (with password hashing)
- User dashboard with post management (create, edit, delete)
- Public post listing and single post view
- Commenting system (authenticated users)
- **Products section**: Browse products, add to cart, and place orders (basic e-commerce)
- Shopping cart and order management for authenticated users
- Responsive, modern UI (custom CSS)
- MySQL database schema included
- Session-based authentication

## Project Structure

```
Configuration/         # PHP config, database, and session files
css/                   # Custom CSS styles
index.php              # Home page (latest posts)
viewposts.php          # All posts listing
view-post.php          # Single post view (with comments)
dashboard.php          # User dashboard (manage posts, account info, products, cart)
create-post.php        # Create new post (authenticated)
edit-post.php          # Edit post (authenticated, owner only)
delete-post.php        # Delete post (authenticated, owner only)
login.php              # User login
register.php           # User registration
logout.php             # User logout
mysql.sql              # Database schema (includes products, cart, orders)
```

## Setup Instructions

### 1. Requirements
- PHP 7.4+
- MySQL/MariaDB
- Web server (Apache, Nginx, or PHP built-in server)

### 2. Database Setup
1. Create a new MySQL database (default: `blogspace`).
2. Import the provided `mysql.sql` file to create the required tables:
   ```
   mysql -u root -p blogspace < mysql.sql
   ```
3. Update `Configuration/config.php` if your DB credentials differ from the defaults:
   ```php
   const DB_HOST = 'localhost';
   const DB_NAME = 'blogspace';
   const DB_USER = 'root';
   const DB_PASS = '';
   ```

### 3. Running the App
- Place the project in your web server's root directory (e.g., `htdocs` or `www`).
- Access `index.php` via your browser (e.g., `http://localhost/PurePHPWithAuthentication/`).

## Usage

- **Register** a new account via `register.php`.
- **Login** via `login.php`.
- **Dashboard**: Manage your posts and account info.
- **Create/Edit/Delete Posts**: Only authenticated users can manage their own posts.
- **View Posts**: All users (including guests) can view posts and comments.
- **Comment**: Only authenticated users can comment on posts.
- **Logout**: End your session securely.

## Security Notes
- Passwords are securely hashed using PHP's `password_hash`.
- Session cookies expire when the browser is closed.
- Only post owners can edit or delete their posts.

## Customization
- **Styling**: Modify `css/style.css` for custom themes or layouts.
- **Config**: Change app name, debug mode, or DB settings in `Configuration/config.php`.

## License
MIT or specify your own license.

## Products & E-commerce Section

The application now includes a basic e-commerce section:

- **Product Listing:** View available products from the dashboard.
- **Add to Cart:** Authenticated users can add products to their cart.
- **Cart Management:** View cart item count, and order all items in the cart.
- **Order Products:** Place an order for all items in your cart (stock is checked and updated).

**Database:**
- The schema now includes `products`, `carts`, `cart_items`, `orders`, and `order_items` tables in addition to blog tables.

**UI:**
- The dashboard displays a products section with add-to-cart and order buttons, and a cart icon with item count.

**Note:** This is a simple e-commerce implementation for learning purposes and does not include payment processing.

---

*Built for learning and rapid prototyping. Contributions welcome!* 