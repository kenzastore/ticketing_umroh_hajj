# Digitalisasi Ticketing Umroh & Haji

A centralized, web-based system designed to streamline the end-to-end ticketing process for Umroh and Haji operations. This platform replaces fragmented spreadsheets with a real-time, mobile-responsive solution for managing requests, flight bookings, payments, and daily movements.

## 🚀 Features

*   **Request Management:** Centralized handling of Group and Individual (FID) requests with scheduling and airline preferences.
*   **Booking Lifecycle:** Track status from `NEW` to `PNR_ISSUED`, `INVOICED`, and `PAID_FULL`.
*   **Real-time Flight Monitoring:** Track flight status (Scheduled, Airborne, Arrived, Delayed) with automated polling (mock/live).
*   **Finance Module:** Generate invoices, track payments, and manage "FP Merah" status.
*   **Master Data:** Management interfaces for Agents and Users.
*   **Role-Based Access Control (RBAC):** Distinct dashboards for Admin, Finance, and Monitor roles.
*   **Audit Logging:** Track critical changes to requests and bookings.

## 🛠️ Tech Stack

*   **Backend:** Native PHP 7.4+ (No heavy frameworks)
*   **Database:** MariaDB / MySQL
*   **Frontend:** HTML5, CSS3, Bootstrap 5, Vanilla JavaScript
*   **Architecture:** Simple MVC-like structure (Models, Views, Controllers)

## 📋 Prerequisites

*   PHP >= 7.4
*   MySQL or MariaDB
*   Terminal / Command Line

## ⚙️ Installation & Setup

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/yourusername/ticketing_umroh_hajj.git
    cd ticketing_umroh_hajj
    ```

2.  **Database Setup**
    *   Create a database (e.g., `ticketing_umroh`).
    *   Import the schema:
        ```bash
        mysql -u root -p ticketing_umroh < database/schema.sql
        ```
    *   *Note: If you need the Finance or Flight modules, run their specific migrations if they haven't been merged into `schema.sql` yet.*

3.  **Configuration**
    *   Edit `config/db.php` to match your database credentials:
        ```php
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'ticketing_umroh');
        define('DB_USER', 'your_db_user');
        define('DB_PASS', 'your_db_pass');
        ```

4.  **Seed Dummy Data**
    *   Populate the database with Indonesian dummy data for testing:
        ```bash
        php database/seed.php
        ```
    *   *(Optional)* Run the flight sync cron to seed flight data:
        ```bash
        php cron/cron_sync_flights.php
        ```

## 🚀 Running the Application

Start the built-in PHP development server:

```bash
php -S localhost:8080 -t public/
```

Access the application at: **http://localhost:8080/login.php**

### Default Credentials

*   **Admin:** `admin` / `admin`
    *   *Full access to Dashboard, Requests, Master Data, and Flight Monitor.*
*   **Finance:** `finance` / `password123`
    *   *Access to Finance Dashboard.*
*   **Monitor:** `monitor` / `password123`
    *   *Access to Monitor Dashboard.*

*(Note: Other generated users have the password `password123`)*

## 📂 Project Structure

```
ticketing_umroh_hajj/
├── app/
│   └── models/          # Database models (User, Agent, etc.)
├── config/              # Configuration files (db.php, flight_api.php)
├── cron/                # Scheduled tasks (Flight sync)
├── database/            # SQL schema and seed scripts
├── includes/            # Helper functions (auth, db_connect)
├── public/              # Web root (CSS, JS, PHP pages)
│   ├── admin/           # Admin pages (Dashboard, Requests, Masters)
│   ├── api/             # API endpoints (Movement data)
│   ├── assets/          # Static assets (CSS/JS)
│   ├── finance/         # Finance pages
│   ├── monitor/         # Monitor pages
│   └── shared/          # Shared layout files (Header/Footer)
└── conductor/           # Project documentation and specs
```

## 🤝 Contributing

1.  Fork the repository.
2.  Create your feature branch (`git checkout -b feature/AmazingFeature`).
3.  Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4.  Push to the branch (`git push origin feature/AmazingFeature`).
5.  Open a Pull Request.

## 📝 License

This project is licensed under the MIT License.
