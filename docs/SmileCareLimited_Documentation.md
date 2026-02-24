# Smile Care Limited – Dental Laboratory Management System Documentation

## 1. Overview
- **Purpose**: Manages dental lab operations for Smile Care Limited: patient records, product inventory, payments, production steps, and lab product requests.
- **Primary Entities**: `Patient`, `Inventory`/`Product`, `ProductRequest`, `Cart`/`CartItem`, `Payment`, `PaymentPlan`/`PaymentPlanPayment`, `ProductionStep`, `User`.
- **User Roles**: `admin`, `lab_technician` with role-based access and segregated route groups.

## 2. Technology Stack
- **Framework**: Laravel 12 (`composer.json` requires `laravel/framework:^12.0`)
- **Language**: PHP 8.2+
- **Database**: MySQL
- **Frontend**: Blade, Bootstrap 5, Vite (via `npm run dev`), Sass (if used in views)
- **Excel Exports**: `maatwebsite/excel` and `phpoffice/phpspreadsheet`
- **Dev tools**: Laravel Pail, Pint, PHPUnit, Sail

Key packages (`composer.json`):
- `laravel/ui` for auth scaffolding
- `maatwebsite/excel` and `phpoffice/phpspreadsheet` for export

## 3. Project Structure
- `app/Http/Controllers/`
  - `AdminController.php`: Admin dashboard, patients, inventory/products, product requests, payments, payment plans, production steps, lab technicians, file serving proxy.
  - `AdminCartController.php`: Admin cart workflow.
  - `LabTechnicianController.php`: Lab dashboard, patients, inventory (lab), payments, product requests, production steps, patient cases.
  - `LabCartController.php`: Lab cart workflow.
  - `Auth/*`: Auth controllers (login/register/reset/etc.).
- `app/Models/`
  - `Patient`, `Inventory`, `Product`, `ProductRequest`, `Cart`, `CartItem`, `Payment`, `PaymentPlan`, `PaymentPlanPayment`, `ProductionStep`, `User`.
- `database/migrations/`: Schema definitions (tables for users, cache, jobs, patients, inventory, payments, products, product requests, carts, payment plans, production steps, etc.). Includes several fixes/evolution migrations.
- `resources/views/`
  - `admin/*`: Dashboards, patient CRUD, inventory/product CRUD, product requests, payments, payment plan UI, production.
  - `lab/*`: Lab-facing dashboard, patients, inventory, payments, products listing, request cart, production.
  - `auth/*`, `layouts/*`, `home.blade.php`, `welcome.blade.php`.
- `routes/web.php`: Route definitions with groups for admin and lab roles; auth routes.

## 4. Routing and Access Control
Defined in `routes/web.php`.

- **Auth**
  - `GET /` redirects to `login`.
  - `GET /login` `AuthController@showLoginForm` (named `login`)
  - `POST /login` `AuthController@login`
  - `POST /logout` `AuthController@logout` (named `logout`)

- **Static Storage Proxy (Windows/XAMPP helper)**
  - `GET /storage/{path}` and `GET /files/{path}` → `AdminController@servePublic`
    - Serves `storage/app/public/...` when symlink to `public/storage` is unreliable.

- **Admin routes** (`prefix: admin`, `name: admin.*`, middleware: `auth`, `admin`)
  - Dashboard: `GET /admin/dashboard` → `AdminController@dashboard`
  - Patients CRUD: index/create/store/show/edit/update/destroy, model bound by `Predict3DId`.
  - Patients shortlist: last 15 days by patients/doctors; Excel exports (all, last-month, range).
  - Inventory/Products: listing, create/store/show/edit/update/destroy.
  - Cart: view/add/update/delete/confirm/list confirmed (`AdminCartController`).
  - Product requests: approve/reject (`AdminController@approveProductRequest`, `rejectProductRequest`).
  - Payments listing + exports/shortlist/installments; create/store.
  - Payment plan (Predict3DId-based): index/find patient/get plan/save plan/add installment/update total.
  - Lab Technicians: CRUD for lab users.
  - Production: index, find patient by Predict3DId, save patient cases; production steps get/save.

- **Lab routes** (`prefix: lab`, `name: lab.*`, middleware: `auth`, `lab_technician`)
  - Dashboard: `GET /lab/dashboard`.
  - Patients: index/create/store/show/edit/update/destroy (mirrors admin capability in routes; controller validates similarly).
  - Product Request Cart: list/create/store/show (lab techs request products; admins review).
  - Inventory (lab): index/create/store/show/edit/update/destroy for lab-managed inventory entries.
  - Payments (lab): index/create/store/show/edit/update/destroy for lab recorded payments.
  - Production: index, find patient by Predict3DId, save patient cases; production steps get/save.
  - Products listing for lab; lab cart: view/add/update/delete/confirm (`LabCartController`).

