PRAGMA foreign_keys= ON;
.headers ON
.mode columns

DROP TABLE IF EXISTS PetMedicalNeed;
DROP TABLE IF EXISTS Booking;
DROP TABLE IF EXISTS Review;
DROP TABLE IF EXISTS Payment;
DROP TABLE IF EXISTS BookingType;
DROP TABLE IF EXISTS MedicalNeed;
DROP TABLE IF EXISTS Pet;
DROP TABLE IF EXISTS Message;
DROP TABLE IF EXISTS Schedule;
DROP TABLE IF EXISTS ServiceProvider;
DROP TABLE IF EXISTS PetOwner;
DROP TABLE IF EXISTS Person;

-- Table Person --
CREATE TABLE Person (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    phone_number TEXT,
    adress TEXT,
    email TEXT NOT NULL,
    city TEXT NOT NULL
);

-- Table PetOwner -- 
CREATE TABLE PetOwner(
    person   INTEGER PRIMARY KEY REFERENCES Person,
    avg_rating REAL
);

-- Table ServiceProvider -- 
CREATE TABLE ServiceProvider(
    person INTEGER PRIMARY KEY REFERENCES Person,
    iban TEXT UNIQUE NOT NULL,
    service_type TEXT CHECK(service_type IN ('sitting','walking','both')),
    avg_rating REAL
);

-- Table Schedule --
 CREATE TABLE Schedule(
    id INTEGER PRIMARY KEY,
    day_week TEXT NOT NULL,
    start_time TEXT NOT NULL,
    end_time TEXT NOT NULL CHECK(end_time>start_time),
    service_provider INTEGER NOT NULL REFERENCES ServiceProvider
 );

-- Table Message --
CREATE TABLE Message (
    id INTEGER PRIMARY KEY,
    sender INTEGER NOT NULL,
    message_body TEXT NOT NULL,
    send_time TEXT NOT NULL,
    is_read BOOLEAN,
    owner INTEGER REFERENCES PetOwner,
    provider INTEGER REFERENCES ServiceProvider
);

-- Table BookingType --
CREATE TABLE BookingType (
    type TEXT PRIMARY KEY CHECK (type IN ('sitting', 'walking'))
);

-- Table Payement --
CREATE TABLE Payment(
    id INTEGER PRIMARY KEY,
    is_paid BOOLEAN,
    price REAL CHECK (price>0),
    payment_date TEXT CHECK (is_paid = 0 OR payment_date IS NOT NULL)
);

-- Table Review -- 
CREATE TABLE Review(
    id INTEGER PRIMARY KEY,
    rating INTEGER CHECK (rating IS NULL OR (rating>=0 AND rating <=5)),
    description TEXT,
    date_review TEXT
);

-- Table Pet --
CREATE TABLE Pet (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    species TEXT NOT NULL,
    size REAL CHECK (size>0),
    age INTEGER CHECK (age>0),
    profile_picture TEXT,
    owner INTEGER REFERENCES PetOwner
);

-- Table MedicalNeed --
CREATE TABLE MedicalNeed (
    type TEXT PRIMARY KEY,
    description TEXT
);

-- Table PetMedicalNeed --
CREATE TABLE PetMedicalNeed(
    pet INTEGER REFERENCES PET,
    medicalNeed TEXT REFERENCES MedicalNeed,
    PRIMARY KEY (pet,medicalNeed)
);

-- Table Booking --
CREATE TABLE Booking (
    id INTEGER PRIMARY KEY,
    start_time TEXT,
    end_time TEXT CHECK(end_time IS NULL OR end_time>start_time),
    duration REAL CHECK (duration>0),
    accepted_by_provider BOOLEAN,
    adress_collect TEXT NOT NULL,
    adress_dropoff TEXT NOT NULL,
    provider INTEGER NOT NULL REFERENCES ServiceProvider(person),
    type TEXT NOT NULL REFERENCES BookingType,
    pet INTEGER NOT NULL REFERENCES Pet, -- (id)
    payment INTEGER REFERENCES Payment, -- (id)
    ownerReview INTEGER REFERENCES Review,-- ... --
    providerReview INTEGER REFERENCES Review
);

-- Trigger to calculate duration on INSERT
CREATE TRIGGER calculate_duration_after_insert
AFTER INSERT ON Booking
FOR EACH ROW
WHEN NEW.end_time IS NOT NULL
BEGIN
    UPDATE Booking
    SET duration = (julianday(NEW.end_time) - julianday(NEW.start_time)) * 24 * 60
    WHERE id = NEW.id;
END;

-- Trigger to update duration on UPDATE
CREATE TRIGGER calculate_duration_after_update
AFTER UPDATE OF end_time, start_time ON Booking
FOR EACH ROW
WHEN NEW.end_time IS NOT NULL
BEGIN
    UPDATE Booking
    SET duration = (julianday(NEW.end_time) - julianday(NEW.start_time)) * 24 * 60
    WHERE id = NEW.id;
END;

INSERT INTO Person (id, name, phone_number, adress, email, city) VALUES (1, 'John Doe', '123456789', 'Rua do Ouro 100', 'johndoe@gmail.com', 'Porto');
INSERT INTO Person (id, name, phone_number, adress, email, city) VALUES (2, 'Ema Beira', '987654321', 'Rua do Ouro 101', 'ema@gmail.com', 'Aldoar');
INSERT INTO ServiceProvider (person, iban, service_type, avg_rating) VALUES (1, 'PT50000201231234567890154', 'sitting', 4.5);
INSERT INTO Schedule (id, day_week, start_time, end_time, service_provider) VALUES (1, 'Monday', '10:00', '12:00', 1);
INSERT INTO Schedule (id, day_week, start_time, end_time, service_provider) VALUES (2, 'Tuesday', '07:00', '11:00', 1);

INSERT INTO PetOwner(person, avg_rating) VALUES (1, 4.5);
INSERT INTO ServiceProvider(person, iban, service_type, avg_rating) VALUES (2, 111, 'both', 4.5);


INSERT INTO PetOwner (person, avg_rating) VALUES (1, 4.5);
INSERT INTO Pet (name, species, size, age, profile_picture, owner)  VALUES ('Rex', 'Dog', 'Large', 5, 'cat.jpg',1);

INSERT INTO Pet (name, species, size, age, owner) VALUES ('Whiskers', 'Cat', 'Small', 3, 1);
INSERT INTO Pet (name, species, size, age, owner) VALUES ('Buddy', 'Dog', 'Medium', 2, 1);

INSERT INTO Message(id, sender, message_body, send_time, is_read, owner, provider) VALUES (1,1,'Hello', '15:20', 0, 1, 2);
INSERT INTO Message(id, sender, message_body, send_time, is_read, owner, provider) VALUES (2,1,'Hi', '15:21', 0, 1, 2);
INSERT INTO Message(id, sender, message_body, send_time, is_read, owner, provider) VALUES (3,2,'How are you?', '15:22', 0, 1, 2);