<div class="vehicle_statistics">
    <h2><?php echo $vehicle->getName(); ?></h2>
    <div class="data overall_cost">
        <div class="description">Overall Cost</div>
        <div class="value"><?php echo $vehicle->getOverallCost(); ?></div>
        <div class="unit"> CHF</div>
    </div>

    <div class="data traveled_distance">
        <div class="description">Traveled distance</div>
        <div class="value"><?php echo $vehicle->getTraveledDistance(); ?></div>
        <div class="unit"> km</div>
    </div>

    <div class="data cost_per_km">
        <div class="description">Cost per kilometer</div>
        <div class="value"><?php echo $vehicle->getCostPerKm(); ?></div>
        <div class="unit"> CHF/km</div>
    </div>

    <div class="data fuel_consumption">
        <div class="description">Fuel consumption</div>
        <div class="value"><?php echo $vehicle->getAverageConsumption(); ?></div>
        <div class="unit"> l/100km</div>
    </div>
    <div class="end_float">&nbsp;</div>
</div>