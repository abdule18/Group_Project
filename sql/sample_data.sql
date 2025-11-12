-- Seed data
INSERT IGNORE INTO categories (name) VALUES ('Account'),('Networking'),('Hardware'),('Software'),('Other');
INSERT IGNORE INTO priorities (name) VALUES ('Low'),('Medium'),('High'),('Critical');
INSERT IGNORE INTO statuses (name) VALUES ('Open'),('In Progress'),('Resolved'),('Closed');

-- Admin user: email admin@example.com / password: Admin123!
INSERT IGNORE INTO users (name, email, password_hash, role) VALUES
('Admin User','admin@example.com', '$2y$10$k7F4E0m7V7R8xv2a8FqCGe1D0mM3q8o1d4A7iS9vFqJv1v6QkK9bi','admin');
