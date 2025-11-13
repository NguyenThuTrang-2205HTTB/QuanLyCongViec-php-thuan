-- todo_app.sql
-- Import this file into phpMyAdmin or MySQL to create the database and sample data.
CREATE DATABASE IF NOT EXISTS `todo_app` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `todo_app`;

DROP TABLE IF EXISTS `tasks`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tasks` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `due_date` DATE NULL,
  `status` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Demo user: admin / 123456
INSERT INTO `users` (`username`, `password`) VALUES
('admin', '$2y$10$u1Oa6g9Z4mJZqOQKqV6ri.OzU6nq8m9xXbnQ0q5oW0YfQz1yLqk9G'); -- password: 123456

INSERT INTO `tasks` (`user_id`, `title`, `description`, `due_date`, `status`) VALUES
(1, 'Mua nguyên liệu', 'Mua bánh, sữa và trà', DATE_ADD(CURDATE(), INTERVAL 3 DAY), 0),
(1, 'Kiểm tra máy tính', 'Cài đặt bản vá và backup dữ liệu', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 0),
(1, 'Gửi báo cáo', 'Gửi báo cáo tuần cho quản lý', NULL, 1);
