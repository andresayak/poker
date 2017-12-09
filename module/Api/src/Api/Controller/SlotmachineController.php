<?php

namespace Api\Controller;

use Api\Controller\AbstractController;

class SlotmachineController extends AbstractController
{

    public function getResultAction()
    {
        $authService = $this->getServiceLocator()->get('Auth\Service');
        $userRow = $authService->getUserRow();
        if ($userRow && $userRow->slot_attempt > 0) {
            $slotmachine = $this->getServiceLocator()->get('Slotmachine\Service');
            $result      = $slotmachine->getResult();
            $userRow->updateMoney($result['prize']);
            return $this->outOk($result);
        } else {
            return $this->outError('No more attempts.');
        }
    }

}
