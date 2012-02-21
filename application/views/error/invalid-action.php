<h1>404 - Invalid action "<?php echo $this->dispatchToken->getControllerClassName() ?>::<?php echo $this->dispatchToken->getActionMethodName() ?>()" requested</h1>
<?php Debug::dump($this->request, 'Request'); ?>
<?php Debug::dump($this->dispatchToken, 'Dispatch token'); ?>