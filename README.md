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
    git clone https://gitlab.com/dekhil22omran/fonds-social_backend.git
    cd fonds-social_backend
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

10. Starts a worker to continuously process jobs in the queue

    ```bash
    php artisan queue:work
    ```

### Backend Configuration

Configure the following environment variables in the `.env` file:

## API Endpoints
### Authentication
-  Register a New User: POST /api/register
-  Log In a User: POST /api/login
-  Get Authenticated User: GET /api/profile
-  Log Out a User: GET /api/logout
-  Refresh Token :  POST /api/refresh-token

### Users
-  Get confirmed employees without VRF PERMISSIONS: GET /api/users/confirmed-employee-all
-  Get confirmed employees VRF PERMISSIONS: GET /api/users/confirmed-employee
-  Get pending employees without VRF PERMISSIONS: GET /api/users/pending-employee-all
-  Get pending employees VRF PERMISSIONS: GET /api/users/pending-employee
-  Create employee: POST /api/users/store-employee
-  Update a employee: POST /api/update-employee/{id}
-  Delete/Reject a employee: DELETE /api/users/delete-employee/{id}
-  Confirm employee: POST /api/users/confirm-employee/{id}
-  Update password: POST /api/update-password


### Categories
-  Get All Categories without VRF PERMISSIONS: GET /api/categories/all
-  Get All Categories VRF PERMISSIONS: GET /api/categories
-  Create a New Category: POST /api/categories/store-category
-  Get a Specific Category: GET /api/categories/fetch-category/{id}
-  Update a Category: POST /api/categories/update-category/{id}
-  Delete a Category: DELETE /api/categories/delete-category/{id}


### Type Fees
-  Get All type fees without VRF PERMISSIONS: GET /api/typefees/all
-  Get All type fees VRF PERMISSIONS: GET /api/typefees
-  Create a New type fee: POST /api/typefees/store-type-fee
-  Get a Specific type fee: GET /api/typefees/fetch-typefee/{id}
-  Update a type fee: POST /api/typefees/update-typefee/{id}
-  Delete a type fee: DELETE /api/typefees/delete-typefee/{id}

### Policies
-  Get All Policies without VRF PERMISSIONS: GET /api/policies/all
-  Get All Policies VRF PERMISSIONS: GET /api/policies
-  Create a New Policy: POST /api/policies/store-policy
-  Get a Specific Policy: GET /api/policies/fetch-policy/{id}
-  Update a Policy: POST /api/policies/update-policy/{id}
-  Delete a Policy: DELETE /api/policies/delete-policy/{id}

### Role & Permissions
-  Get All Roles without VRF PERM: GET /api/roles/all
-  Get All Roles VRF PERM: GET /api/roles
-  Get All Permissions: GET /api/roles/permissions
-  Create a New Role: POST /api/roles/store-role
-  Update a Roles: POST /api/roles/roles/{id}
-  Delete a Roles: DELETE /api/roles/roles/{id}

### Reimbursement Management
-  Get All refund demands VRF PERM: GET /api/refunds/refund-demands
-  Get pending refund demands without VRF PERM: GET /api/refunds/refunds-pending-all
-  Get pending refund demands VRF PERM: GET /api/refunds/refunds-pending
-  Accept a refund demand VRF PERM: POST /api/refunds/refund/{id}/accept
-  Reject a refund demand VRF PERM: POST /api/refunds/refund/{id}/reject 
-  Delete a refund demand VRF PERM: POST /api/refunds/refund-delete/{id} 
-  Create a refund demand VRF PERM: POST /api/refunds/refunds-store-by-RH
-  Update a refund demand by RH VRF PERM: POST /api/refunds/refund-update-by-rh/{id} {user_id} 
-  Update payed refund demand status VRF PERMISSIONS: POST /api/refunds/refunds-update-payed

-  Get All refund demands for auth user VRF PERM: GET /api/refunds-employee/refunds-auth
-  Get last 5 refund demands for auth user VRF PERM: GET /api/refunds-employee/refunds-last-5
-  Create a refund demand by employee VRF PERM: POST /api/refunds-employee/refund-store
-  Delete a refund demand by user VRF PERM: POST /api/refunds-employee/refund-cancel/{id} 
-  Update a refund demand by user VRF PERM: POST /api/refunds-employee//refund-update/{id}

 ### Statistics
-  Get refund statistics for auth user VRF PERM : GET api/stats/refund-monthly-statistics
-  Get refund statistics for all users VRF PERM : GET api/stats/refund-statistics/{period?}/{employeeId?}/{typeFeeId?}

