<?php
$pageTitle = 'О компании';
include 'header.php';
?>

<div class="main-container">
    <div class="left-column">
        <?php include 'menu.php'; ?>
    </div>

    <div id="content">
        <h2 class="section-title">О нашей компании</h2>
        
        <div class="about-image-container">
            <img src="plane.webp" alt="Наш самолет" class="about-plane-image">
        </div>
        
        <div class="about-content">
            <p>Мы — ведущий сервис по бронированию авиабилетов с 2010 года. Наша миссия — сделать ваши путешествия комфортными и доступными.</p>
            
            <h3>Наши преимущества:</h3>
            <ul>
                <li>Более 500 авиакомпаний-партнеров</li>
                <li>Круглосуточная поддержка</li>
                <li>Гарантия лучших цен</li>
            </ul>

            <h3>Контакты:</h3>
            <p>Телефон: +7 (999) 123-45-67<br>
            Email: info@avia-booking.ru<br>
            Адрес: Москва, ул. Авиационная, 15</p>
        </div>
            <!-- Полоса с наградами -->
        <div class="awards-section">
            <div class="awards-content">
                <div class="award-badge">
                    <img src="like.webp" class="award-icon" alt="Награда">
                    <div class="award-text">
                        <strong>Лучшая компания 2018</strong>
                        По версии города Воронеж
                    </div>
                </div>
                
                <div class="award-badge">
                    <img src="trophy.png" class="award-icon" alt="Сертификат">
                    <div class="award-text">
                        <strong>Топ-10 сервисов РФ</strong>
                        Рейтинг Forbes 2018
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>



<?php include 'footer.php'; ?>