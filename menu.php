<div id="nav">
    <h2 class="section-title">Меню</h2>
    <nav class="main-menu">
        <a href="main_page.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'main_page.php' ? 'active' : '' ?>">Главная</a>
        <a href="search.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'search.php' ? 'active' : '' ?>">Поиск билетов</a>
        <a href="account.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'account.php' ? 'active' : '' ?>">Личный кабинет</a>
        
        <div class="user-section">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="user-greeting"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="cart.php" class="nav-link">Корзина</a>
                <a href="logout.php" class="nav-link">Выйти</a>
            <?php else: ?>
                <a href="register.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : '' ?>">Регистрация</a>
                <a href="login.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : '' ?>">Войти</a>
            <?php endif; ?>
        </div>
    </nav>
</div>