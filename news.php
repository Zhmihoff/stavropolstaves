<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/logo_small.png">
    <title>Новости - ПАО "Ставропольэнергосбыт"</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <a href="index.html" class="logo">
                <img src="logo.svg" alt="Ставропольэнергосбыт" class="logo-icon">
            </a>
            <nav>
                <ul>
                    <li><a href="about.html">О нас</a></li>
                    <li><a href="news.php">Новости</a></li>
                    <li><a href="contacts.html">Контакты</a></li>
                    <li><a href="meter.php">Передача показаний</a></li>
                </ul>
            </nav>
            <div class="phone-link">
                <svg class="phone-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                <span>+7(962)494-44-92</span>
            </div>
        </div>
    </header>

    <section class="news-page-section">
        <div class="news-page-container">
            <div class="news-header">
                <h1>Новости</h1>
                <div class="news-filter">
                    <select id="sortSelect" onchange="sortNews()">
                        <option value="newest">От новых к старым</option>
                        <option value="oldest">От старых к новым</option>
                    </select>
                </div>
            </div>
            <div class="news-list" id="newsList">
                <div class="loading">Загрузка новостей...</div>
            </div>
        </div>
    </section>

    <div id="newsModal" class="news-modal">
        <div class="news-modal-content">
            <span class="news-modal-close" onclick="closeNewsModal()">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>

    <footer id="contacts">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Навигация</h3>
                <ul>
                    <li><a href="index.html">Главная</a></li>
                    <li><a href="about.html">О компании</a></li>
                    <li><a href="news.php">Новости</a></li>
                    <li><a href="contacts.html">Контакты</a></li>
                    <li><a href="meter.php">Передача показаний</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Акционерам и инвесторам</h3>
                <ul>
                    <li>ПАО "Ставропольэнергосбыт"</li>
                	<li> info@staves.ru</li>
                    <li>+7 (962) 494-66-92</li>
                    <li><a href="https://stavropolstaves.infinityfree.me/admin-panel.php" target="_blank">Администраторам</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 ПАО "Ставропольэнергосбыт"<br>Все права защищены</p>
        </div>
    </footer>

    <script>
        let allNews = [];
        let currentSort = 'newest';

        document.addEventListener('DOMContentLoaded', function() {
            loadNews();
        });

        function loadNews() {
            const newsList = document.getElementById('newsList');
            newsList.innerHTML = '<div class="loading">Загрузка новостей...</div>';
            
            fetch('api/get_news.php?sort=' + currentSort)
                .then(response => {
                    if (!response.ok) throw new Error('HTTP error ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        allNews = data.data;
                        displayNews(allNews);
                    } else {
                        newsList.innerHTML = '<div class="error">Ошибка: ' + (data.message || 'Неизвестная ошибка') + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    newsList.innerHTML = '<div class="error">Ошибка загрузки: ' + error.message + '</div>';
                });
        }

        function displayNews(news) {
            const newsList = document.getElementById('newsList');
            if (!news || news.length === 0) {
                newsList.innerHTML = '<div class="no-news">Новостей пока нет</div>';
                return;
            }
            let html = '';
            news.forEach(function(item) {
                html += '<div class="news-item" onclick="openNewsModal(' + item.id + ')" data-date="' + item.news_date_sort + '">' +
                    '<div class="news-item-image"><img src="img/news.png" alt="Новости"></div>' +
                    '<div class="news-item-content">' +
                        '<div class="news-item-date">' + escapeHtml(item.news_date_formatted) + '</div>' +
                        '<div class="news-item-title">' + escapeHtml(item.news_name) + '</div>' +
                    '</div></div>';
            });
            newsList.innerHTML = html;
        }

        function sortNews() {
            currentSort = document.getElementById('sortSelect').value;
            loadNews();
        }

        function openNewsModal(id) {
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = '<div class="loading">Загрузка...</div>';
            document.getElementById('newsModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            fetch('api/get_news_item.php?id=' + id)
                .then(response => {
                    if (!response.ok) throw new Error('HTTP error ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const news = data.data;
                        modalBody.innerHTML = 
                            '<h2>' + escapeHtml(news.news_name) + '</h2>' +
                            '<div class="modal-date">' + escapeHtml(news.news_date_formatted) + '</div>' +
                            '<div class="modal-content">' + news.news_description + '</div>';
                    } else {
                        modalBody.innerHTML = '<div class="error">Ошибка: ' + (data.message || 'Неизвестная ошибка') + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="error">Ошибка загрузки: ' + error.message + '</div>';
                });
        }

        function closeNewsModal() {
            document.getElementById('newsModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        window.onclick = function(event) {
            if (event.target === document.getElementById('newsModal')) closeNewsModal();
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') closeNewsModal();
        });
    </script>
</body>
</html>