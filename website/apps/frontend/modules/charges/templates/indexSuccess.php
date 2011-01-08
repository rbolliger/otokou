<h1>Charges List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Vehicle</th>
      <th>User</th>
      <th>Category</th>
      <th>Date</th>
      <th>Kilometers</th>
      <th>Amount</th>
      <th>Comment</th>
      <th>Quantity</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($charges as $charge): ?>
    <tr>
      <td><a href="<?php echo url_for('charges_edit', $charge) ?>"><?php echo $charge->getId() ?></a></td>
      <td><?php echo $charge->getVehicleId() ?></td>
      <td><?php echo $charge->getUserId() ?></td>
      <td><?php echo $charge->getCategoryId() ?></td>
      <td><?php echo $charge->getDate() ?></td>
      <td><?php echo $charge->getKilometers() ?></td>
      <td><?php echo $charge->getAmount() ?></td>
      <td><?php echo $charge->getComment() ?></td>
      <td><?php echo $charge->getQuantity() ?></td>
      <td><?php echo $charge->getCreatedAt() ?></td>
      <td><?php echo $charge->getUpdatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('charges_new') ?>">New</a>
