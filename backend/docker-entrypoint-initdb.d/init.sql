CREATE DATABASE IF NOT EXISTS control_acceso;
CREATE USER IF NOT EXISTS 'control_user'@'%' IDENTIFIED BY 'control_password';
GRANT ALL PRIVILEGES ON control_acceso.* TO 'control_user'@'%';
FLUSH PRIVILEGES;
