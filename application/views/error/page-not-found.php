<?php if (LS_DEBUG): ?>
	<h1>404 - Page not found!</h1>
	<?php Debug::dump($this->request, 'Request'); ?>
<?php else: ?>
	<div id="error-message"><?php echo Translator::get('error.page-not-found') ?></div>
	<?php echo Translator::get('error.page-not-found-info:html') ?>
<?php endif; ?>