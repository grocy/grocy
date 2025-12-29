<?php

namespace Grocy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EquipmentController extends BaseController
{
	protected $UserfieldsService;

	public function EditForm(Request $request, Response $response, array $args)
	{
                if ($args['equipmentId'] == 'new')
                {
                        return $this->renderPage($response, 'equipmentform', [
                                'mode' => 'create',
                                'userfields' => $this->getUserfieldsService()->GetFields('equipment'),
                                'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE')
                        ]);
                }
                else
                {
                        return $this->renderPage($response, 'equipmentform', [
                                'equipment' => $this->getDatabase()->equipment($args['equipmentId']),
                                'mode' => 'edit',
                                'userfields' => $this->getUserfieldsService()->GetFields('equipment'),
                                'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE')
                        ]);
                }
        }

        public function Overview(Request $request, Response $response, array $args)
        {
                return $this->renderPage($response, 'equipment', [
                        'equipment' => $this->getDatabase()->equipment()->orderBy('name', 'COLLATE NOCASE'),
                        'userfields' => $this->getUserfieldsService()->GetFields('equipment'),
                        'userfieldValues' => $this->getUserfieldsService()->GetAllValues('equipment'),
                        'locations' => $this->getDatabase()->locations()->orderBy('name', 'COLLATE NOCASE')
                ]);
        }
}
