<?php
include 'connect.php';
// Проверка активности сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Детали рейса';
include 'header.php';

if (!isset($_GET['flight_id']) || !is_numeric($_GET['flight_id'])) {
    die("Ошибка: неверный ID рейса");
}

$flight_id = (int)$_GET['flight_id'];

// Обновлённый SQL-запрос с данными из всех связанных таблиц
$sql = "SELECT 
            r.*,
            a.name AS airline_name,
            a.iata_code,
            fi.actual_departure,
            fi.actual_arrival,
            fi.status AS flight_status,
            COUNT(t.ticket_id) AS tickets_available,
            GROUP_CONCAT(t.seat SEPARATOR ', ') AS seats,
            MIN(t.price) AS min_price,
            MAX(t.price) AS max_price
        FROM рейсы r
        JOIN авиакомпании a ON r.airline_id = a.airline_id
        LEFT JOIN flight_instances fi ON r.flight_id = fi.flight_id
        LEFT JOIN билеты t ON fi.flight_instance_id = t.flight_instance_id
        WHERE r.flight_id = ?
        GROUP BY r.flight_id, fi.flight_instance_id";

$stmt = $conn->prepare($sql);
if (!$stmt) die("Ошибка подготовки запроса: " . $conn->error);

$stmt->bind_param('i', $flight_id);
if (!$stmt->execute()) die("Ошибка выполнения: " . $stmt->error);

$result = $stmt->get_result();
$flight = $result->fetch_assoc();

if (!$flight) {
    die("Рейс не найден, но ID корректен. Проверьте данные в БД.");
}
?>

<div class="main-container">
    <div class="left-column">
        <?php include 'menu.php'; ?>
        <div id="sidebar">
            <h2 class="section-title">Акции</h2>
            <p><strong>Спецпредложение:</strong> Бесплатная страховка</p>
        </div>
    </div>

    <div id="content">
        <h2 class="section-title">Рейс <?= $flight['flight_number'] ?? 'N/A' ?></h2>
        
        <div class="flight-details">
            <!-- Основная информация -->
            <div class="detail-row">
                <span class="detail-label">Авиакомпания:</span>
                <span class="detail-value"><?= htmlspecialchars($flight['airline_name']) ?> (<?= $flight['iata_code'] ?>)</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Маршрут:</span>
                <span class="detail-value">
                    <?= htmlspecialchars($flight['origin']) ?> → <?= htmlspecialchars($flight['destination']) ?>
                </span>
            </div>

            <!-- Плановое время -->
            <div class="detail-row">
                <span class="detail-label">Плановый вылет:</span>
                <span class="detail-value">
                    <?= date('d.m.Y H:i', strtotime($flight['departure_datetime'])) ?>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Плановое прибытие:</span>
                <span class="detail-value">
                    <?= date('d.m.Y H:i', strtotime($flight['arrival_datetime'])) ?>
                </span>
            </div>

            <!-- Фактическое время -->
            <div class="detail-row">
                <span class="detail-label">Фактический вылет:</span>
                <span class="detail-value">
                    <?= $flight['actual_departure'] ? date('d.m.Y H:i', strtotime($flight['actual_departure'])) : 'N/A' ?>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Фактическое прибытие:</span>
                <span class="detail-value">
                    <?= $flight['actual_arrival'] ? date('d.m.Y H:i', strtotime($flight['actual_arrival'])) : 'N/A' ?>
                </span>
            </div>

            <!-- Информация о билетах -->
            <div class="detail-row">
                <span class="detail-label">Доступные места:</span>
                <span class="detail-value"><?= $flight['seats'] ?? 'Нет данных' ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Диапазон цен:</span>
                <span class="detail-value">
                    <?= number_format($flight['min_price'], 2) ?> ₽ - <?= number_format($flight['max_price'], 2) ?> ₽
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Статус рейса:</span>
                <span class="detail-value status-<?= $flight['flight_status'] ?>">
                    <?= htmlspecialchars($flight['flight_status']) ?>
                </span>
            </div>
        </div>

        <a href="search.php" class="btn-back">← Назад к поиску</a>
    </div>
</div>

<?php include 'footer.php'; ?>