const importProgress = document.getElementById('import-progress');
const importWindow = document.getElementById('import-window');
const comicCollection = document.getElementById('comic-collection');
const importProgressPercentage = document.getElementById('import-progress-percentage');
const importButton = document.getElementById('import-button');
const emptyCollection = document.getElementById('empty-collection');
const comicCollectionWrapper = document.getElementById('comic-collection-wrapper');
let collectionLoaded = false;

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
    const intervalId = window.setInterval(() => {
        fetch(window.location.origin + '/api/import-status')
        .then((r) => r.json())
        .then((j) => {
            console.log(j);
            if (j.progress === 100 && j.importPending === false) {
                clearInterval(intervalId);
                setProgressBar(importProgress, 100);
                setTimeout(() => {
                    hide(importWindow);
                    hide(emptyCollection);
                    show(comicCollection);
                    loadCollection();
                    collectionLoaded = true;
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
    if (!collectionLoaded) {
        fetch(window.location.origin + '/api/comics?page=1')
        .then((r) => r.json())
        .then((j) => {
            if (j.data.length > 0) {
                for (let comic of j.data) {
                    console.log(comic);
                    const comicElement = document.createElement('div');
                    comicElement.classList.add('comic-wrapper');
                    const comicThumbnail = document.createElement('img');
                    comicThumbnail.src = comic.thumbnail + '.jpg';
                    const comicTitle = document.createElement('h5');
                    comicTitle.append(comic.title);
                    comicElement.append(comicThumbnail);
                    comicElement.append(comicTitle);
                    comicCollectionWrapper.append(comicElement);
                }
            } else {
                show(emptyCollection);
            }
        });
    }
}

function show(element) {
    element.style.display = '';
}

function hide(element) {
    element.style.display = 'none';
}
