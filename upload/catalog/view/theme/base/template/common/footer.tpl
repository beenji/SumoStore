
            </div>
            <div class="footer-container container">
                <?php echo $this->getChild('app/widgetsimplefooter')?>
                <?php if (defined('DEVELOPMENT')): ?>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <span class="btn btn-primary" id="debug_enabled"><a class=""><i class="fa fa-fighter-jet"></i><span class="bubble" id="debug"></span></a></span>
                        <br /><br >
                    </div>
                </div>
                <?php endif ?>
            </div>
        </div>

        <?php echo $google_analytics; ?>
    </body>
</html>
