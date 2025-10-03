START TRANSACTION;

ALTER TABLE events
  ADD COLUMN farmer_id INT NULL AFTER location;

-- Backfill existing events to the first farmer account
UPDATE events
SET farmer_id = (
  SELECT id FROM users WHERE role = 'farmer' ORDER BY id ASC LIMIT 1
)
WHERE farmer_id IS NULL;

ALTER TABLE events
  MODIFY farmer_id INT NOT NULL,
  ADD INDEX idx_events_farmer_id (farmer_id),
  ADD CONSTRAINT fk_events_farmer FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE;

COMMIT;
