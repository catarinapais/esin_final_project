<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head> 
        <title> Registration Page</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!--define this for responsive design-->
        <link href="css/style.css" rel="stylesheet">
        <link href="css/layout.css" rel="stylesheet">
        <link href="css/responsive.css" rel="stylesheet">
    </head> 
    <body> 
        <?php
        include('header.php');
        ?>


        <main id="FAQcontent">
            <h1>Frequently Asked Questions (FAQs)</h1>

            <section id="faq-section">
                <div class="faq-item">
                    <h3>1. What services do you offer?</h3>
                    <p> We offer two main services for pet care: <br>
                        Pet Walking: Regular walks for dogs, ensuring exercise and socialization. <br>
                        Pet Sitting: In-home care for your pet, including feeding, playtime, and companionship.
                    </p>
                </div>
    
                <div class="faq-item">
                    <h3>2. How do I book a service?</h3>
                    <p>You can book a service directly through our website by clicking on "Book a Service" or by contacting us via phone or email. We will confirm availability and assist with all the details.</p>
                </div>
    
                <div class="faq-item">
                    <h3>3. What are the available hours for pet walking?</h3>
                    <p>Our pet walking services are available Monday to Friday, from 6 AM to 10 PM. If you need services outside of these hours, please contact us, and we'll check availability.</p>
                </div>
    
                <div class="faq-item">
                    <h3>4. What are the requirements for Pet Sitting?</h3>
                    <p>For Pet Sitting, your pet should be comfortable in their own home with a set feeding and care routine. We also ask for details about any medications, allergies, or special needs your pet may have.</p>
                </div>
    
                <div class="faq-item">
                    <h3>5. How can I check on my pet during walks or pet sitting?</h3>
                    <p>During walks, you can request updates via text or pictures if you'd like. For Pet Sitting, we can provide regular check-ins with photos and updates on how your pet is doing.</p>
                </div>
    
                <div class="faq-item">
                    <h3>6. What happens if I need to cancel or change a booking?</h3>
                    <p> We have a flexible cancellation policy. If you need to cancel or change a booking, please inform us at least 24 hours in advance. If not, a cancellation fee may apply.</p>
                </div>
    
                <div class="faq-item">
                    <h3>7. Do you care for all types of animals?</h3>
                    <p> For now we only provide services for dogs and cats.</p>
                </div>
    
                <div class="faq-item">
                    <h3>8. Are the dog walks done individually or in groups?</h3>
                    <p> No, they are walked individually. We prioritize your pet’s safety and comfort at all times.</p>
                </div>
    
                <div class="faq-item">
                    <h3>9. Do you offer services for pets with special needs?</h3>
                    <p>Yes, we provide tailored care for pets with special needs, including those that require medication or have mobility issues. Our team is trained to handle a variety of conditions and ensure your pet’s safety and comfort.</p>
                </div>
    
                <div class="faq-item">
                    <h3>10. How do you ensure the safety of my pet?</h3>
                    <p> Your pet’s safety is our top priority. All of our caregivers are trained in first aid, and we have strict protocols in place to ensure the well-being of pets during walks or pet sitting. We also maintain constant communication with owners to keep them updated.</p>
                </div>
    
                <div class="faq-item">
                    <h3>11. How do I pay for services?</h3>
                    <p>At the moment we only accept payments by bank tranfer. Payment is due at the time of booking confirmation.</p>
                </div>
    
            
    
                <div class="faq-item">
                    <h3>12. How do I know if my pet will adapt to the service?</h3>
                    <p> We offer an initial consultation to understand your pet’s needs and temperament, ensuring that the service is a good fit. If your pet requires an adjustment period, we are prepared to work with them to ensure they feel comfortable and safe.</p>
                </div>
    
                <div class="faq-item">
                    <h3>13. Do you offer insurance for animals during services?</h3>
                    <p>Yes, all pets using our services are covered by insurance during the service period, ensuring that any emergencies are handled swiftly and efficiently.</p>
                </div>
    
                <div class="faq-item">
                    <h3>14. What should I do if my pet feels unwell during a walk or pet sitting?</h3>
                    <p>In case of any medical emergency, our team is trained to handle the situation and will seek veterinary care immediately if necessary. We always ask for detailed health information about your pet before starting the service to ensure we are prepared for any situation.</p>
                </div>
            </section>
        </main>
            
        <?php include('footer.php'); ?>

    </body>
    </html>

   