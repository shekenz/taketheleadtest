let importProgress = document.getElementById('import-progress');
let importWindow = document.getElementById('import-window');
let comicCollection = document.getElementById('comic-collection');
let importProgressPercentage = document.getElementById('import-progress-percentage');
let importButton = document.getElementById('import-button');

window.addEventListener('load', () => {
    // show(importWindow);
    fetch(window.location.origin + '/api/import-status')
        .then((r) => r.json())
        .then((j) => {
            if (j.importPending) {
                show(importWindow);
                observeProgress();
            } else {
                loadCollection();
                show(comicCollection);
            }
        });
});

importButton.addEventListener('click', () => {
    hide(comicCollection);
    show(importWindow);
    setProgressBar(importProgress, 0);
    fetch(window.location.origin + '/api/import-marvel-data').then(() => { observeProgress(); });
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

function loadCollection() {
    fetch(window.location.origin + '/api/comics?page=1')
    .then((r) => r.json())
    .then((j) => console.log(j));
}

function show(element) {
    element.style.display = '';
}

function hide(element) {
    element.style.display = 'none';
}
