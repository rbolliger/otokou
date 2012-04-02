<?php
class sfSslPostFilter extends sfFilter
{
	public function execute($filterChain)
	{
		if ($this->isFirstCall()) {
			$context = $this->getContext();
			$request = $context->getRequest();
			if (!$request->isSecure()) {
				$actionEntry = $this->getContext()->getActionStack()->getLastEntry();
				return $actionEntry->getActionInstance()->forward('api','notSecureError');
			}
			else {
				if (!$request->isMethod('post')) {
					$actionEntry = $this->getContext()->getActionStack()->getLastEntry();
					return $actionEntry->getActionInstance()->forward('api','notPostError');
				}
			}
			$filterChain->execute();
		}
		else {
			$filterChain->execute();
		}
	}
}