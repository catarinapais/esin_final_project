<?php
session_start();

$provider = $_GET['provider']; // logged in user
$owner = $_GET['owner']; // who the logged in user wants to chat with
$id = $_SESSION['id']; // logged in user

if ($id == $owner) {
    $recipient = $provider;
} else {
    $recipient = $owner;
}

// check if the logged in user is the owner or the provider
function displayMessages($messages) {
    // make user_id a global variable
    global $id;
    if (empty($messages)) {
        echo '<p>There are no messages to display.</p>';
    } else {
        echo '<div id="chatMessages">';
        foreach ($messages as $message) {
            $messageContent = $message['message_body'];
            $messageTime = $message['send_time'];
            $messageOwner = $message['sender'];
            if ($messageOwner == $id) {
                // This block is for the logged-in user's sent messages
                echo '<div class="senderMessage">
                        <p class="messageContent">' . $messageContent . '</p>
                        <p class="messageTime">' . $messageTime . '</p>
                    </div>';
            } else {
                // This block is for messages received from others
                echo '<div class="recipientMessage">
                        <p class="messageContent">' . $messageContent . '</p>
                        <p class="messageTime">' . $messageTime . '</p>
                    </div>';
            }
        }
        echo '</div>';
    }
}


require_once('../database/init.php');

try { // try catch for error handling
    $stmt = $dbh->prepare('SELECT * FROM Message WHERE 
    (owner = ? AND provider = ?) OR
    (owner = ? AND provider = ?)
    ORDER BY send_time DESC;'); // querying all messages between these two homies
    $stmt->execute(array($owner,$provider,$provider,$owner));
    $messages = $stmt->fetchAll(); //fetching all messages

    $stmt = $dbh->prepare(
        'SELECT name 
        FROM Person 
        WHERE id = :id;'
    ); // querying all messages between these two homies
    $stmt->execute([':id' => $recipient]);
    $name_recipient = $stmt->fetchAll(); //fetching name
} catch (Exception $e) {
    $error_msg = $e->getMessage(); // ir buscar a mensagem de erro e guardar em $error_msg
}
?>


<?php
    include('../templates/header_tpl.php');
    ?>
    <main id="main"> 
        <?php if($id == $provider) : ?>
            <a class="back-button" href="serviceProvider.php">&lt; Back</a>
        <?php else : ?>
            <a class="back-button" href="bookingRequest.php">&lt; Back</a>
        <?php endif; ?>
        <h1>Chat Service</h1>
        <section id="chatBox">
            <div id="recipientHeader">
                <h2 class="recipient">
                    <?= $name_recipient[0]['name']; ?>
                </h2>
            </div>
            <div id="chatMessages">
                <?php displayMessages($messages); ?>
            </div>
            <form id="messageForm" method="post" action="../actions/action_messages.php">
                <input type="text" name="message_body" id="messageInput" placeholder="Type a message...">
                <input type="hidden" name="send_time" value="<?= date('Y-m-d H:i:s'); ?>">
                <input type="hidden" name="owner" value="<?= $owner; ?>">
                <input type="hidden" name="provider" value="<?= $provider; ?>">
                <input type="hidden" name="sender" value="<?= $id; ?>">
                <button type="submit" id="sendButton">&#128233;</button>
            </form>
        </section>
    </main>
    <?php include('../templates/footer_tpl.php'); ?>
