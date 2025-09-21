<table>
    <thead>
        <tr>
            <th>Titre</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($carpools as $carpool): ?>
            <tr>
                <td><?php echo $carpool->departure_city ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>