<?php if (LS_DEBUG): ?>
	<h1>500 - Application error occured</h1>
	<?php Debug::dump($this->exception, 'Exception'); ?>
	<?php Debug::dump($this->request, 'Request'); ?>
	<?php Debug::dump($this->dispatchToken, 'Dispatch token'); ?>
<?php else: ?>
	<div id="error-message"><?php echo Translator::get('error.application-error') ?></div>
	<?php echo Translator::get('error.application-error-info:html') ?>
<?php endif; ?>
