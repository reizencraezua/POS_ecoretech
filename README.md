# POS Ecoretech - Point of Sale System

A comprehensive Point of Sale (POS) system built with Laravel 12 for printing and design services business management.

## Features

- **Order Management**: Create, edit, and track orders with multiple items
- **Product & Service Catalog**: Manage products and services with categories, sizes, and pricing
- **Customer Management**: Track customer information and order history
- **Employee Management**: Assign employees to orders and track their roles
- **Layout Design**: Graphics designer assignment for layout services
- **Payment Tracking**: Multiple payment methods and payment terms
- **Inventory Management**: Track stock levels and usage
- **Quotation System**: Generate and manage customer quotations
- **Reporting**: Financial summaries and performance tracking

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 16+ and NPM
- MySQL 5.7+ or MariaDB 10.3+

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd POS_Ecoretech
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
   # Create MySQL database
   mysql -u root -p -e "CREATE DATABASE pos_ecoretech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   
   # Update .env file with your database credentials
   # DB_DATABASE=pos_ecoretech
   # DB_USERNAME=your_username
   # DB_PASSWORD=your_password
   
   # Run migrations and seeders
   php artisan migrate --seed
   ```

   **Note**: If you encounter foreign key constraint errors, try running:
   ```bash
   # Reset database and reseed
   php artisan migrate:fresh --seed
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

7. **Start the application**
   ```bash
   # Terminal 1: Start Laravel server
   php artisan serve
   
   # Terminal 2: Start Vite dev server (for development)
   npm run dev
   ```

8. **Access the application**
   - Open your browser and go to `http://localhost:8000`
   - Default admin credentials:
     - **Super Admin**: admin@ecoretech.com / admin123
     - **Admin**: admin@pos.com / password123

## Database

The application uses MySQL for robust data management and better performance. Make sure to create the database before running migrations.

## Key Features

### Order Management
- Create orders with multiple products/services
- Assign production and graphics design employees
- Track order status and deadlines
- Calculate totals with layout fees and discounts

### Graphics Design Integration
- Automatic graphics designer selection based on layout requirements
- Layout fee calculation and tracking
- Designer assignment validation

### Payment System
- Multiple payment methods (Cash, GCash, Bank Transfer)
- Payment terms (Full Payment, Downpayment)
- Payment tracking and remaining balance calculation

### Inventory Management
- Stock level tracking
- Usage monitoring
- Supplier management

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
php artisan pint
```

### Database Reset
```bash
php artisan migrate:fresh --seed
```

## Project Structure

```
app/
├── Http/Controllers/Admin/    # Admin panel controllers
├── Models/                    # Eloquent models
└── Providers/                 # Service providers

resources/
├── views/admin/              # Admin panel views
├── css/                      # Stylesheets
└── js/                       # JavaScript files

database/
├── migrations/               # Database migrations
└── seeders/                  # Database seeders
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests and ensure code quality
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).