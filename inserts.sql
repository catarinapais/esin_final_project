
INSERT INTO BookingType(type) VALUES ('sitting');
INSERT INTO BookingType(type) VALUES ('walking');

INSERT INTO Person (id, name, phone_number, address, email, city) VALUES (1, 'John Doe', '123456789', 'Rua do Ouro 100', 'johndoe@gmail.com', 'Porto');
INSERT INTO Person (id, name, phone_number, address, email, city) VALUES (2, 'Ema Beira', '987654321', 'Rua do Ouro 101', 'ema@gmail.com', 'Aldoar');

INSERT INTO ServiceProvider (person, iban, service_type, avg_rating) VALUES (1, 'PT50000201231234567890154', 'sitting', 4.5);
INSERT INTO ServiceProvider(person, iban, service_type, avg_rating) VALUES (2, '111', 'both', 4.5);

INSERT INTO Schedule (id, day_week, start_time, end_time, service_provider) VALUES (1, 'Monday', '10:00', '12:00', 1);
INSERT INTO Schedule (id, day_week, start_time, end_time, service_provider) VALUES (2, 'Tuesday', '07:00', '11:00', 1);

INSERT INTO PetOwner(person, avg_rating) VALUES (1, 4.5);
INSERT INTO PetOwner (person, avg_rating) VALUES (2, 4.5);

INSERT INTO Pet (id, name, species, size, birthdate, owner)  VALUES (1, 'Rex', 'Dog', 'large', '15/08/2020', 1);
INSERT INTO Pet (id, name, species, size, birthdate, owner) VALUES (2, 'Whiskers', 'Cat', 'small', '15/08/2020', 1);
INSERT INTO Pet (id, name, species, size, birthdate, owner) VALUES (3, 'Buddy', 'Dog', 'medium', '15/08/2020', 1);

INSERT INTO Message(id, sender, message_body, send_time, is_read, owner, provider) VALUES (1,1,'Hello', '15:20', 0, 1, 2);
INSERT INTO Message(id, sender, message_body, send_time, is_read, owner, provider) VALUES (2,1,'Hi', '15:21', 0, 1, 2);

INSERT INTO Payment (id, is_paid, price, payment_date) VALUES (1, 0, 10.0, NULL);

-- TODO: quando se criar um booking, por o review com id=0 (review null)
INSERT INTO Review (id, rating, description, date_review) VALUES (0, 0, NULL, NULL); -- importante manter este aqui!!!!

INSERT INTO Review (id, rating, description, date_review) VALUES (1, 5, 'Great service', '2021-06-01');
INSERT INTO Review (id, rating, description, date_review) VALUES (2, 4, 'Good service', '2021-06-01');

INSERT INTO Booking (date, start_time, end_time, address_collect, photo_consent, provider, type, pet, payment, ownerReview, providerReview) VALUES ('2021-06-01', '10:00', '12:00', 'Rua do Ouro 100', 'yes', 2, 'sitting', 2, 1, 0, 2);
