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
    phone_number INTEGER UNIQUE,
    address TEXT,
    email TEXT NOT NULL UNIQUE,
    city TEXT NOT NULL,
    password TEXT
);

-- Table PetOwner -- 
CREATE TABLE PetOwner(
    person INTEGER PRIMARY KEY REFERENCES Person,
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
    is_read INTEGER NOT NULL CHECK (is_read IN (0,1)),
    owner INTEGER REFERENCES PetOwner,
    provider INTEGER REFERENCES ServiceProvider
);

-- Table BookingType --
CREATE TABLE BookingType (
    type TEXT PRIMARY KEY CHECK (type IN ('sitting', 'walking'))
);

-- Table Payement -- TIRAR


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
    size TEXT CHECK (size IN ('small', 'medium', 'large')),
    birthdate string,
    profile_picture TEXT,
    owner INTEGER REFERENCES PetOwner
);

-- Table MedicalNeed --
CREATE TABLE MedicalNeed (
    id INTEGER PRIMARY KEY,
    description TEXT
);

-- Table PetMedicalNeed --
CREATE TABLE PetMedicalNeed(
    pet INTEGER REFERENCES PET,
    medicalNeed TEXT REFERENCES MedicalNeed,
    PRIMARY KEY (pet,medicalNeed)
);

CREATE TABLE Booking (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    date TEXT NOT NULL,
    start_time TEXT,
    end_time TEXT CHECK(end_time IS NULL OR end_time>start_time),
    duration INTEGER CHECK (duration>0),
    address_collect TEXT NOT NULL,
    photo_consent TEXT CHECK (photo_consent IN ('YES', 'NO')) NULL,
    review_consent TEXT CHECK (review_consent IN ('YES', 'NO')) NULL,
    provider INTEGER NOT NULL REFERENCES ServiceProvider ON DELETE CASCADE, --istoassegura que, ao excluir um provider, todos os agendamentos relacionados sejam automaticamente removidos.  
    type TEXT NOT NULL REFERENCES BookingType,
    pet INTEGER NOT NULL REFERENCES Pet, -- (id)
    payment REAL CHECK (price>0),
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
