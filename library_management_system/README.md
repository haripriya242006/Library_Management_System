# Library Management System

A beginner-friendly college mini project using **HTML, CSS, JavaScript, PHP, MySQL, and XML**.

## Features
- Dashboard with book, student, and active issue counts
- Add, search, and delete books
- Register students
- Issue books to students and return them
- MySQL database connectivity using PHP PDO prepared statements
- Responsive layout and JavaScript form confirmation/validation
- XML example file showing structured book data

## Requirements
- Windows, macOS, or Linux
- XAMPP (Apache + PHP + MySQL/MariaDB) or another PHP/MySQL environment
- A web browser

## Setup using XAMPP (Windows)
1. Extract the ZIP file.
2. Copy the `library_management_system` folder into `C:\xampp\htdocs\`.
3. Open the XAMPP Control Panel and start **Apache** and **MySQL**.
4. Open `http://localhost/phpmyadmin`.
5. Choose **Import**, select this project's `database.sql`, and click **Import/Go**.
   - The SQL file creates the `library_management` database and sample records.
6. Check `config.php`. The default XAMPP MySQL username is usually `root` and password is blank. Change these if your installation uses different credentials.
7. Open `http://localhost/library_management_system/`.

## Pages
- `index.php` — Dashboard
- `books.php` — Book catalogue and book management
- `students.php` — Student registration
- `issues.php` — Issue and return records

## How the connectivity works
Browser form → PHP page → PDO prepared statement → MySQL database → PHP renders updated HTML.

`database.sql` defines the database tables. `books.xml` is an XML example for learning structured data; XML is not required for the database connection itself.

## Important
- This is a learning/demo project and does not include user login or role-based permissions.
- Do not expose a development database or use this demo as-is for a public production site.
- For the class demo, run it locally through Apache. Do not open PHP files directly as `file:///...`.
