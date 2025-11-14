-- This runs automatically the first time the main MySQL container starts
CREATE DATABASE IF NOT EXISTS hras;

CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY 'root';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;

-- Optional: create dedicated app user instead of root
CREATE USER IF NOT EXISTS 'user'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON hras.* TO 'user'@'%';
FLUSH PRIVILEGES;
