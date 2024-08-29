# Reimbursement Management System

This project is a reimbursement management system designed to help employees manage and request reimbursements for their insurance benefits.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Installation](#installation)
    - [Prerequisites](#prerequisites)
    - [Backend Installation](#backend-installation)
    - [Backend Configuration](#backend-configuration)
- [API Endpoints](#api-endpoints)
- [Contributing](#contributing)
- [License](#license)
- [Author](#author)
## Overview

The Reimbursement Management System allows employees to submit reimbursement requests for their insurance benefits. The system calculates the reimbursement amount based on predefined rules and tracks the status of each request.

## Features

- User authentication JWT TOKEN and role-based access control (Admin, Employee, Guest)
- Calculation of reimbursement amounts based on predefined rules
- Tracking of request status
- Admin dashboard for managing profile, categories, fee types, policies, employees, refund requests
- Employee dashboard for managing profile, sending requests, tracking requests status
- Secure document upload and storage

## Installation

### Prerequisites

- Node.js and npm installed
- Laravel installed
- MySQL or any other supported database

### Backend Installation

1. Clone the repository

    ```bash
    git clone https://github.com/dkhomran/Gestion_remboursement_backend.git
    cd reimbursement-system-backend
    ```

2. Install dependencies

    ```bash
    composer install
    ```

3. Create a copy of `.env.example` and rename it to `.env`

    ```bash
    cp .env.example .env
    ```

4. Generate an application key

    ```bash
    php artisan key:generate
    ```

5. Configure your database in the `.env` file

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=insurance
   DB_USERNAME=root
   DB_PASSWORD=

6. Run database migrations

    ```bash
    php artisan migrate
    ```

7. Seed the database with initial data

    ```bash
    php artisan db:seed
    ```

8. Default admin account

- email : admin@admin.com
- password : password

9. Start the Laravel server

    ```bash
    php artisan serve
    ```

### Backend Configuration

Configure the following environment variables in the `.env` file:

## API Endpoints
### Authentication
-  Register a New User: POST /api/register
-  Log In a User: POST /api/login
- Get Authenticated User: GET /api/profile
- Log Out a User: GET /api/logout
-  Refresh Token :  POST /api/refresh-token

### Users
-  Get confirmed employees: GET /confirmed-employee
-  Get pending employees: GET /api/pending-employee
- Create employee: POST /api/store-employee
-  Update a employee: POST /api/update-employee/{id}
-  Delete/Reject a employee: DELETE /api/delete-employee/{id}
-  Confirm employee: POST /api/confirm-employee/{id}
-  Update password: POST /api/update-password


### Categories
-  Get All Categories: GET /api/categories
-  Create a New Category: POST /api/store-category
-  Get a Specific Category: GET /api/fetch-category/{id}
-  Update a Category: POST /api/update-category/{id}
-  Delete a Category: DELETE /api/delete-category/{id}


### Type Fees
-  Get All type fees: GET /api/typefees
-  Create a New type fee: POST /api/store-type-fee
-  Get a Specific type fee: GET /api/fetch-typefee/{id}
-  Update a type fee: POST /api/update-typefee/{id}
-  Delete a type fee: DELETE /api/delete-typefee/{id}

### Policies
-  Get All Policies: GET /api/policies
-  Create a New Policy: POST /api/store-policy
-  Get a Specific Policy: GET /api/fetch-policy/{id}
-  Update a Policy: POST /api/update-policy/{id}
-  Delete a Policy: DELETE /api/delete-policy/{id}

### Reimbursement Management
-  Get All refund demands: GET /api/refund-demands
-  Get pending refund demands: GET /api/refunds-pending
-  Accept a refund demand: POST /api/refund/{id}/accept
-  Reject a refund demand: POST /api/refund/{id}/reject 
-  Create a refund demand: POST /api/refunds-store-by-RH
-  Get monthly refund statistics : GET api/refund-monthly-statistics

