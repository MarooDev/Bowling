<?php
require_once 'BowlingGame.php';

session_start();

if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['game']);
    echo json_encode(['message' => 'Gra została zresetowana.']);
    exit;
}

if (!isset($_SESSION['game'])) {
    $_SESSION['game'] = new BowlingGame();
}

$game = $_SESSION['game'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pins = isset($_POST['pins']) ? intval($_POST['pins']) : 0;
    try {
        $game->roll($pins);
        echo json_encode([
            'score' => $game->getScore(),
            'frames' => array_map(function($frame, $index) {
                return [
                    'frameNumber' => $index + 1,
                    'rolls' => $frame->getRolls(),
                ];
            }, $game->getFrames(), array_keys($game->getFrames())),
            'isGameOver' => $game->isGameOver()
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}