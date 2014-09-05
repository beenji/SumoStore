			<div class="footer">
				<p>
					<a href="<?php echo $this->config->get('LANG_FOOTER_HOME_LINK')?>" target="_blank"><?php echo $this->config->get('LANG_FOOTER_HOME_NAME')?></a> |
					<a href="<?php echo $this->config->get('LANG_FOOTER_DOCS_LINK')?>" target="_blank"><?php echo $this->config->get('LANG_FOOTER_DOCS_NAME')?></a> |
					<a href="<?php echo $this->config->get('LANG_FOOTER_COMS_LINK')?>" target="_blank"><?php echo $this->config->get('LANG_FOOTER_COMS_NAME')?></a>
					<span class="pull-right">
						<?php if (isset($languages)): foreach ($languages as $lang): ?>
						<a href="?route=<?php echo isset($this->request->get['route']) ? $this->request->get['route'] : 'step_1'?>&amp;language=<?php echo $lang?>"><img src="/../image/flags/<?php echo $lang?>.png"></a>
						<?php endforeach; endif?>
					</span>
				</p>
				<p>Copyright &copy; 2013 - <?php echo date('Y')?> <a href="<?php echo $this->config->get('LANG_FOOTER_HOME_LINK')?>" target="_blank">SumoStore</a> - All rights reserved</p>
			</div>
		</div>
	</body>
</html>
