START TRANSACTION;

CREATE TABLE IF NOT EXISTS admin_access_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  status ENUM('pending','approved','denied') NOT NULL DEFAULT 'pending',
  requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  decided_at TIMESTAMP NULL,
  decided_by INT NULL,
  CONSTRAINT fk_admin_req_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_admin_req_decider FOREIGN KEY (decided_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_admin_req_status (status),
  INDEX idx_admin_req_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
