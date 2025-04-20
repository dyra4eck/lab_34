<?php 
$pageTitle = 'Главная';
include 'connect.php';
include 'header.php'; 

// Получение параметров фильтрации
$selected_origin = $_GET['origin'] ?? '';
$selected_destination = $_GET['destination'] ?? '';
$selected_departure = $_GET['departure'] ?? '';
$selected_arrival = $_GET['arrival'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$passengers = (int)($_GET['passengers'] ?? 1);

// Базовый SQL-запрос
$sql = "SELECT 
            r.origin,
            r.destination,
            r.departure_datetime,
            r.arrival_datetime,
            a.name AS airline,
            MIN(t.price) AS min_price,
            COUNT(t.ticket_id) AS tickets_available,
            p.flight_instance_id
        FROM рейсы r
        JOIN авиакомпании a ON r.airline_id = a.airline_id
        JOIN полеты p ON r.flight_id = p.flight_id
        LEFT JOIN билеты t ON p.flight_instance_id = t.flight_instance_id
        WHERE 1=1";

$params = [];
$types = '';

// Условия фильтрации
if (!empty($selected_origin)) {
    $sql .= " AND r.origin = ?";
    $params[] = $selected_origin;
    $types .= 's';
}

if (!empty($selected_destination)) {
    $sql .= " AND r.destination = ?";
    $params[] = $selected_destination;
    $types .= 's';
}

if (!empty($selected_departure)) {
    $sql .= " AND DATE(r.departure_datetime) = ?";
    $params[] = $selected_departure;
    $types .= 's';
}

if (!empty($selected_arrival)) {
    $sql .= " AND DATE(r.arrival_datetime) = ?";
    $params[] = $selected_arrival;
    $types .= 's';
}

if (!empty($min_price)) {
    $sql .= " AND t.price >= ?";
    $params[] = $min_price;
    $types .= 'i';
}

if (!empty($max_price)) {
    $sql .= " AND t.price <= ?";
    $params[] = $max_price;
    $types .= 'i';
}

$sql .= " GROUP BY p.flight_instance_id";

// Выполнение запроса
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Получение данных для фильтров
$origins = $conn->query("SELECT DISTINCT origin FROM рейсы")->fetch_all(MYSQLI_ASSOC);
$destinations = $conn->query("SELECT DISTINCT destination FROM рейсы")->fetch_all(MYSQLI_ASSOC);
$dates = $conn->query("SELECT 
    DATE(departure_datetime) as departure_date,
    DATE(arrival_datetime) as arrival_date 
    FROM рейсы")->fetch_all(MYSQLI_ASSOC);
?>

<div class="main-container">
    <div class="left-column">
        <?php include 'menu.php'; ?>
    </div>

    <div id="content">
        <h2 class="section-title">Список авиабилетов</h2>
        
        <!-- Обновленная форма фильтров -->
        <form class="filter-form" method="GET">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Откуда:</label>
                    <select name="origin">
                        <option value="">Все</option>
                        <?php foreach($origins as $o): ?>
                            <option value="<?= $o['origin'] ?>" <?= $selected_origin == $o['origin'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($o['origin']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Куда:</label>
                    <select name="destination">
                        <option value="">Все</option>
                        <?php foreach($destinations as $d): ?>
                            <option value="<?= $d['destination'] ?>" <?= $selected_destination == $d['destination'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['destination']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="filter-row">
                <div class="filter-group">
                    <label>Дата вылета:</label>
                    <input type="date" name="departure" 
                           value="<?= htmlspecialchars($selected_departure) ?>">
                </div>

                <div class="filter-group">
                    <label>Дата прилета:</label>
                    <input type="date" name="arrival" 
                           value="<?= htmlspecialchars($selected_arrival) ?>">
                </div>

                <div class="filter-group">
                    <label>Пассажиры:</label>
                    <select name="passengers">
                        <option value="1" <?= $passengers === 1 ? 'selected' : '' ?>>1</option>
                        <option value="2" <?= $passengers === 2 ? 'selected' : '' ?>>2</option>
                    </select>
                </div>
            </div>

            <div class="filter-row">
                <div class="filter-group price-filter">
                    <label>Стоимость:</label>
                    <input type="number" name="min_price" placeholder="От" 
                           value="<?= htmlspecialchars($min_price) ?>" min="0">
                    <span>-</span>
                    <input type="number" name="max_price" placeholder="До" 
                           value="<?= htmlspecialchars($max_price) ?>" min="0">
                </div>
                <button type="submit" class="btn-filter">Применить</button>
            </div>
        </form>

        <!-- Список билетов -->
        <div class="tickets-list">
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="ticket-item">
                        <h3><?= htmlspecialchars($row['origin']) ?> → <?= htmlspecialchars($row['destination']) ?></h3>
                        <div class="ticket-details">
                            <p>Вылет: <?= date('d.m.Y H:i', strtotime($row['departure_datetime'])) ?></p>
                            <p>Прилет: <?= date('d.m.Y H:i', strtotime($row['arrival_datetime'])) ?></p>
                            <p>Авиакомпания: <?= htmlspecialchars($row['airline']) ?></p>
                            <p class="price"><?= number_format($row['min_price'], 0, '', ' ') ?> ₽</p>
                            <a href="add_to_cart.php?flight_instance_id=<?= $row['flight_instance_id'] ?>&passengers=<?= $passengers ?>" 
                               class="btn-book">Забронировать</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">Билетов не найдено</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.filter-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 25px;
}

.filter-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
    background: white;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

.filter-group label {
    font-weight: 500;
    white-space: nowrap;
}

.price-filter input {
    width: 100px;
    padding: 6px;
}

.btn-filter {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 5px;
    cursor: pointer;
    align-self: center;
}
</style>

<?php include 'footer.php'; ?>