- **Debug route (to remove in production)**
  - `GET /debug/product-requests` dumps `ProductRequest` and `User` raw info.

Middleware `admin` and `lab_technician` are referenced by name (ensure they exist and are registered in `app/Http/Kernel.php`).

## 5. Controllers: Responsibilities and Key Methods

- `App\Http\Controllers\AdminController`
  - `dashboard()`: Stats and 12-month analytics using `Carbon` and `DB` for charts.
  - Patients: `patients()`, shortlist (`shortlistPatients`, `shortlistDoctors`), exports (`exportPatientsAll`, `exportPatientsLastMonth`, `exportPatientsRange`), CRUD: `createPatient`, `storePatient`, `showPatient`, `editPatient`, `updatePatient`, `deletePatient`.
    - Model binding by `Predict3DId`; strong validation.
  - Inventory/Products: `inventory`, `createProduct`, `storeProduct`, `showProduct`, `editProduct`, `updateProduct`, `deleteProduct`.
    - Images stored in `storage/app/public/products` and deleted on update/delete.
  - Product Requests: `approveProductRequest`, `rejectProductRequest` (status transitions: pending → approved/rejected with audit fields).
  - Payments: `payments()` (summary totals), `createPayment`, `storePayment`.
  - Payment Plans (Predict3DId-based): `paymentPlanIndex`, `getPaymentPlan`, `savePaymentPlan`, `addInstallmentPayment`, `updatePlanTotal`.
  - Lab Technicians management: list/create/store/edit/update/delete with validation and password hashing.
  - Production: `productionIndex`, `findPatientByPredictId`, `savePatientCases`, `getProductionSteps`, `saveProductionSteps` (admin equivalents).
  - Utility: `servePublic($path)` proxy files from public storage.

- `AdminCartController`
  - Manages add/update/delete/confirm cart operations for admin; uses DB transactions and stock locking semantics.

- `LabTechnicianController`
  - Mirrors many admin flows for lab side (patients, inventory, payments) with appropriate views and ownership checks for product requests.
  - Production JSON APIs:
    - `getProductionSteps(predict3dId)` → returns `{steps:[{step_number, upper_value, lower_value}]}`.
    - `saveProductionSteps(request,predict3dId)` → upsert of steps; ensures legacy columns filled; returns saved steps JSON.
    - `savePatientCases(request,predict3dId)` → updates `UpperCases`/`LowerCases`; returns JSON.

- `LabCartController`
  - Lab-facing product listing and cart management identical to admin cart patterns.

## 6. Models and Data

- `Patient`
  - PK: `Predict3DId` (string, non-incrementing). See `getRouteKeyName()`.
  - Fillable: name, contact, case fields, counts, status, `created_by`.
  - Relations: `creator()` → `User`; `payments()` → hasMany `Payment` via `Predict3DId`.

- `Inventory`
  - Table: `inventory`.
  - Fields: item metadata, quantity, unit price, supplier, expiry_date, description, status, `managed_by`.
  - Relation: `manager()` → `User`.

- `Product`
  - Fields: name, price, quantity, description, image, status, created_by, requested_by.
  - Relations: `creator()` and `requester()` → `User`.

- `ProductRequest`
  - Fields: name, price, quantity, description, image, `status` (pending|approved|rejected), `requested_by`, `reviewed_by`, `reviewed_at`, `rejection_reason`.
  - Relations: `requester()`, `reviewer()` → `User`.

- `Cart` / `CartItem`
  - `Cart`: user_id, status (`open`|`checked_out`), confirmed_at; scopes: `confirmed()`.
  - `CartItem`: cart_id, product_id, quantity, unit_price.

- `Payment`
  - Fields: patient_id (Predict3DId), amount, method, date, description, status, processed_by.
  - Relations: `patient()` → `Patient` via Predict3DId; `processor()` → `User`.

- `PaymentPlan` / `PaymentPlanPayment`
  - Plan: predict3d_id, total_amount, payment_method, is_installment, next_payment_date, remaining_amount, created_by.
  - Payment: payment_plan_id, amount, payment_method, payment_date, created_by.

- `ProductionStep`
  - Fields: predict3d_id, step_number, upper_value, lower_value; legacy-compat columns `patient_predict3d_id`, `step_type`, `created_by` are fillable and casted.

- `User`
  - Fields: name, username, email, password, role, status. Helpers: `isAdmin()`, `isLabTechnician()`.

## 7. Database Schema and Migrations
See `database/migrations/` for full details. Notable migrations:
- Base: users, cache, jobs.
- Role add-on: `2024_01_01_000000_add_role_to_users_table.php`.
- Domain tables: patients, inventory, payments, products, product_requests, carts (+confirmed_at), payment_plans (+payments), production_steps.
- Patient PK evolution and additions: fix/recreate to make `Predict3DId` the PK; add `FullName`, etc.
- Production steps: initial create and several relax/fix migrations for column nullability and legacy compatibility.

