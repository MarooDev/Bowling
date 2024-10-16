document.addEventListener("DOMContentLoaded", () => {
    const rollForm = document.getElementById("rollForm");
    const currentScore = document.getElementById("currentScore");
    const framesContainer = document.getElementById("frames");
    const gameOverMessage = document.getElementById("gameOverMessage");
    const resetButton = document.getElementById("resetButton");

    rollForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        const formData = new FormData(rollForm);
        const pins = formData.get("pins");

        try {
            const response = await fetch("index.php", {
                method: "POST",
                body: formData,
            });

            if (response.ok) {
                const data = await response.json();
                if (data.error) {
                    alert(data.error);
                } else {
                    currentScore.textContent = `Aktualny wynik: ${data.score}`;
                    framesContainer.innerHTML = '';
                    if (data.frames) {
                        data.frames.forEach(({ frameNumber, rolls }) => {
                            const frameDiv = document.createElement('div');
                            frameDiv.classList.add('frame');
                            frameDiv.textContent = `Ramka ${frameNumber}: ${rolls.length > 0 ? rolls.join(', ') : 'Brak rzutów'}`;
                            framesContainer.appendChild(frameDiv);
                        });
                    }

                    if (data.isGameOver) {
                        gameOverMessage.style.display = 'block';
                        rollForm.querySelector('button').disabled = true;
                        resetButton.style.display = 'block';
                    }
                }
            }
        } catch (error) {
            console.error("Error updating score:", error);
        }
    });

    resetButton.addEventListener("click", async () => {
        try {
            const response = await fetch("index.php?action=reset", {
                method: "GET",
            });

            if (response.ok) {
                const data = await response.json();
                if (data.message) {
                    // Reset game UI
                    currentScore.textContent = "Aktualny wynik: 0";
                    framesContainer.innerHTML = '';
                    gameOverMessage.style.display = 'none';
                    rollForm.querySelector('button').disabled = false;
                    resetButton.style.display = 'none';
                }
            }
        } catch (error) {
            console.error("Error resetting game:", error);
        }
    });
});