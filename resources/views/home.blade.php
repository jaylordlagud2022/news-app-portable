<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News App</title>
</head>
<body>

<div id="app" data-home-url="{{ url('/') }}">
    <div class="search-container">
        <input type="text" id="searchInput" class="search-input" placeholder="Search for news...">
        <button onclick="searchNews()" class="search-button">Search</button>
    </div>
    <div class="load-more-container">
        <div id="loading" class="loading" style="display:none;">Loading...</div>
    </div>
    <div class="news-container" id="searchResults"></div>

    <div class="load-more-container">
        <button id="loadMore" onclick="loadMoreResults()" class="load-more-button" style="display:none;">Load More</button>
        <div id="loading" class="loading" style="display:none;">Loading...</div>
    </div>

    <div class="pinned-news-container">
        <h2>Pinned News</h2>
        <div id="pinnedNews" class="pinned-news"></div>
    </div>


</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const app = document.getElementById('app');
    const searchInput = document.getElementById('searchInput');
    const newsContainer = document.getElementById('searchResults');
    const pinnedNewsContainer = document.getElementById('pinnedNews');
    const loadMoreButton = document.getElementById('loadMore');
    const loadingDiv = document.getElementById('loading');

    let searchResults = [];
    let pinnedResults = JSON.parse(localStorage.getItem('pinnedResults')) || [];
    let page = 1;
    const pageSize = 10;
    const apiKey = 'YOUR_GUARDIAN_API_KEY';
    let hasMoreResults = true;

    function formatDate(dateString) {
        const date = new Date(dateString);
        return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
    }

    function renderNewsCards() {
        newsContainer.innerHTML = '';
        searchResults.forEach(result => {
            const newsCard = createNewsCard(result);
            newsContainer.appendChild(newsCard);
        });
    }

    function renderPinnedNews() {
        pinnedNewsContainer.innerHTML = '';
        pinnedResults.forEach(result => {
            const pinnedCard = createNewsCard(result, true);
            pinnedNewsContainer.appendChild(pinnedCard);
        });
    }

    function createNewsCard(result, isPinned = false) {
        const card = document.createElement('div');
        card.className = 'news-card';

        const details = document.createElement('div');
        details.className = 'news-details';

        const title = document.createElement('div');
        title.className = 'news-title';
        title.textContent = result.webTitle;

        const date = document.createElement('div');
        date.className = 'news-date';
        date.textContent = formatDate(result.webPublicationDate);

        const link = document.createElement('a');
        link.className = 'news-link';
        link.href = result.webUrl;
        link.target = '_blank';
        link.textContent = 'Read Article';

        const pinButton = document.createElement('button');
        pinButton.className = 'pin-button';
        pinButton.textContent = isPinned ? 'Unpin' : 'Pin';
        pinButton.onclick = function () {
            togglePin(result, isPinned);
        };

        details.appendChild(title);
        details.appendChild(date);
        details.appendChild(link);

        card.appendChild(details);
        card.appendChild(pinButton);

        return card;
    }

    function togglePin(result, isPinned) {
        if (!isPinned) {
            result.pinned = true;
            pinnedResults.push(result);
        } else {
            result.pinned = false;
            const index = pinnedResults.findIndex(item => item.id === result.id);
            if (index !== -1) {
                pinnedResults.splice(index, 1);
            }
        }

        localStorage.setItem('pinnedResults', JSON.stringify(pinnedResults));
        renderPinnedNews();
    }

    function fetchResults(query) {
        loadingDiv.style.display = 'block';
        var homeUrl = document.getElementById('app').getAttribute('data-home-url');

        const apiUrl = homeUrl+`/api/news/`;

        axios.post(apiUrl, { search: query, page: page })
            .then(response => {
                const newResults = response.data.response.results;
                searchResults = searchResults.concat(newResults);
                page++;

                if (newResults.length === 0) {
                    hasMoreResults = false;
                }

                renderNewsCards();
                loadMoreButton.style.display = hasMoreResults ? 'block' : 'none';
                loadingDiv.style.display = 'none';
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                loadingDiv.style.display = 'none';
            });
    }

    window.searchNews = function () {
        const searchTerm = searchInput.value.trim();
        if (searchTerm !== '') {
            searchResults = [];
            page = 1;
            hasMoreResults = true;
            fetchResults(searchTerm);
        }
    };

    window.loadMoreResults = function () {
        const searchTerm = searchInput.value.trim();
        if (searchTerm !== '') {
            fetchResults(searchTerm);
        }
    };

    // Initial render
    renderPinnedNews();
});
</script>
<style>
        /* Add your styles here */
        #app {
            max-width: 800px;
            margin: 0 auto;
            font-family: Arial, sans-serif;
        }

        .search-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-input {
            padding: 10px;
            width: 60%;
        }

        .search-button {
            padding: 10px;
            cursor: pointer;
        }

        .news-container {
            margin-bottom: 20px;
        }

        .news-card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .news-details {
            margin-bottom: 10px;
        }

        .news-title {
            font-size: 18px;
            font-weight: bold;
        }

        .news-date {
            font-style: italic;
        }

        .news-link {
            color: #007BFF;
            text-decoration: none;
        }

        .pin-button {
            padding: 5px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .pinned-news-container {
            margin-bottom: 20px;
        }

        .pinned-news {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .load-more-container {
            text-align: center;
        }

        .load-more-button {
            padding: 10px;
            cursor: pointer;
            display: none;
        }

        .loading {
            display: none;
        }
    </style>
</body>
</html>
