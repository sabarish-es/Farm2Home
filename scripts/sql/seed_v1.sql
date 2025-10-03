/* Example data */
INSERT INTO users (role,name,email,password_hash) VALUES
('farmer','Green Acres Farm','farmer1@example.com', '$2y$10$w9YpZ3E5GvO8h7b7rjJ8yu2mZkA3vJwQn4t2zX3bq9h4K1d2pXo8i'), -- password: password123
('customer','Asha','asha@example.com', '$2y$10$w9YpZ3E5GvO8h7b7rjJ8yu2mZkA3vJwQn4t2zX3bq9h4K1d2pXo8i');  -- password: password123

INSERT INTO products (farmer_id,name,description,category,price,stock,image_path,active) VALUES
(1,'Organic Tomatoes','Juicy organic tomatoes.','Vegetables',60,50,'assets/img/sample-veg.jpg',1),
(1,'Fresh Mangoes','Sweet Alphonso mangoes.','Fruits',120,30,'assets/img/sample-fruit.jpg',1);

INSERT INTO events (title,description,event_date,location) VALUES
('Sunday Farmer’s Market','Local farmers bring fresh produce.','2025-10-12','Community Park'),
('Organic Awareness Camp','Learn about organic practices.','2025-11-05','Town Hall');
