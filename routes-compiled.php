<?php return array (
  0 => 
  array (
    'GET' => 
    array (
      '/' => 'route0',
      '/about' => 'route1',
      '/barcodescannertesting' => 'route2',
      '/login' => 'route3',
      '/logout' => 'route5',
      '/userfields' => 'route6',
      '/userentities' => 'route8',
      '/users' => 'route12',
      '/stockoverview' => 'route14',
      '/stockentries' => 'route15',
      '/purchase' => 'route16',
      '/consume' => 'route17',
      '/transfer' => 'route18',
      '/inventory' => 'route19',
      '/products' => 'route21',
      '/stocksettings' => 'route23',
      '/locations' => 'route24',
      '/quantityunits' => 'route26',
      '/productgroups' => 'route29',
      '/stockjournal' => 'route31',
      '/locationcontentsheet' => 'route32',
      '/quantityunitpluraltesting' => 'route33',
      '/shoppinglocations' => 'route34',
      '/shoppinglist' => 'route36',
      '/shoppinglistsettings' => 'route39',
      '/recipes' => 'route40',
      '/mealplan' => 'route43',
      '/recipessettings' => 'route44',
      '/choresoverview' => 'route45',
      '/choretracking' => 'route46',
      '/choresjournal' => 'route47',
      '/chores' => 'route48',
      '/choressettings' => 'route50',
      '/batteriesoverview' => 'route51',
      '/batterytracking' => 'route52',
      '/batteriesjournal' => 'route53',
      '/batteries' => 'route54',
      '/batteriessettings' => 'route56',
      '/tasks' => 'route57',
      '/taskcategories' => 'route59',
      '/taskssettings' => 'route61',
      '/equipment' => 'route62',
      '/calendar' => 'route64',
      '/api' => 'route65',
      '/manageapikeys' => 'route66',
      '/manageapikeys/new' => 'route67',
      '/api/openapi/specification' => 'route68',
      '/api/system/info' => 'route69',
      '/api/system/db-changed-time' => 'route70',
      '/api/system/config' => 'route71',
      '/api/users' => 'route84',
      '/api/user/settings' => 'route88',
      '/api/stock' => 'route91',
      '/api/stock/volatile' => 'route94',
      '/api/recipes/fulfillment' => 'route122',
      '/api/chores' => 'route123',
      '/api/batteries' => 'route128',
      '/api/tasks' => 'route132',
      '/api/calendar/ical' => 'route135',
      '/api/calendar/ical/sharing-link' => 'route136',
    ),
    'POST' => 
    array (
      '/login' => 'route4',
      '/api/system/log-missing-localization' => 'route72',
      '/api/users' => 'route85',
      '/api/stock/shoppinglist/add-missing-products' => 'route115',
      '/api/stock/shoppinglist/clear' => 'route116',
      '/api/stock/shoppinglist/add-product' => 'route117',
      '/api/stock/shoppinglist/remove-product' => 'route118',
      '/api/chores/executions/calculate-next-assignments' => 'route127',
    ),
  ),
  1 => 
  array (
    'GET' => 
    array (
      0 => 
      array (
        'regex' => '~^(?|/userfield/([^/]+)|/userentity/([^/]+)()|/userobjects/([^/]+)()()|/userobject/([^/]+)/([^/]+)()()|/user/([^/]+)()()()()|/stockentry/([^/]+)()()()()()|/product/([^/]+)()()()()()()|/location/([^/]+)()()()()()()()|/quantityunit/([^/]+)()()()()()()()()|/quantityunitconversion/([^/]+)()()()()()()()()())$~',
        'routeMap' => 
        array (
          2 => 
          array (
            0 => 'route7',
            1 => 
            array (
              'userfieldId' => 'userfieldId',
            ),
          ),
          3 => 
          array (
            0 => 'route9',
            1 => 
            array (
              'userentityId' => 'userentityId',
            ),
          ),
          4 => 
          array (
            0 => 'route10',
            1 => 
            array (
              'userentityName' => 'userentityName',
            ),
          ),
          5 => 
          array (
            0 => 'route11',
            1 => 
            array (
              'userentityName' => 'userentityName',
              'userobjectId' => 'userobjectId',
            ),
          ),
          6 => 
          array (
            0 => 'route13',
            1 => 
            array (
              'userId' => 'userId',
            ),
          ),
          7 => 
          array (
            0 => 'route20',
            1 => 
            array (
              'entryId' => 'entryId',
            ),
          ),
          8 => 
          array (
            0 => 'route22',
            1 => 
            array (
              'productId' => 'productId',
            ),
          ),
          9 => 
          array (
            0 => 'route25',
            1 => 
            array (
              'locationId' => 'locationId',
            ),
          ),
          10 => 
          array (
            0 => 'route27',
            1 => 
            array (
              'quantityunitId' => 'quantityunitId',
            ),
          ),
          11 => 
          array (
            0 => 'route28',
            1 => 
            array (
              'quConversionId' => 'quConversionId',
            ),
          ),
        ),
      ),
      1 => 
      array (
        'regex' => '~^(?|/productgroup/([^/]+)|/shoppinglocation/([^/]+)()|/shoppinglistitem/([^/]+)()()|/shoppinglist/([^/]+)()()()|/recipe/([^/]+)()()()()|/recipe/([^/]+)/pos/([^/]+)()()()()|/chore/([^/]+)()()()()()()|/battery/([^/]+)()()()()()()()|/task/([^/]+)()()()()()()()()|/taskcategory/([^/]+)()()()()()()()()())$~',
        'routeMap' => 
        array (
          2 => 
          array (
            0 => 'route30',
            1 => 
            array (
              'productGroupId' => 'productGroupId',
            ),
          ),
          3 => 
          array (
            0 => 'route35',
            1 => 
            array (
              'shoppingLocationId' => 'shoppingLocationId',
            ),
          ),
          4 => 
          array (
            0 => 'route37',
            1 => 
            array (
              'itemId' => 'itemId',
            ),
          ),
          5 => 
          array (
            0 => 'route38',
            1 => 
            array (
              'listId' => 'listId',
            ),
          ),
          6 => 
          array (
            0 => 'route41',
            1 => 
            array (
              'recipeId' => 'recipeId',
            ),
          ),
          7 => 
          array (
            0 => 'route42',
            1 => 
            array (
              'recipeId' => 'recipeId',
              'recipePosId' => 'recipePosId',
            ),
          ),
          8 => 
          array (
            0 => 'route49',
            1 => 
            array (
              'choreId' => 'choreId',
            ),
          ),
          9 => 
          array (
            0 => 'route55',
            1 => 
            array (
              'batteryId' => 'batteryId',
            ),
          ),
          10 => 
          array (
            0 => 'route58',
            1 => 
            array (
              'taskId' => 'taskId',
            ),
          ),
          11 => 
          array (
            0 => 'route60',
            1 => 
            array (
              'categoryId' => 'categoryId',
            ),
          ),
        ),
      ),
      2 => 
      array (
        'regex' => '~^(?|/equipment/([^/]+)|/api/objects/([^/]+)()|/api/objects/([^/]+)/([^/]+)()|/api/objects/([^/]+)/search/([^/]+)()()|/api/userfields/([^/]+)/([^/]+)()()()|/api/files/([^/]+)/([^/]+)()()()()|/api/user/settings/([^/]+)()()()()()()|/api/stock/entry/([^/]+)()()()()()()()|/api/stock/products/([^/]+)()()()()()()()()|/api/stock/products/([^/]+)/entries()()()()()()()()())$~',
        'routeMap' => 
        array (
          2 => 
          array (
            0 => 'route63',
            1 => 
            array (
              'equipmentId' => 'equipmentId',
            ),
          ),
          3 => 
          array (
            0 => 'route73',
            1 => 
            array (
              'entity' => 'entity',
            ),
          ),
          4 => 
          array (
            0 => 'route74',
            1 => 
            array (
              'entity' => 'entity',
              'objectId' => 'objectId',
            ),
          ),
          5 => 
          array (
            0 => 'route75',
            1 => 
            array (
              'entity' => 'entity',
              'searchString' => 'searchString',
            ),
          ),
          6 => 
          array (
            0 => 'route79',
            1 => 
            array (
              'entity' => 'entity',
              'objectId' => 'objectId',
            ),
          ),
          7 => 
          array (
            0 => 'route82',
            1 => 
            array (
              'group' => 'group',
              'fileName' => 'fileName',
            ),
          ),
          8 => 
          array (
            0 => 'route89',
            1 => 
            array (
              'settingKey' => 'settingKey',
            ),
          ),
          9 => 
          array (
            0 => 'route92',
            1 => 
            array (
              'entryId' => 'entryId',
            ),
          ),
          10 => 
          array (
            0 => 'route95',
            1 => 
            array (
              'productId' => 'productId',
            ),
          ),
          11 => 
          array (
            0 => 'route96',
            1 => 
            array (
              'productId' => 'productId',
            ),
          ),
        ),
      ),
      3 => 
      array (
        'regex' => '~^(?|/api/stock/products/([^/]+)/locations|/api/stock/products/([^/]+)/price\\-history()|/api/stock/products/by\\-barcode/([^/]+)()()|/api/stock/bookings/([^/]+)()()()|/api/stock/transactions/([^/]+)()()()()|/api/stock/barcodes/external\\-lookup/([^/]+)()()()()()|/api/recipes/([^/]+)/fulfillment()()()()()()|/api/chores/([^/]+)()()()()()()()|/api/batteries/([^/]+)()()()()()()()())$~',
        'routeMap' => 
        array (
          2 => 
          array (
            0 => 'route97',
            1 => 
            array (
              'productId' => 'productId',
            ),
          ),
          3 => 
          array (
            0 => 'route98',
            1 => 
            array (
              'productId' => 'productId',
            ),
          ),
          4 => 
          array (
            0 => 'route104',
            1 => 
            array (
              'barcode' => 'barcode',
            ),
          ),
          5 => 
          array (
            0 => 'route110',
            1 => 
            array (
              'bookingId' => 'bookingId',
            ),
          ),
          6 => 
          array (
            0 => 'route112',
            1 => 
            array (
              'transactionId' => 'transactionId',
            ),
          ),
          7 => 
          array (
            0 => 'route114',
            1 => 
            array (
              'barcode' => 'barcode',
            ),
          ),
          8 => 
          array (
            0 => 'route120',
            1 => 
            array (
              'recipeId' => 'recipeId',
            ),
          ),
          9 => 
          array (
            0 => 'route124',
            1 => 
            array (
              'choreId' => 'choreId',
            ),
          ),
          10 => 
          array (
            0 => 'route129',
            1 => 
            array (
              'batteryId' => 'batteryId',
            ),
          ),
        ),
      ),
    ),
    'POST' => 
    array (
      0 => 
      array (
        'regex' => '~^(?|/api/objects/([^/]+)|/api/stock/products/([^/]+)/add()|/api/stock/products/([^/]+)/consume()()|/api/stock/products/([^/]+)/transfer()()()|/api/stock/products/([^/]+)/inventory()()()()|/api/stock/products/([^/]+)/open()()()()()|/api/stock/products/by\\-barcode/([^/]+)/add()()()()()()|/api/stock/products/by\\-barcode/([^/]+)/consume()()()()()()()|/api/stock/products/by\\-barcode/([^/]+)/transfer()()()()()()()()|/api/stock/products/by\\-barcode/([^/]+)/inventory()()()()()()()()()|/api/stock/products/by\\-barcode/([^/]+)/open()()()()()()()()()())$~',
        'routeMap' => 
        array (
          2 => 
          array (
            0 => 'route76',
            1 => 
            array (
              'entity' => 'entity',
            ),
          ),
          3 => 
          array (
            0 => 'route99',
            1 => 
            array (
              'productId' => 'productId',
            ),
          ),
          4 => 
          array (
            0 => 'route100',
            1 => 
            array (
              'productId' => 'productId',
            ),
          ),
          5 => 
          array (
            0 => 'route101',
            1 => 
            array (
              'productId' => 'productId',
            ),
          ),
          6 => 
          array (
            0 => 'route102',
            1 => 
            array (
              'productId' => 'productId',
            ),
          ),
          7 => 
          array (
            0 => 'route103',
            1 => 
            array (
              'productId' => 'productId',
            ),
          ),
          8 => 
          array (
            0 => 'route105',
            1 => 
            array (
              'barcode' => 'barcode',
            ),
          ),
          9 => 
          array (
            0 => 'route106',
            1 => 
            array (
              'barcode' => 'barcode',
            ),
          ),
          10 => 
          array (
            0 => 'route107',
            1 => 
            array (
              'barcode' => 'barcode',
            ),
          ),
          11 => 
          array (
            0 => 'route108',
            1 => 
            array (
              'barcode' => 'barcode',
            ),
          ),
          12 => 
          array (
            0 => 'route109',
            1 => 
            array (
              'barcode' => 'barcode',
            ),
          ),
        ),
      ),
      1 => 
      array (
        'regex' => '~^(?|/api/stock/bookings/([^/]+)/undo|/api/stock/transactions/([^/]+)/undo()|/api/recipes/([^/]+)/add\\-not\\-fulfilled\\-products\\-to\\-shoppinglist()()|/api/recipes/([^/]+)/consume()()()|/api/chores/([^/]+)/execute()()()()|/api/chores/executions/([^/]+)/undo()()()()()|/api/batteries/([^/]+)/charge()()()()()()|/api/batteries/charge\\-cycles/([^/]+)/undo()()()()()()()|/api/tasks/([^/]+)/complete()()()()()()()()|/api/tasks/([^/]+)/undo()()()()()()()()())$~',
        'routeMap' => 
        array (
          2 => 
          array (
            0 => 'route111',
            1 => 
            array (
              'bookingId' => 'bookingId',
            ),
          ),
          3 => 
          array (
            0 => 'route113',
            1 => 
            array (
              'transactionId' => 'transactionId',
            ),
          ),
          4 => 
          array (
            0 => 'route119',
            1 => 
            array (
              'recipeId' => 'recipeId',
            ),
          ),
          5 => 
          array (
            0 => 'route121',
            1 => 
            array (
              'recipeId' => 'recipeId',
            ),
          ),
          6 => 
          array (
            0 => 'route125',
            1 => 
            array (
              'choreId' => 'choreId',
            ),
          ),
          7 => 
          array (
            0 => 'route126',
            1 => 
            array (
              'executionId' => 'executionId',
            ),
          ),
          8 => 
          array (
            0 => 'route130',
            1 => 
            array (
              'batteryId' => 'batteryId',
            ),
          ),
          9 => 
          array (
            0 => 'route131',
            1 => 
            array (
              'chargeCycleId' => 'chargeCycleId',
            ),
          ),
          10 => 
          array (
            0 => 'route133',
            1 => 
            array (
              'taskId' => 'taskId',
            ),
          ),
          11 => 
          array (
            0 => 'route134',
            1 => 
            array (
              'taskId' => 'taskId',
            ),
          ),
        ),
      ),
    ),
    'PUT' => 
    array (
      0 => 
      array (
        'regex' => '~^(?|/api/objects/([^/]+)/([^/]+)|/api/userfields/([^/]+)/([^/]+)()|/api/files/([^/]+)/([^/]+)()()|/api/users/([^/]+)()()()()|/api/user/settings/([^/]+)()()()()()|/api/stock/entry/([^/]+)()()()()()())$~',
        'routeMap' => 
        array (
          3 => 
          array (
            0 => 'route77',
            1 => 
            array (
              'entity' => 'entity',
              'objectId' => 'objectId',
            ),
          ),
          4 => 
          array (
            0 => 'route80',
            1 => 
            array (
              'entity' => 'entity',
              'objectId' => 'objectId',
            ),
          ),
          5 => 
          array (
            0 => 'route81',
            1 => 
            array (
              'group' => 'group',
              'fileName' => 'fileName',
            ),
          ),
          6 => 
          array (
            0 => 'route86',
            1 => 
            array (
              'userId' => 'userId',
            ),
          ),
          7 => 
          array (
            0 => 'route90',
            1 => 
            array (
              'settingKey' => 'settingKey',
            ),
          ),
          8 => 
          array (
            0 => 'route93',
            1 => 
            array (
              'entryId' => 'entryId',
            ),
          ),
        ),
      ),
    ),
    'DELETE' => 
    array (
      0 => 
      array (
        'regex' => '~^(?|/api/objects/([^/]+)/([^/]+)|/api/files/([^/]+)/([^/]+)()|/api/users/([^/]+)()()())$~',
        'routeMap' => 
        array (
          3 => 
          array (
            0 => 'route78',
            1 => 
            array (
              'entity' => 'entity',
              'objectId' => 'objectId',
            ),
          ),
          4 => 
          array (
            0 => 'route83',
            1 => 
            array (
              'group' => 'group',
              'fileName' => 'fileName',
            ),
          ),
          5 => 
          array (
            0 => 'route87',
            1 => 
            array (
              'userId' => 'userId',
            ),
          ),
        ),
      ),
    ),
    'OPTIONS' => 
    array (
      0 => 
      array (
        'regex' => '~^(?|/api/(.+))$~',
        'routeMap' => 
        array (
          2 => 
          array (
            0 => 'route137',
            1 => 
            array (
              'routes' => 'routes',
            ),
          ),
        ),
      ),
    ),
  ),
);