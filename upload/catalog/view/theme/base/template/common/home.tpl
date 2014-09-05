<?php echo $header?>
<div class="container container-home">
    <div class="row">
        <div class="col-md-8 col-lg-9 col-sm-8">
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->getchild('app/widgetsimplesidebar', array('type' => 'slider'))?>
                </div>
            </div>
            <h3><?php echo Sumo\Language::getVar('SUMO_NOUN_LATEST_PRODUCTS')?></h3>
            <div class="product-container">
                <?php echo $this->getchild('app/widgetsimpleproduct', array('limit' => 6, 'type' => 'latest'))?>
            </div>

            <?php echo $this->getchild('app/widgetsimpleproduct', array('limit' => 6, 'type' => 'manufacturers', 'class' => 'product-container', 'title' => Sumo\Language::getVar('SUMO_NOUN_MANUFACTURER_PLURAL')))?>
        </div>
        <div class="col-md-4 col-lg-3 hidden-xs col-sm-4">
            <div class="sidebar sidebar-home">
                <?php echo $this->getchild('app/widgetsimplesidebar', array('type' => 'usp', 'location' => 'home'))?>
            </div>
            <?php echo $this->getchild('app/widgetsimplesidebar', array('type' => 'banner', 'location' => 'home', 'number' => 1))?>
            <?php echo $this->getchild('app/widgetsimplesidebar', array('type' => 'newsletter', 'location' => 'home'))?>
            <?php echo $this->getchild('app/widgetsimplesidebar', array('type' => 'banner', 'location' => 'home', 'number' => 2))?>
        </div>
    </div>
</div>
<?php echo $footer?>
