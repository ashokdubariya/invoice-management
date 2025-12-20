# Invoice Management

A complete invoice management tool built with PHP, MySQL, TailwindCSS, and Alpine.js.

## Features

- **Authentication System** - Secure login/logout with session management
- **Client Management** - Add, edit, delete clients with search and pagination
- **Currency Management** - Multi-currency support with exchange rates
- **Invoice Management** - Create, edit, delete invoices with line items
- **Dynamic Line Items** - Add/remove items with live total calculations (Alpine.js)
- **PDF Export** - Generate professional PDF invoices with DOMPDF
- **Dashboard** - Statistics and recent invoices overview
- **Company Settings** - Manage company profile and logo
- **Dark Mode** - Toggle between light and dark themes
- **Responsive Design** - Works on desktop, tablet, and mobile
- **CSV Export** - Export client list to CSV

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Apache/Nginx with mod_rewrite enabled

## Installation

### 1. Clone or Download

The project is located at `/var/www/html/invoice-management/`

### 2. Install Dependencies

```bash
cd /var/www/html/invoice-management
composer install
```

### 3. Database Setup

Create a MySQL database and import the schema:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE invoice_management;
exit;
```

```bash
mysql -u root -p invoice_management < db/schema.sql
```

### 4. Configure Database

Edit `db/config.php` and update your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'invoice_management');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

### 5. Set Permissions

```bash
chmod -R 755 /var/www/html/invoice-management
chmod -R 777 /var/www/html/invoice-management/public/assets/uploads
```

### 6. Start Development Server

```bash
cd /var/www/html/invoice-management
php -S localhost:8000 -t public
```

Or configure Apache/Nginx to point to the `public` directory.

### 7. Access the Application

Open your browser and navigate to:
- **URL**: `http://localhost:8000`
- **Username**: `admin`
- **Password**: `admin123`

## Project Structure

```
invoice-management/
├── components/          # Reusable UI components
│   ├── header.php
│   ├── navbar.php
│   ├── sidebar.php
│   ├── notification.php
│   ├── modal.php
│   └── footer.php
├── controllers/         # Application controllers
│   ├── BaseController.php
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── ClientController.php
│   ├── CurrencyController.php
│   ├── InvoiceController.php
│   ├── SettingsController.php
│   └── PDFController.php
├── db/                  # Database files
│   ├── config.php
│   ├── Database.php
│   └── schema.sql
├── models/              # Data models
│   ├── BaseModel.php
│   ├── User.php
│   ├── Client.php
│   ├── Currency.php
│   ├── Invoice.php
│   ├── InvoiceItem.php
│   └── Setting.php
├── public/              # Public directory (document root)
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   └── uploads/
│   ├── .htaccess
│   ├── index.php
│   ├── Router.php
│   └── helpers.php
├── views/               # View templates
│   ├── auth/
│   ├── clients/
│   ├── currencies/
│   ├── dashboard/
│   ├── invoices/
│   ├── settings/
│   └── pdf/
├── vendor/              # Composer dependencies
└── composer.json
```

## Usage

### Creating an Invoice

1. Navigate to **Invoices** → **Create Invoice**
2. Select a client and currency
3. Add line items (click "Add Item" to add more rows)
4. Enter quantity, price, and tax percentage
5. Totals calculate automatically
6. Add notes if needed
7. Click "Create Invoice"

### Exporting to PDF

1. Go to **Invoices** list
2. Click **PDF** next to any invoice
3. The PDF will download automatically

### Managing Clients

1. Navigate to **Clients**
2. Use the search bar to find clients
3. Click **Add Client** to create new
4. Click **Edit** or **Delete** to modify
5. Click **Export CSV** to download all clients

### Managing Currencies

1. Navigate to **Currencies**
2. Click **Add Currency** to create new
3. Set exchange rates relative to base currency
4. Click **Set Default** to change default currency

### Company Settings

1. Navigate to **Settings**
2. Upload company logo
3. Update company information
4. Click **Save Settings**

## Features in Detail

### Alpine.js Dynamic Forms

The invoice form uses Alpine.js for:
- Adding/removing line items dynamically
- Live calculation of subtotals, taxes, and totals
- Reactive updates without page refresh

### Security Features

- CSRF protection on all forms
- Prepared statements (PDO) to prevent SQL injection
- Password hashing with `password_hash()`
- Input sanitization
- Session management

### Responsive Design

- Mobile-first approach with TailwindCSS
- Collapsible sidebar on mobile
- Touch-friendly buttons and forms
- Optimized tables for small screens

## Default Data

The system comes with:
- 1 admin user (username: `admin`, password: `admin123`)
- 4 default currencies (USD, EUR, GBP, INR)
- Default company settings

## Troubleshooting

### Database connection failed
- Check database credentials in `db/config.php`
- Ensure MySQL is running
- Verify database exists

### Please run composer install
- Run `composer install` in the project directory
- Ensure Composer is installed globally

### PDF generation not working
- Ensure DOMPDF is installed via Composer
- Check file permissions on uploads directory

### Dark mode not persisting
- Enable JavaScript in your browser
- Clear browser cache and localStorage

## Technologies Used

- **Backend**: PHP 7.4+ (No framework, custom MVC)
- **Database**: MySQL with PDO
- **Frontend**: HTML5, TailwindCSS (CDN)
- **JavaScript**: Alpine.js (CDN)
- **PDF Generation**: DOMPDF
- **Architecture**: MVC pattern with routing

## License

This project is open-source and available for personal and commercial use.