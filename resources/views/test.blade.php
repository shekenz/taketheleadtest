<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Take the Lead - Test</title>
        @vite(['resources/css/app.css', 'resources/js/main.js'])
    </head>
    <body>
        <header>
            <h1>SuperComics</h1>
        </header>
        <section id="main">
            <div id="import-window" style="display: none" class="centered-content">
                <span>Importing comics from Marvel. This can take a while...</span>
                <div id="import-progress-wrapper">
                    <div id="import-progress" class="progress-bar-wrapper">
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                        <div class="progress-bar"></div>
                    </div>
                    <span id="import-progress-percentage">0%</span>
                </div>
            </div>
            <div id="comic-collection" style="display: none">
                <div id="empty-collection" class="centered-content" style="display: none">
                    <span>No comics found in collection.</span>
                    <button id="import-button">Import from Marvel</button>
                </div>
                    <div id="comic-collection-wrapper">
                    </div>
            </div>
        </section>
        <footer>
            Data provided by Marvel. © 2024 Marvel
        <footer>
    </body>
</html>
