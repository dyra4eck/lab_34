<?php
include 'connect.php';
$pageTitle = 'Результаты поиска';
include 'header.php';

// Получение параметров поиска
$origin = $_GET['from'] ?? '';
$destination = $_GET['to'] ?? '';
$departure_date = $_GET['departure'] ?? '';
$return_date = $_GET['return'] ?? '';
$passengers = (int)($_GET['passengers'] ?? 1);
?>
<div class="main-container">
    <div class="left-column">
        <?php include 'menu.php'; ?>
    </div>

    <div id="content">
        <h2 class="section-title">Поиск авиабилетов</h2>
        
        <form class="search-form" method="GET">
            <input type="text" name="from" placeholder="Откуда" 
                   value="<?= htmlspecialchars($origin) ?>" required>
            <input type="text" name="to" placeholder="Куда" 
                   value="<?= htmlspecialchars($destination) ?>" required>
            <input type="date" name="departure" 
                   value="<?= htmlspecialchars($departure_date) ?>" required>
            <input type="date" name="return" 
                   value="<?= htmlspecialchars($return_date) ?>">
            <select name="passengers">
                <option value="1" <?= $passengers === 1 ? 'selected' : '' ?>>1 пассажир</option>
                <option value="2" <?= $passengers === 2 ? 'selected' : '' ?>>2 пассажира</option>
            </select>
            <button type="submit">Найти билеты</button>
        </form>

        <?php if(!empty($_GET)): 
            $sql = "SELECT 
                        p.flight_instance_id,
                        r.flight_id,
                        r.origin,
                        r.destination,
                        r.departure_datetime,
                        r.arrival_datetime,
                        a.name AS airline,
                        p.actual_departure,
                        p.actual_arrival,
                        COUNT(t.ticket_id) AS tickets_available,
                        MIN(t.price) AS min_price
                    FROM рейсы r
                    JOIN авиакомпании a ON r.airline_id = a.airline_id
                    JOIN полеты p ON r.flight_id = p.flight_id
                    LEFT JOIN билеты t ON p.flight_instance_id = t.flight_instance_id
                    WHERE r.origin = ?
                    AND r.destination = ?
                    AND DATE(r.departure_datetime) = ?
                    GROUP BY p.flight_instance_id
                    HAVING tickets_available >= ?";

            $stmt = $conn->prepare($sql);
            if (!$stmt) die("Ошибка подготовки запроса: " . $conn->error);
            
            $stmt->bind_param('sssi', $origin, $destination, $departure_date, $passengers);
            $stmt->execute();
            $result = $stmt->get_result();
        ?>
        
        <?php if($result->num_rows > 0): ?>
            <table class="flights-table">
                <thead>
                    <tr>
                        <th>Авиакомпания</th>
                        <th>Вылет</th>
                        <th>Прибытие</th>
                        <th>Откуда</th>
                        <th>Куда</th>
                        <th>Цена от</th>
                        <th>Доступно мест</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['airline']) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($row['departure_datetime'])) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($row['arrival_datetime'])) ?></td>
                        <td><?= htmlspecialchars($row['origin']) ?></td>
                        <td><?= htmlspecialchars($row['destination']) ?></td>
                        <td><?= number_format($row['min_price'], 0, '', ' ') ?> ₽</td>
                        <td><?= $row['tickets_available'] ?></td>
                        <td class="actions">
                            <a href="add_to_cart.php?flight_instance_id=<?= $row['flight_id'] ?>&passengers=<?= $passengers ?>" 
                            class="btn-book">Забронировать</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-results">По вашему запросу рейсов не найдено</div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>