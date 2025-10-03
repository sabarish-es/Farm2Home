START TRANSACTION;
ALTER TABLE users
  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER created_at,
  ADD INDEX idx_users_deleted_at (deleted_at);
COMMIT;
