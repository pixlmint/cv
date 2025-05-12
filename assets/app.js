document.addEventListener('DOMContentLoaded', function() {
    const whatAmIOptions = document.getElementById('whatAmIOptions');
    const target = document.getElementById('whatAmITarget');

    const typewriter = setupTypewriter(whatAmIOptions, target);
    window.setTimeout(typewriter.start, 2000);
});

/**
 * @param {HTMLUListElement} options
 * @param {Element} targetElement 
 */
function setupTypewriter(options, targetElement) {
    const texts = Object.values(options.getElementsByTagName('li')).map(el => el.innerText);
    let cursorPosition = 0;
    const typeSpeed = 100;
    const typingDelay = 500;
    let tempTypeSpeed = 0;
    const backspaceSpeed = 40;
    const backspaceDelay = 1000;

    let isPaused = false;
    let pausedBeforeBackspace = false;
    let pauseBlocked = false;

    let currentText = 1;

    const start = function() {
        cursorPosition = texts[0].length;
        initialErase().then(() => window.setTimeout(loopTyping, typingDelay));
    }

    const loopTyping = function() {
        type(texts[currentText], targetElement).then(function() {
            if (isPaused && pausedBeforeBackspace) {
                return;
            }
            currentText += 1;
            if (currentText === texts.length) {
                currentText = 0;
            }
            if (!isPaused) {
                window.setTimeout(loopTyping, typingDelay);
            } else {
                pausedBeforeBackspace = false;
            }
        });
    }

    const initialErase = function() {
        return new Promise(function(resolve) {
            showCursor();
            backspace(resolve);
        });
    }

    const backspace = function(resolve) {
        targetElement.innerText = targetElement.innerText.substring(0, targetElement.innerText.length - 1);
        cursorPosition -= 1;

        if (cursorPosition > 0) {
            setTimeout(() => backspace(resolve), backspaceSpeed);
        } else {
            hideCursor();
            resolve();
        }
    }

    const type = async function(text, targetElement) {
        return new Promise(function(resolve) {
            tempTypeSpeed = (Math.random() * typeSpeed) + 50;

            const typeCharacter = function() {
                if (text[cursorPosition] === ' ') {
                    targetElement.innerText += " " + text[cursorPosition + 1];
                    cursorPosition++;
                } else {
                    targetElement.innerText += text[cursorPosition];
                }

                cursorPosition += 1;

                if (cursorPosition < text.length) {
                    setTimeout(() => typeCharacter(text, targetElement), tempTypeSpeed);
                } else {
                    if (isPaused) {
                        pausedBeforeBackspace = true;
                        resolve();
                    } else {
                        setTimeout(() => {
                            backspace(resolve);
                        }, backspaceDelay);
                    }
                }
            }

            showCursor();
            typeCharacter();
        });
    };

    const showCursor = function() {
        targetElement.classList.add('cursor-showing');
    }

    const hideCursor = function() {
        targetElement.classList.remove('cursor-showing');
    }

    const togglePause = function() {
        if (!pauseBlocked) {
            console.log("pause state toggled");
            pauseBlocked = true;
            if (isPaused) {
                isPaused = false;
                if (pausedBeforeBackspace) {
                    initialErase().then(function() {
                        loopTyping();
                    });
                } else {
                    loopTyping();
                }
            } else {
                isPaused = true;
            }
        }
    }

    targetElement.parentNode.addEventListener('click', togglePause);

    return {
        type: type,
        start: start,
        togglePause: togglePause,
    };
}

