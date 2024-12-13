<?php
session_start();

function displayMessages($schedule)
{
    // TODO: ver tb o dia e ordenar as mensagens
    // acho que nao vai ser preciso ordenar, uma vez que sao inseridas por ordem (?) maybe
    // make user_id a global variable
    global $user1_id;
    global $user2_id;
    if (empty($schedule)) {
        echo '<p>There are no messages to display.</p>';
    } else {
        echo '<div id="chatMessages">';
    foreach ($schedule as $message) {
        $messageContent = $message['message_body'];
        $messageTime = $message['send_time'];
        $messageOwner = $message['sender'];
        if ($messageOwner == $user1_id) {
            echo '<div class="senderMessage">
                    <p class="messageContent">' . $messageContent . '</p>
                    <p class="messageTime">' . $messageTime . '</p>
                </div>';
        } else {
            echo '<div class="recipientMessage">
                    <p class="messageContent">' . $messageContent . '</p>
                    <p class="messageTime">' . $messageTime . '</p>
                </div>';
        }
    }
    echo '</div>';
    }
}

$dbh = new PDO('sqlite:database.db');
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); //association fetching
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling

try { // try catch for error handling
    $user1_id = 1; // considering this is the one logged in
    $user2_id = 2;
    $stmt = $dbh->prepare('SELECT * FROM Message WHERE 
    (owner = ? AND provider = ?) OR
    (owner = ? AND provider = ?);'); // querying all messages between these two homies
    $stmt->execute(array($user1_id,$user2_id,$user2_id,$user1_id));
    $schedule = $stmt->fetchAll(); //fetching all messages

    $stmt = $dbh->prepare('SELECT name FROM Person WHERE 
    id=?;'); // querying all messages between these two homies
    $stmt->execute(array($user2_id));
    $name = $stmt->fetchAll(); //fetching name
} catch (Exception $e) {
    $error_msg = $e->getMessage(); // ir buscar a mensagem de erro e guardar em $error_msg
}
?>


<?php
    include('templates/header_tpl.php');
    ?>
    <main id="main"> 
        <h1>Chat</h1>
        <section id="chatBox">
            <div id="recipientHeader">
                <h2 class="recipient">
                    <?php
                    echo $name[0]["name"]; ?>
                </h2>
            </div>
            <div id="chatMessages">
                <?php displayMessages($schedule); ?>
            </div>
            <form id="messageForm" method="post" action="actions/action_messages.php">
                <input type="text" id="messageInput" placeholder="Type a message...">
                <button type="submit" id="sendButton">&#128233;</button>
            </form>
        </section>
    </main>
    <?php include('templates/footer_tpl.php'); ?>
