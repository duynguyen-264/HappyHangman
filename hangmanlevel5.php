<?php

session_start();

$alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$GAME_WON = false;

// Live variables here

// All the body parts
$bodyPartsList = ["nohead", "head", "body", "hand", "hands", "leg", "legs"];

// Random words for the game and you to guess
$wordList = [
    "SERPENT", "TAMARIND", "BIOME", "ARBOREAL", "TOUCAN",
    "GIBBON", "DRAGON"
];

function fetchCurrentImage($part)
{
    return "hangman1_" . $part . ".jpg";
}

function startNewGame()
{
}

// restart the game. Clear the session variables
function resetGame()
{
    session_destroy();
    session_start();
}

// Get all the hangman Parts
function fetchParts()
{
    global $bodyPartsList;
    return isset($_SESSION["parts"]) ? $_SESSION["parts"] : $bodyPartsList;
}

// add part to the Hangman
function addBodyPart()
{
    $partsList = fetchParts();
    array_shift($partsList);
    $_SESSION["parts"] = $partsList;
}

// get Current Hangman Body part
function getCurrentBodyPart()
{
    $partsList = fetchParts();
    return $partsList[0];
}

// get the current words
function getCurrentGameWord()
{
    global $wordList;
    if (!isset($_SESSION["word"]) && empty($_SESSION["word"])) {
        $randomKey = array_rand($wordList);
        $_SESSION["word"] = $wordList[$randomKey];
    }
    return $_SESSION["word"];
}

// user responses logic

// get user response
function fetchUserResponses()
{
    return isset($_SESSION["responses"]) ? $_SESSION["responses"] : [];
}

function addUserResponse($letter)
{
    $responsesList = fetchUserResponses();
    array_push($responsesList, $letter);
    $_SESSION["responses"] = $responsesList;
}

// check if pressed letter is correct
function isLetterGuessedCorrectly($letter)
{
    $word = getCurrentGameWord();
    $max = strlen($word) - 1;
    for ($i = 0; $i <= $max; $i++) {
        if ($letter == $word[$i]) {
            return true;
        }
    }
    return false;
}

// is the word (guess) correct

function isGameWordCorrect()
{
    $currentGuess = getCurrentGameWord();
    $responsesList = fetchUserResponses();
    $max = strlen($currentGuess) - 1;
    for ($i = 0; $i <= $max; $i++) {
        if (!in_array($currentGuess[$i],  $responsesList)) {
            return false;
        }
    }
    return true;
}

// check if the body is ready to hang

function isBodyFullyComplete()
{
    $partsList = fetchParts();
    // is the current parts less than or equal to one
    if (count($partsList) <= 1) {
        return true;
    }
    return false;
}

// manage game session

// is game complete
function isGameComplete()
{
    return isset($_SESSION["gamecomplete"]) ? $_SESSION["gamecomplete"] : false;
}

// set game as complete
function setGameAsComplete()
{
    $_SESSION["gamecomplete"] = true;
}

// start a new game
function setGameAsNew()
{
    $_SESSION["gamecomplete"] = false;
}

/* Detect when the game is to restart. From the restart button press*/
if (isset($_GET['start'])) {
    resetGame();
}

/* Detect when Key is pressed */
if (isset($_GET['kp'])) {
    $currentPressedKey = isset($_GET['kp']) ? $_GET['kp'] : null;

    // Track all guesses (correct and incorrect)
    addUserResponse($currentPressedKey);

    // Check if the pressed key is correct
    if ($currentPressedKey && isLetterGuessedCorrectly($currentPressedKey) && !isBodyFullyComplete() && !isGameComplete()) {
        if (isGameWordCorrect()) {
            $GAME_WON = true; // game complete
            setGameAsComplete();
        }
    } else {
        // Start hanging the man :)
        if (!isBodyFullyComplete()) {
            addBodyPart();
            if (isBodyFullyComplete()) {
                setGameAsComplete(); // lost condition
            }
        } else {
            setGameAsComplete(); // lost condition
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Happy Hangman: Level 5</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1 class = "text1">Level 5</h1>
    <!-- Main app display -->
    <div class="container">

        <!-- Display the image here -->
        <div class="image-container">
            <img src="<?php echo fetchCurrentImage(getCurrentBodyPart()); ?>" />
        </div>

        <!-- Display the status messages (win and lose messages) to the right of the image container -->
        <div class="status-container">
            <?Php if (isGameComplete()) : ?>
                <h1 class = "text1">GAME COMPLETE</h1>
            <?php endif; ?>
            <?php if ($GAME_WON && isGameComplete()) : ?>
                <p style="color: darkgreen; font-size: 25px; text-align: center;">CONGRATS!</p>
				<div style="text-align:center;">
					<button onclick="window.location.href='hangmanlevel6.php'">Level 6</button><br>
				</div>	
            <?php elseif (!$GAME_WON && isGameComplete()) : ?>
                <p style="color: darkred; font-size: 25px; text-align: center;">GAME OVER</p>
            <?php endif; ?>
        </div>

        <div class="guess-container">
            <!-- Display the current guesses -->
            <p>Your Guesses: <?php echo implode(", ", fetchUserResponses()); ?></p>
            <?php
            $currentGuess = getCurrentGameWord();
            $maxLettersInCurrentGuess = strlen($currentGuess) - 1;
            for ($j = 0; $j <= $maxLettersInCurrentGuess; $j++) : $letter = getCurrentGameWord()[$j]; ?>
                <?php if (in_array($letter, fetchUserResponses())) : ?>
                    <span><?php echo $letter; ?></span>
                <?php else : ?>
                    <span>&nbsp;&nbsp;&nbsp;</span>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
		
		<br></br>
        <div class="button-container" style="text-align: center;">
            <form method="get">
                <?php
                $max = strlen($alphabet) - 1;
                for ($i = 0; $i <= $max; $i++) {
                    echo "<button type='submit' name='kp' value='" . $alphabet[$i] . "'>" .
                        $alphabet[$i] . "</button>";
                    if ($i % 7 == 0 && $i > 0) {
                        echo '<br>';
                    }
                }
                ?>
                <br><br>
                <!-- Restart game button -->
                <button type="submit" name="start">Restart Game</button>
            </form>
        </div>
		
		
		<div class = "button-home">
			<button onclick="window.location.href='homepage.html'">Choose Another Level</button><br>
		</div>
		
    </div>
</body>

</html>