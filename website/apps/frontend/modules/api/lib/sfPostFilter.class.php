<?php
class sfPostFilter extends sfFilter
{
	public function execute($filterChain)
	{
		if ($this->isFirstCall()) {
			$context = $this->getContext();
			$request = $context->getRequest();
			if (!$request->isMethod('post')) {
				$actionEntry = $this->getContext()->getActionStack()->getLastEntry();
				return $actionEntry->getActionInstance()->forward('api','notPostError');
			}
		}
		$filterChain->execute();
	}
}