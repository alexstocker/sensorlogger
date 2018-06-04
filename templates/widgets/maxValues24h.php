<?php $widget = $_['widget'];
var_dump($widget->getLog());
?>

<?php if($widget->getLog()) { ?>

    <?php if(is_array($widget->getLog()->getData()) && !empty($widget->getLog()->getData())) { ?>
        <?php foreach ($widget->getLog()->getData() as $dataLog) { ?>
            <h3><?php p($dataLog->getValue()); ?> <?php p($dataLog->getShort()); ?></h3>
            <small><?php p($dataLog->getDescription()); ?></small>
        <?php } ?>
    <?php } else { ?>
        <h3><?php p($widget->getLog()->getTemperature()); ?>°C</h3>
        <h3><?php p($widget->getLog()->getHumidity()); ?>% r.F.</h3>
    <?php } ?>
    <h3><?php p($widget->getLog()->getCreatedAt()); ?> UTC</h3>
<?php }?>
