# Internal_Project
Internal_Project  is a Laravel 12 web app for managing a dental lab. It supports two roles: Admin and Lab Technician.
Admins and lab technicians can manage patients (keyed by Predict3DId), products/inventory, payments (including payment plans and installments), and production steps. Lab technicians can submit product requests, which admins approve or reject. Both roles use a cart for products; admins also see a confirmed-orders list. The app provides Excel exports for patients and payments, and a storage proxy for serving uploaded files (e.g. on XAMPP/Windows).
Built with PHP 8.2+, Laravel 12, MySQL, Blade, Bootstrap 5, and Vite, with session-based auth and role-based routing (/admin/* and /lab/*).
