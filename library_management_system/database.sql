CREATE DATABASE IF NOT EXISTS library_management
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE library_management;

CREATE TABLE IF NOT EXISTS books (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  author VARCHAR(120) NOT NULL,
  category VARCHAR(80) NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  available INT UNSIGNED NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_books_title (title),
  INDEX idx_books_author (author)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS students (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  department VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS issues (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  book_id INT UNSIGNED NOT NULL,
  student_id INT UNSIGNED NOT NULL,
  status ENUM('Issued', 'Returned') NOT NULL DEFAULT 'Issued',
  issued_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  returned_at TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_issues_book FOREIGN KEY (book_id) REFERENCES books(id),
  CONSTRAINT fk_issues_student FOREIGN KEY (student_id) REFERENCES students(id),
  INDEX idx_issues_status (status)
) ENGINE=InnoDB;

-- Optional sample data. Remove these INSERT statements if you want an empty database.
INSERT INTO books (title, author, category, quantity, available) VALUES
('The Alchemist', 'Paulo Coelho', 'Fiction', 3, 3),
('Wings of Fire', 'A. P. J. Abdul Kalam', 'Biography', 2, 2),
('Clean Code', 'Robert C. Martin', 'Programming', 2, 2);

INSERT INTO students (name, email, department) VALUES
('Demo Student', 'student@example.com', 'Computer Science');
