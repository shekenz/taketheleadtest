let importProgress = document.getElementById('import-progress');
let importWindow = document.getElementById('import-window');
let comicCollection = document.getElementById('comic-collection');
let importProgressPercentage = document.getElementById('import-progress-percentage');

window.addEventListener('load', () => {
    // show(importWindow);
    fetch(window.location.origin + '/api/import-status')
        .then((r) => r.json())
        .then((j) => {
            if (j.importPending) {
                show(importWindow);
                observeProgress();
            } else {
                show(comicCollection);
            }
        });
});

function observeProgress() {
    let intervalId = window.setInterval(() => {
        fetch(window.location.origin + '/api/import-status')
        .then((r) => r.json())
        .then((j) => {
            console.log(j);
            if (j.progress === 100 && j.importPending === false) {
                clearInterval(intervalId);
                setProgressBar(importProgress, 100);
                setTimeout(() => {
                    hide(importWindow);
                    show(comicCollection);
                }, 1000);
            }
            setProgressBar(importProgress, j.progress);
        });
    }, 1000);
}

function setProgressBar(wrapper, progress) {
    let maxBars = wrapper.children.length;
    let doneBarsAmount = Math.round(progress / 100 * maxBars);

    for (let [index, bar] of Array.from(wrapper.children).entries()) {
        bar.classList.remove('progress-bar-done');
        if (index < doneBarsAmount) {
            bar.classList.add('progress-bar-done');
        }
    }

    importProgressPercentage.innerHTML = progress + '%';
}

function show(element) {
    element.style.display = '';
}

function hide(element) {
    element.style.display = 'none';
}