## 8. Views
- Admin views under `resources/views/admin/`:
  - Patients: `index/create/edit/show/shortlist`
  - Inventory/Products: `index/create/edit/show`, cart `cart.blade.php`, `confirmed.blade.php`
  - Payments: `index/create/shortlist/installments`, payment plan `plan.blade.php`
  - Production: `production/index.blade.php`
  - Lab technicians: `lab-technicians/index/create/edit`
- Lab views under `resources/views/lab/` mirror admin where applicable and add:
  - Product requests: `request-cart/index/create/show`
  - Products list: `products/index.blade.php`
  - Cart: `cart.blade.php`, `confirmed.blade.php`
- Auth views under `resources/views/auth/*`.

## 9. Key Workflows

- **Patient Management** (`AdminController`, `LabTechnicianController`)
  - Create/Edit with validation. Primary key is `Predict3DId` (string). Route binding uses `{patient:Predict3DId}`.

- **Inventory & Products**
  - Admin manages shared products used by lab cart. Images stored on public disk; use `php artisan storage:link` ideally, or `/storage/*` proxy route on Windows.

- **Product Requests (Lab → Admin)**
  - Lab submits `ProductRequest` (status `pending`).
  - Admin reviews: approve creates a `Product` and marks request as `approved`; reject records reason and marks `rejected`.

- **Cart Workflow**
  - Add/update/delete items adjusts `Product.quantity` within DB transactions.
  - Confirm marks cart `checked_out` and timestamps `confirmed_at`.

- **Payments**
  - Two layers: direct `Payment` records and higher-level `PaymentPlan` with `PaymentPlanPayment` used for analytics, exports, and installment tracking.

- **Production**
  - JSON endpoints for getting/saving production steps and updating case counts (`UpperCases`, `LowerCases`).

## 10. Security, Roles, and Middleware
- All admin routes protected by `auth` + `admin` middleware.
- All lab routes protected by `auth` + `lab_technician` middleware.
- Ensure middleware registration in `app/Http/Kernel.php` and implementation under `app/Http/Middleware/*`.
- Remove the debug route `GET /debug/product-requests` in production.

## 11. File Storage
- Images and exports use the `public` disk. On Windows/XAMPP where symlink may fail, use proxy routes:
  - `GET /storage/{path}` and `GET /files/{path}` → `AdminController@servePublic()`.

## 12. Exports (Excel)
- `AdminController@exportPatients*` and payments export methods use `maatwebsite/excel` with downloadable `.xlsx`.
- Configure queue if exporting large datasets.

## 13. Setup and Running
- Copy `.env` from `.env.example` and configure DB credentials.
- Install dependencies:
  - `composer install`
  - `npm install`
- Generate key and migrate:
  - `php artisan key:generate`
  - `php artisan migrate`
- Storage link (if symlink supported):
  - `php artisan storage:link`
- Start dev environment (from `composer.json`):
  - `composer run dev`
  - This concurrently runs: server, queue listener, pail logs, and Vite.

## 14. Known Issues / Notes
- Debug route present: `GET /debug/product-requests` in `routes/web.php` – remove before production.
- Multiple fix/relax migrations indicate schema evolution. Ensure your migration order is intact on fresh setups.
- On Windows/XAMPP, prefer the storage proxy routes if symlink is unreliable.

## 15. Extensibility and Future Improvements
- Add policies/gates for fine-grained authorization beyond role middleware.
- Add tests for critical flows: patient CRUD with Predict3DId PK; cart concurrency; payment plan calculations; production steps.
- Consider soft deletes for critical models (patients, products, payments).
- Add search and filters to listings (patients/products/payments).
- Add notifications on product request approval/rejection.
- Harden input validation and add rate limiting to JSON endpoints.

## 16. API Summary (JSON endpoints)
- `GET /admin/production/steps/{predict3dId}` → `AdminController@getProductionSteps` returns steps JSON.
- `POST /admin/production/steps/{predict3dId}` → `AdminController@saveProductionSteps` saves steps, returns JSON.
- `GET /lab/production/steps/{predict3dId}` → `LabTechnicianController@getProductionSteps`.
- `POST /lab/production/steps/{predict3dId}` → `LabTechnicianController@saveProductionSteps`.
- `POST /lab/production/save-cases/{predict3dId}` and `POST /admin/production/save-cases/{predict3dId}` → update case counts; returns JSON.

## 17. Contact and Ownership
- Application: Smile Care Limited Laboratory Management System
- Owners: Admin users defined in `users` table with `role=admin`.

