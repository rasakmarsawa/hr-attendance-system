-- This runs automatically the first time the test MySQL container starts
CREATE DATABASE IF NOT EXISTS hras_test;

CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY 'root';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;

CREATE USER IF NOT EXISTS 'user'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON hras_test.* TO 'user'@'%';
FLUSH PRIVILEGES;
