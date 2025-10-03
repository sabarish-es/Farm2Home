ALTER TABLE orders
  ADD COLUMN delivery_time DATETIME NULL AFTER delivery_slot;
