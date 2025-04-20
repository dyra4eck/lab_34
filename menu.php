<div id="nav">
    <h2 class="section-title">Меню</h2>
    <nav class="main-menu">
        <a href="main_page.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'main_page.php' ? 'active' : '' ?>">Список билетов</a>
        <a href="about.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">О нас</a>
        <a href="search.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'search.php' ? 'active' : '' ?>">Поиск билетов</a>
        <a href="news.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : '' ?>">Новости</a>
        <a href="account.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'account.php' ? 'active' : '' ?>">Личный кабинет</a>
        
        <div class="user-section">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="user-greeting"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="cart.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : '' ?>">Корзина</a> <!-- Добавлена проверка -->
                <a href="logout.php" class="nav-link">Выйти</a>
            <?php else: ?>
                <a href="register.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : '' ?>">Регистрация</a>
                <a href="login.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : '' ?>">Войти</a>
            <?php endif; ?>
        </div>
    </nav>
</div>
<div id="sidebar">
    <h2 class="section-title">Акции и новости</h2>
    <a href="news.php" class="sidebar-news-link">
        <p><strong>Скидка 20%</strong><br>на рейсы в Европу</p>
    </a>
    <hr>
    <a href="news.php" class="sidebar-news-link">
        <p><strong>Новый маршрут</strong><br>Москва - Токио</p>
    </a>
    <hr>
    <a href="news.php" class="sidebar-news-link">
        <p><strong>Бонусные мили</strong><br>Для постоянных клиентов</p>
    </a>
</div>