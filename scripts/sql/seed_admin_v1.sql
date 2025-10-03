/*
  Seed a default admin user (idempotent).

  How to use:
  1) Generate a bcrypt password hash in your app or any PHP shell:
       php -r "echo password_hash('ChangeMe123!', PASSWORD_BCRYPT) . PHP_EOL;"
  2) Paste the hash into the @ADMIN_PASSWORD_HASH variable below.
  3) Set the desired admin email and name.
  4) Run this script AFTER running schema_v1.sql and any subsequent schema migrations.

  Notes:
  - This will insert an admin only if no admin user exists yet.
  - You should immediately change the admin password after first login.
*/

START TRANSACTION;

-- set your admin identity here
SET @ADMIN_EMAIL := 'admin@farm2home.local';
SET @ADMIN_NAME := 'Administrator';

-- paste your bcrypt hash here (example placeholder shown, replace it)
-- Example generation: php -r "echo password_hash('ChangeMe123!', PASSWORD_BCRYPT) . PHP_EOL;"
SET @ADMIN_PASSWORD_HASH := '$2y$10$w9YpZ3E5GvO8h7b7rjJ8yu2mZkA3vJwQn4t2zX3bq9h4K1d2pXo8i';

-- Create a default admin only if none exists
INSERT INTO users (role, name, email, password_hash, address, phone)
SELECT 'admin', @ADMIN_NAME, @ADMIN_EMAIL, @ADMIN_PASSWORD_HASH, NULL, NULL
WHERE NOT EXISTS (SELECT 1 FROM users WHERE role = 'admin');

-- Ensure configured admin email has expected role and password (idempotent)
UPDATE users
SET role = 'admin',
    name = @ADMIN_NAME,
    password_hash = @ADMIN_PASSWORD_HASH
WHERE email = @ADMIN_EMAIL;

COMMIT;
