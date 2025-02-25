# Sidestacker System Summary

## System Overview
Sidestacker is a comprehensive e-commerce and content management system with integrated user management, product catalogue, and administrative features.

## Core Components

### 1. Authentication System
- **Location**: `/includes/auth_middleware.php`, `login.php`, `register.php`
- **Functionality**:
  - User registration with email verification
  - Secure login with password hashing
  - Session management and security
  - Password reset functionality
  - Role-based access control (Admin/User)

### 2. Database Structure
- **Location**: `/database/`
- **Key Tables**:
  - `users`: Stores user accounts and credentials
  - `products`: Product catalogue information
  - `tools`: Equipment and tool listings
  - `content`: CMS content storage
  - `basket`: Shopping basket items
  - `saved_items`: User's saved products/tools

### 3. Admin Dashboard
- **Location**: `/admin/`
- **Features**:
  - User Management
    - View/Edit user accounts
    - Toggle user status
    - Assign roles
  - Content Management
    - Create/Edit/Delete content
    - Content status management
  - Product Management
    - Inventory control
    - Price management
    - Stock tracking
  - Tools Management
    - Categorisation
    - Status tracking
  - Analytics Dashboard
    - User statistics
    - Sales tracking
    - System status

### 4. E-commerce Features
- **Location**: `shop.php`, `product.php`, `tools.php`
- **Functionality**:
  - Product catalogue browsing
  - Shopping basket system
  - Save items for later
  - Product filtering and search
  - Tool rental/purchase system

### 5. Content Management
- **Location**: `content.php`, `view_content.php`
- **Features**:
  - Dynamic content rendering
  - Content categorisation
  - Draft/Published status
  - Author attribution

### 6. User Features
- **Location**: `profile.php`, `dashboard.php`
- **Functionality**:
  - Profile management
  - Order history
  - Saved items
  - Account settings

## Technical Infrastructure

### Database Connection
- **Location**: `/includes/db_connect.php`
- Uses PDO for secure database operations
- Connection pooling and error handling
- Prepared statements for query security

### Session Management
- **Location**: `/includes/session_manager.php`
- Secure session handling
- Session timeout management
- Cross-site request forgery protection

### Helper Functions
- **Location**: `/includes/helpers.php`
- Common utility functions
- Data validation
- Security functions

### Asset Management
- **Location**: `/assets/`, `/css/`, `/js/`
- Structured asset organisation
- CSS styling with Tailwind
- JavaScript functionality

## Security Features
1. Password Hashing (using PHP's password_hash)
2. SQL Injection Prevention (PDO prepared statements)
3. Session Security
   - Secure cookie settings
   - Session timeout
   - Session ID regeneration
4. CSRF Protection
5. Input Validation
6. Role-based Access Control

## File Structure Overview

### Root Directory
- `index.php`: Main entry point
- `login.php`, `register.php`: Authentication pages
- `profile.php`: User profile management
- `shop.php`, `product.php`: E-commerce pages

### Admin Directory
- `dashboard.php`: Admin control panel
- `manage_content.php`: Content management
- `manage_products.php`: Product management
- `manage_tools.php`: Tools management
- `analytics.php`: Statistics and reporting

### Includes Directory
- Configuration files
- Database connections
- Helper functions
- Common components (header, footer)

### Database Directory
- SQL schema files
- Database initialization
- Test data population
- Database maintenance scripts

## API Endpoints
- **Location**: `/api/`
- RESTful API endpoints for:
  - User management
  - Product operations
  - Content management
  - Shopping basket operations

## Development Tools
- PHP 7.4+
- MariaDB/MySQL
- Tailwind CSS
- JavaScript/jQuery
- Bootstrap 5.1.3

## Maintenance and Updates
- Regular database backups
- Session cleanup
- Log rotation
- Security updates
- Content moderation

This summary provides an overview of the current state of the Sidestacker system. The system continues to be developed with new features and improvements being added regularly.
