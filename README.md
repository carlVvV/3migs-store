# Three Migs - E-commerce Platform

A full-stack e-commerce platform built with Laravel 12 and modern frontend technologies, implementing the structure from [carlVvV/3migsProject](https://github.com/carlVvV/3migsProject.git).

## 🚀 Features

### Core E-commerce Features
- **Product Management**: Complete product catalog with variants and inventory tracking
- **Shopping Cart**: Persistent cart functionality with session-based storage
- **Order Management**: Order processing, tracking, and status management
- **User Authentication**: Secure login/registration with Laravel Breeze
- **Role-based Access Control**: Admin and customer roles with Spatie Laravel Permission
- **Product Reviews & Ratings**: Customer review system with moderation
- **Wishlist**: Save products for later purchase
- **Search & Filtering**: Advanced product search and filtering capabilities

### Admin Features
- **Admin Dashboard**: Comprehensive admin panel with analytics
- **Inventory Management**: Product CRUD operations with stock tracking
- **Order Management**: View and manage customer orders
- **User Management**: Manage customer accounts and roles
- **Review Moderation**: Approve and manage product reviews

### API Features
- **RESTful API**: Complete API v1 with Laravel Sanctum authentication
- **API Documentation**: Well-documented endpoints for mobile/frontend integration
- **Authentication**: Token-based authentication for API access
- **Rate Limiting**: Built-in API rate limiting and security

## 🛠 Tech Stack

### Backend
- **Laravel 12.0** - PHP Framework
- **PHP 8.2+** - Server-side language
- **SQLite** - Database (configurable to MySQL/PostgreSQL)
- **Laravel Sanctum** - API Authentication
- **Spatie Laravel Permission** - Role & Permission Management
- **Laravel Breeze** - Authentication scaffolding

### Frontend
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Vite** - Build tool and development server
- **Blade Templates** - Server-side templating engine

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite (or MySQL/PostgreSQL)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd three-migs-clean
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed --class=RolesAndAdminSeeder
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

## 🚀 Running the Application

### Development Mode

```bash
# Terminal 1 - Laravel server
php artisan serve

# Terminal 2 - Vite dev server (for development)
npm run dev
```

### Production Mode

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## 🌐 Access Points

### Frontend Pages
- **Homepage**: http://127.0.0.1:8000/
- **Login**: http://127.0.0.1:8000/login
- **Signup**: http://127.0.0.1:8000/register
- **Cart**: http://127.0.0.1:8000/cart
- **Checkout**: http://127.0.0.1:8000/checkout
- **Product Details**: http://127.0.0.1:8000/product/{slug}
- **Wishlist**: http://127.0.0.1:8000/wishlist
- **Profile**: http://127.0.0.1:8000/profile
- **Orders**: http://127.0.0.1:8000/orders

### Admin Panel
- **Admin Login**: http://127.0.0.1:8000/login
- **Admin Dashboard**: http://127.0.0.1:8000/admin/dashboard
- **Product Management**: http://127.0.0.1:8000/admin/products
- **Order Management**: http://127.0.0.1:8000/admin/orders
- **User Management**: http://127.0.0.1:8000/admin/users
- **Review Management**: http://127.0.0.1:8000/admin/reviews

### API Endpoints
- **Health Check**: http://127.0.0.1:8000/api/health
- **API v1 Products**: http://127.0.0.1:8000/api/v1/products
- **API v1 Authentication**: http://127.0.0.1:8000/api/v1/login

## 👤 Default Credentials

### Admin Account
- **Email**: `admin@example.com`
- **Password**: `password`
- **Role**: Admin (full access)

### Test Customer Account
- **Email**: `customer@example.com`
- **Password**: `password`
- **Role**: Customer

## 📚 API Documentation

### Authentication Endpoints

#### Register User
```http
POST /api/v1/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password",
    "phone": "+1234567890",
    "address": "123 Main St"
}
```

#### Login
```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password"
}
```

#### Get User Profile
```http
GET /api/v1/me
Authorization: Bearer {token}
```

#### Logout
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

### Product Endpoints

#### Get All Products
```http
GET /api/v1/products?page=1&per_page=12&category=barong&min_price=100&max_price=500&search=barong&sort=price&order=asc
```

#### Get Featured Products
```http
GET /api/v1/products/featured?limit=8
```

#### Get New Arrivals
```http
GET /api/v1/products/new-arrivals?limit=8
```

#### Get Product by Slug
```http
GET /api/v1/products/premium-barong-embroidered
```

#### Search Products
```http
GET /api/v1/products/search?q=barong&per_page=12
```

#### Get Products by Category
```http
GET /api/v1/products/category/formal-barong?per_page=12
```

### Cart Endpoints

#### Get Cart
```http
GET /api/v1/cart
```

#### Add to Cart
```http
POST /api/v1/cart/add
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 2
}
```

#### Update Cart Item
```http
PUT /api/v1/cart/update
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 3
}
```

#### Remove from Cart
```http
DELETE /api/v1/cart/remove
Content-Type: application/json

{
    "product_id": 1
}
```

#### Clear Cart
```http
DELETE /api/v1/cart/clear
```

#### Get Cart Summary
```http
GET /api/v1/cart/summary
```

## 🏗 Project Structure

```
three-migs-clean/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── V1/          # API v1 controllers
│   │   │   │       ├── AuthController.php
│   │   │   │       ├── ProductController.php
│   │   │   │       └── CartController.php
│   │   │   ├── AdminController.php
│   │   │   ├── AuthController.php
│   │   │   ├── HomeController.php
│   │   │   └── ...
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   ├── Models/                 # Eloquent models
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Review.php
│   │   └── Wishlist.php
│   └── ...
├── database/
│   ├── migrations/             # Database migrations
│   │   ├── create_permission_tables.php
│   │   ├── create_products_table.php
│   │   ├── create_categories_table.php
│   │   ├── create_orders_table.php
│   │   ├── create_order_items_table.php
│   │   ├── create_reviews_table.php
│   │   └── create_wishlists_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── CategorySeeder.php
│       ├── ProductSeeder.php
│       └── RolesAndAdminSeeder.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── partials/
│   │   │   ├── header.blade.php
│   │   │   └── footer.blade.php
│   │   ├── admin/
│   │   │   ├── dashboard.blade.php
│   │   │   └── products.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   └── register.blade.php
│   │   ├── home.blade.php
│   │   ├── cart.blade.php
│   │   ├── checkout.blade.php
│   │   └── product-details.blade.php
│   └── ...
├── routes/
│   ├── web.php                 # Web routes
│   ├── api.php                 # API routes
│   └── auth.php                # Laravel Breeze auth routes
├── public/                     # Public assets
│   ├── Homepage/              # Static homepage files
│   ├── Cart/                  # Static cart files
│   ├── CheckOut/              # Static checkout files
│   ├── ProductDetails/        # Static product details files
│   └── ...
└── ...
```

## 🔐 Security Features

- **CSRF Protection**: All forms protected with CSRF tokens
- **SQL Injection Prevention**: Eloquent ORM with parameter binding
- **XSS Protection**: Blade templating with automatic escaping
- **Authentication**: Laravel Breeze with secure password hashing
- **Authorization**: Role-based permissions with Spatie
- **API Security**: Sanctum token authentication
- **Input Validation**: Comprehensive request validation
- **Rate Limiting**: API rate limiting for abuse prevention

## 🎨 Frontend Features

- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Modern UI**: Clean, professional e-commerce interface
- **Interactive Elements**: Alpine.js for dynamic interactions
- **Shopping Cart**: Real-time cart updates
- **Product Search**: Instant search with filtering
- **Wishlist**: Save products for later
- **User Dashboard**: Profile and order management
- **Admin Panel**: Comprehensive admin interface

## 🚀 Deployment

### Production Checklist

1. **Environment Configuration**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   ```

2. **Database Configuration**
   ```bash
   DB_CONNECTION=mysql
   DB_HOST=your-db-host
   DB_DATABASE=your-database
   DB_USERNAME=your-username
   DB_PASSWORD=your-password
   ```

3. **Cache Configuration**
   ```bash
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   ```

4. **Build Assets**
   ```bash
   npm run build
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the MIT license.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation

## 🎯 Roadmap

### Phase 1 (Completed)
- ✅ Basic e-commerce functionality
- ✅ User authentication and roles
- ✅ Product management
- ✅ Shopping cart
- ✅ Order management
- ✅ Admin dashboard

### Phase 2 (In Progress)
- 🔄 Payment gateway integration
- 🔄 Email notifications
- 🔄 Advanced analytics
- 🔄 Mobile app API

### Phase 3 (Planned)
- 📋 Multi-vendor support
- 📋 Advanced inventory management
- 📋 Marketing tools
- 📋 Advanced reporting

---

**Built with ❤️ using Laravel 12 and modern web technologies**