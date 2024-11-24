-- queries.sql

-- Fetch reviews for a service provider
SELECT r.rating, r.description, r.date_review
FROM Review r
JOIN Booking b ON b.providerReview = r.id
WHERE b.provider = 1;

-- Calculate average rating for a provider
SELECT sp.person, AVG(r.rating) AS avg_rating
FROM ServiceProvider sp
JOIN Booking b ON b.provider = sp.person
JOIN Review r ON b.providerReview = r.id
GROUP BY sp.person;

-- Fetch reviews for a pet owner
SELECT r.rating, r.description, r.date_review
FROM Review r
JOIN Booking b ON b.ownerReview = r.id
JOIN PetOwner po ON b.pet IN (SELECT id FROM Pet WHERE owner = po.person)
WHERE po.person = 1;
