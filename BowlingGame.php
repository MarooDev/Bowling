<?php

declare(strict_types=1);

class BowlingGame {
    private array $frames = [];
    private ?Frame $currentFrame = null;
    private bool $isGameOver = false;

    public function __construct() {
        for ($i = 0; $i < 10; $i++) {
            $this->frames[] = new Frame();
        }
        $this->currentFrame = $this->frames[0];
    }

    public function roll(int $pins): void {
        if ($this->isGameOver) {
            throw new Exception("Gra jest już zakończona.");
        }

        if ($this->currentFrame !== null && !$this->currentFrame->isComplete()) {
            $this->currentFrame->addRoll($pins);
        }
        $this->updateCurrentFrame();

        if ($this->currentFrame === null) {
            $this->isGameOver = true;
        }
    }

    public function getScore(): int {
        $score = 0;
        foreach ($this->frames as $frame) {
            $score += $frame->getScore();
        }
        return $score;
    }

    public function getFrames(): array {
        return $this->frames;
    }

    public function isGameOver(): bool {
        return $this->isGameOver;
    }

    private function updateCurrentFrame(): void {
        if ($this->currentFrame !== null && $this->currentFrame->isComplete()) {
            $index = array_search($this->currentFrame, $this->frames, true);
            if ($index !== false && isset($this->frames[$index + 1])) {
                $this->currentFrame = $this->frames[$index + 1];
            } else {
                $this->currentFrame = null; // All frames completed
            }
        }
    }
}

class Frame {
    private array $rolls = [];
    private const MAX_PINS = 10;

    public function addRoll(int $pins): void {
        if (count($this->rolls) < 2 || $this->isStrike()) {
            $this->rolls[] = $pins;
        }
    }

    public function getScore(): int {
        return array_sum($this->rolls);
    }

    public function getRolls(): array {
        return $this->rolls;
    }

    public function isComplete(): bool {
        return $this->isStrike() || count($this->rolls) >= 2;
    }

    public function isStrike(): bool {
        return isset($this->rolls[0]) && $this->rolls[0] === self::MAX_PINS;
    }
}