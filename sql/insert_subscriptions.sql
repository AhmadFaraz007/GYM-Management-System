-- Insert subscriptions for member_id 3
INSERT INTO subscriptions (member_id, package_name, amount_paid, start_date, end_date, payment_status) VALUES
-- Basic Monthly Package
(3, 'Basic Monthly', 49.99, '2024-03-01', '2024-04-01', 'paid'),

-- Premium 3-Month Package
(3, 'Premium 3-Month', 129.99, '2024-04-01', '2024-07-01', 'paid');

-- Insert subscriptions for member_id 5
INSERT INTO subscriptions (member_id, package_name, amount_paid, start_date, end_date, payment_status) VALUES
-- Elite 6-Month Package
(5, 'Elite 6-Month', 249.99, '2024-03-01', '2024-09-01', 'paid'),

-- VIP Annual Package
(5, 'VIP Annual', 399.99, '2024-03-01', '2025-03-01', 'paid');

-- Insert pending subscriptions for member_id 6
INSERT INTO subscriptions (member_id, package_name, amount_paid, start_date, end_date, payment_status) VALUES
-- Premium 3-Month Package (Pending)
(6, 'Premium 3-Month', 129.99, '2024-03-15', '2024-06-15', 'pending');

-- Insert pending subscription for member_id 7
INSERT INTO subscriptions (member_id, package_name, amount_paid, start_date, end_date, payment_status) VALUES
-- Elite 6-Month Package (Pending)
(7, 'Elite 6-Month', 249.99, '2024-03-20', '2024-09-20', 'pending'); 