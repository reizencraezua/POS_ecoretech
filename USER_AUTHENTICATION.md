# User Authentication System

## Overview
The application now uses a unified `users` table for all authentication (admin and cashier roles).

## User Roles
- **super_admin**: Full system access
- **admin**: Administrative access
- **cashier**: Cashier/employee access

## Default Accounts

### Super Admin
- **Email**: admin@ecoretech.com
- **Password**: admin123
- **Role**: super_admin

### Admin
- **Email**: admin@pos.com
- **Password**: password123
- **Role**: admin

### Cashier
- **Email**: cashier@ecoretech.com
- **Password**: cashier123
- **Role**: cashier

## Database Structure

### Users Table
```sql
- id (primary key)
- name (string)
- email (string, unique)
- email_verified_at (timestamp, nullable)
- password (string, hashed)
- role (enum: admin, cashier, super_admin)
- is_active (boolean, default: true)
- employee_id (string, nullable) - Links to employees table if needed
- remember_token (string, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

## Authentication Guards
- **web**: Default guard for general users
- **admin**: Admin panel authentication
- **cashier**: Cashier panel authentication

All guards use the same `users` table but can be differentiated by role.

## Migration Notes
- The old `admins` table has been migrated to the new `users` table
- All existing admin accounts have been preserved
- The `AdminSeeder` has been removed and replaced with `UserSeeder`
- Authentication configuration has been updated to use the unified system

## Usage in Controllers
```php
// Check if user is admin
if (auth()->user()->isAdmin()) {
    // Admin logic
}

// Check if user is cashier
if (auth()->user()->isCashier()) {
    // Cashier logic
}

// Check if user is super admin
if (auth()->user()->isSuperAdmin()) {
    // Super admin logic
}
```
