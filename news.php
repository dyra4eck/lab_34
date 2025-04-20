<?php
session_start();
include 'connect.php';
$pageTitle = 'Новости авиации';
include 'header.php';
?>

<div class="main-container">
    <div class="left-column">
        <?php include 'menu.php'; ?>
    </div>

    <div id="content">
        <h2 class="section-title">Новости авиации</h2>
        
        <!-- Статья 1 -->
        <div class="news-article">
            <div class="article-header">
                <h3 onclick="toggleContent(1)">Новый маршрут Москва-Токио ▼</h3>
                <span class="news-date">15 октября 2023</span>
            </div>
            <div class="article-content" id="content-1">
                <p>Авиакомпания "SkyAir" запускает прямые рейсы из Москвы в Токио с 1 декабря 2023 года.</p>
                <ul>
                    <li><strong>Расписание:</strong> Вт, Чт, Сб — вылет в 08:00.</li>
                    <li><strong>Самолёт:</strong> Boeing 787 Dreamliner (эко-класс от 35 000 ₽).</li>
                    <li><strong>Услуги:</strong> Бесплатный Wi-Fi, 2 багажных места.</li>
                </ul>
                <img src="monkey-monkey-nails.gif" alt="Маршрут Москва-Токио" class="news-image">
            </div>
        </div>

        <!-- Статья 2 -->
        <div class="news-article">
            <div class="article-header">
                <h3 onclick="toggleContent(2)">Обновление парка самолетов ▼</h3>
                <span class="news-date">10 октября 2023</span>
            </div>
            <div class="article-content" id="content-2">
                <p>Компания "AeroFlot" представила 5 новых лайнеров Boeing 787 Dreamliner.</p>
                <ul>
                    <li><strong>Вместимость:</strong> 290 пассажиров (42 бизнес-класса).</li>
                    <li><strong>Особенности:</strong> Система подавления турбулентности, LED-подсветка.</li>
                    <li><strong>Маршруты:</strong> Лондон, Дубай, Нью-Йорк.</li>
                </ul>
                <div class="news-gallery">
                    <img src="monkey-monkeys.gif" alt="Салон">
                    <img src="monkey-cute.gif" alt="Кабина">
                </div>
            </div>
        </div>

        <!-- Статья 3 -->
        <div class="news-article">
            <div class="article-header">
                <h3 onclick="toggleContent(3)">Скидки на раннее бронирование ▼</h3>
                <span class="news-date">5 октября 2023</span>
            </div>
            <div class="article-content" id="content-3">
                <p>Забронируйте билеты до 31 января 2024 и получите скидку 20%.</p>
                <ul>
                    <li><strong>Условия:</strong> Оплата в течение 24 часов.</li>
                    <li><strong>Маршруты:</strong> Париж (от 18 000 ₽), Бангкок (от 27 000 ₽).</li>
                    <li><strong>Исключения:</strong> Не применяется к бизнес-классу.</li>
                </ul>
                <button class="booking-button" onclick="location.href='main_page.php'">Забронировать</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleContent(articleId) {
    const content = document.getElementById(`content-${articleId}`);
    const header = content.previousElementSibling.querySelector('h3');
    
    if (content.style.maxHeight) {
        content.style.maxHeight = null;
        header.innerHTML = header.innerHTML.replace('▲', '▼');
    } else {
        content.style.maxHeight = content.scrollHeight + "px";
        header.innerHTML = header.innerHTML.replace('▼', '▲');
    }
}
</script>

<?php include 'footer.php'; ?>