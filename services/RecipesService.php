<?php

namespace Grocy\Services;

class RecipesService extends BaseService
{
	public function GetRecipesFulfillment()
	{
		$sql = 'SELECT * from recipes_fulfillment';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetRecipesSumFulfillment()
	{
		$sql = 'SELECT * from recipes_fulfillment_sum';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}
}
