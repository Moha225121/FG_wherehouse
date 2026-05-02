# FG_wherehouse
Alfarsi group warehouse system

## About the Project
This is a Laravel-based system for managing car glass warehouses across multiple branches. It includes:
- **Admin Dashboard**: Centralized control of stock, employees, and sales.
- **Employee Portal**: Branch-specific inventory management and POS.
- **Searchable Dropdowns**: Optimized selection for large datasets using Tom Select.
- **Mobile Optimized**: Responsive layout with a mobile bottom navigation bar.

## Getting Started

### Prerequisites
- PHP 8.3+
- Composer
- Node.js & NPM
- SQLite (or your preferred database)

### Installation
1. Clone the repository.
2. Run `composer install`.
3. Copy `.env.example` to `.env` and generate a key: `php artisan key:generate`.
4. Run migrations and seeders: `php artisan migrate --seed`.
5. Install and build frontend assets: `npm install && npm run build`.
6. Start the server: `php artisan serve`.

## Features
- Real-time stock tracking.
- Sale undo functionality for admins.
- PDF report generation.
- Responsive design for mobile and tablet.

## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
