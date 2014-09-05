<?php if (isset($usp) && !empty($usp['href'])): ?>
<a href="<?php echo $usp['href']?>">
<?php endif ?>
<?php if (isset($usp) && !empty($usp['image'])): ?>
    <img src="image/<?php echo $usp['image']?>" alt="<?php if (isset($usp['title'][$this->config->get('language_id')])): echo $usp['title'][$this->config->get('language_id')]; endif; ?>">
<?php endif; ?>
<?php if (isset($usp) && !empty($usp['text'])): echo html_entity_decode($usp['text']); endif ?>
<?php if (isset($usp) && !empty($usp['href'])): ?>
</a>
<?php endif ?>
