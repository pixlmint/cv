document.addEventListener('DOMContentLoaded', function() {
    const whatAmIOptions = document.getElementById('whatAmIOptions');
    const target = document.getElementById('whatAmITarget');

    const typewriter = setupTypewriter(whatAmIOptions, target);
    typewriter.start();
});

/**
 * @param {HTMLUListElement} options
 * @param {Element} targetElement 
 */
function setupTypewriter(options, targetElement) {
    const texts = Object.values(options.getElementsByTagName('li')).map(el => el.innerText);
    console.log(texts);
    let cursorPosition = 0;
    const typeSpeed = 100;
    const typingDelay = 500;
    let tempTypeSpeed = 0;
    const backspaceSpeed = 40;
    const backspaceDelay = 1000;

    let currentText = 0;

    const start = function() {
        cursorPosition = 0;
        type(texts[currentText], targetElement).then(function() {
            currentText += 1;
            if (currentText === texts.length) {
                currentText = 0;
            } 
            window.setTimeout(start, typingDelay);
        });
    }

    const type = function(text, targetElement) {
        return new Promise(function (resolve, reject) {
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
                    setTimeout(backspace, backspaceDelay);
                }
            }

            const backspace = function() {
                targetElement.innerText = targetElement.innerText.substring(0, targetElement.innerText.length - 1);
                cursorPosition -= 1;

                if (cursorPosition > 0) {
                    setTimeout(backspace, backspaceSpeed);
                } else {
                    resolve();
                }
            }

            typeCharacter();
        });
    };

    return {
        type: type,
        start: start,
    };
}

/**
 * @param {HTMLUListElement} listElement
 * @param {HTMLElement} targetElement
 */
function typingAnimation(listElement, targetElement) {
    return new Promise(function(resolve, reject) {
        const options = listElement.getElementsByTagName('li');
        while (true) {
            for (let i = 0; i < options.length; i++) {
                /** @type {HTMLLIElement} el */
                const el = options[i];
                const letters = el.innerText.split('');
                let j = 0;
                let typingInterval = window.setInterval(function() {
                    targetElement.innerText += letters[j++];
                    if (j >= letters.length) {
                        typingInterval = window.setInterval(function() {
                            targetElement.innerText = targetElement.innerText.substring(0, targetElement.innerText.length - 1);
                            j--;
                            if (j === 0) {
                                window.clearInterval(typingInterval);
                            }
                        }, 100);
                    }
                }, 100);
                sleep(200 * letters.length);
            }
        }
    })
}

