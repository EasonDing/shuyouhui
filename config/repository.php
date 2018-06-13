<?php
/*
|--------------------------------------------------------------------------
| Prettus Repository Config
|--------------------------------------------------------------------------
|
|
*/
return [

    /*
    |--------------------------------------------------------------------------
    | Repository Pagination Limit Default
    |--------------------------------------------------------------------------
    |
    */
    'pagination' => [
        'limit' => 10
    ],

    /*
    |--------------------------------------------------------------------------
    | Fractal Presenter Config
    |--------------------------------------------------------------------------
    |

    Available serializers:
    ArraySerializer
    DataArraySerializer
    JsonApiSerializer

    */
    'fractal'    => [
        'params'     => [
            'include' => 'include'
        ],
        'serializer' => League\Fractal\Serializer\DataArraySerializer::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Config
    |--------------------------------------------------------------------------
    |
    */
    'cache'      => [
        /*
         |--------------------------------------------------------------------------
         | Cache Status
         |--------------------------------------------------------------------------
         |
         | Enable or disable cache
         |
         */
        'enabled'    => true,

        /*
         |--------------------------------------------------------------------------
         | Cache Minutes
         |--------------------------------------------------------------------------
         |
         | Time of expiration cache
         |
         */
        'minutes'    => 30,

        /*
         |--------------------------------------------------------------------------
         | Cache Repository
         |--------------------------------------------------------------------------
         |
         | Instance of Illuminate\Contracts\Cache\Repository
         |
         */
        'repository' => 'cache',

        /*
          |--------------------------------------------------------------------------
          | Cache Clean Listener
          |--------------------------------------------------------------------------
          |
          |
          |
          */
        'clean'      => [

            /*
              |--------------------------------------------------------------------------
              | Enable clear cache on repository changes
              |--------------------------------------------------------------------------
              |
              */
            'enabled' => true,

            /*
              |--------------------------------------------------------------------------
              | Actions in Repository
              |--------------------------------------------------------------------------
              |
              | create : Clear Cache on create Entry in repository
              | update : Clear Cache on update Entry in repository
              | delete : Clear Cache on delete Entry in repository
              |
              */
            'on'      => [
                'create' => true,
                'update' => true,
                'delete' => true,
            ]
        ],

        'params'     => [
            /*
            |--------------------------------------------------------------------------
            | Skip Cache Params
            |--------------------------------------------------------------------------
            |
            |
            | Ex: http://prettus.local/?search=lorem&skipCache=true
            |
            */
            'skipCache' => 'skipCache'
        ],

        /*
       |--------------------------------------------------------------------------
       | Methods Allowed
       |--------------------------------------------------------------------------
       |
       | methods cacheable : all, paginate, find, findByField, findWhere, getByCriteria
       |
       | Ex:
       |
       | 'only'  =>['all','paginate'],
       |
       | or
       |
       | 'except'  =>['find'],
       */
        'allowed'    => [
            'only'   => null,
            'except' => null
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Criteria Config
    |--------------------------------------------------------------------------
    |
    | Settings of request parameters names that will be used by Criteria
    |
    */
    'criteria'   => [
        /*
        |--------------------------------------------------------------------------
        | Accepted Conditions
        |--------------------------------------------------------------------------
        |
        | Conditions accepted in consultations where the Criteria
        |
        | Ex:
        |
        | 'acceptedConditions'=>['=','like']
        |
        | $query->where('foo','=','bar')
        | $query->where('foo','like','bar')
        |
        */
        'acceptedConditions' => [
            '=',
            'like'
        ],
        /*
        |--------------------------------------------------------------------------
        | Request Params
        |--------------------------------------------------------------------------
        |
        | Request parameters that will be used to filter the query in the repository
        |
        | Params :
        |
        | - search : Searched value
        |   Ex: http://prettus.local/?search=lorem
        |
        | - searchFields : Fields in which research should be carried out
        |   Ex:
        |    http://prettus.local/?search=lorem&searchFields=name;email
        |    http://prettus.local/?search=lorem&searchFields=name:like;email
        |    http://prettus.local/?search=lorem&searchFields=name:like
        |
        | - filter : Fields that must be returned to the response object
        |   Ex:
        |   http://prettus.local/?search=lorem&filter=id,name
        |
        | - orderBy : Order By
        |   Ex:
        |   http://prettus.local/?search=lorem&orderBy=id
        |
        | - sortedBy : Sort
        |   Ex:
        |   http://prettus.local/?search=lorem&orderBy=id&sortedBy=asc
        |   http://prettus.local/?search=lorem&orderBy=id&sortedBy=desc
        |
        */
        'params'             => [
            'search'       => 'search',
            'searchFields' => 'searchFields',
            'filter'       => 'filter',
            'orderBy'      => 'orderBy',
            'sortedBy'     => 'sortedBy',
            'with'         => 'with'
        ]
    ],
    /*
    |--------------------------------------------------------------------------
    | Generator Config
    |--------------------------------------------------------------------------
    |
    */

//    'generator' => [
//        'basePath' => app_path(),
//        'rootNamespace' => 'App\\',
//        'paths' => [
//            'models' => 'Entities',
//            'repositories' => 'Repositories/Bar',
//            'interfaces' => 'Repositories/Bar',
//            'transformers' => 'Transformers/Bar',
//            'presenters' => 'Presenters/Bar',
//            'validators' => 'Validators/Bar',
//            'controllers' => 'Http/Controllers/Bar',
//            'provider' => 'RepositoryServiceProvider',
//            'criteria' => 'Criteria/Bar',
//            'stubsOverridePath' => app_path()
//        ]
//    ],

//    'generator' => [
//        'basePath' => app_path(),
//        'rootNamespace' => 'App\\',
//        'paths' => [
//            'models' => 'Entities',
//            'repositories' => 'Repositories/Admin/Admin',
//            'interfaces' => 'Repositories/Admin/Admin',
//            'transformers' => 'Transformers/Admin/Admin',
//            'presenters' => 'Presenters/Admin/Admin',
//            'validators' => 'Validators/Admin/Admin',
//            'controllers' => 'Http/Controllers/Admin/Admin',
//            'provider' => 'RepositoryServiceProvider',
//            'criteria' => 'Criteria/Admin/Admin',
//            'stubsOverridePath' => app_path()
//        ]
//    ],

//    'generator' => [
//        'basePath' => app_path(),
//        'rootNamespace' => 'App\\',
//        'paths' => [
//            'models' => 'Entities',
//            'repositories' => 'Repositories/Api/MiniProgram',
//            'interfaces' => 'Repositories/Api/MiniProgram',
//            'transformers' => 'Transformers/Api/MiniProgram',
//            'presenters' => 'Presenters/Api/MiniProgram',
//            'validators' => 'Validators/Api/MiniProgram',
//            'controllers' => 'Http/Controllers/Api/MiniProgram',
//            'provider' => 'RepositoryServiceProvider',
//            'criteria' => 'Criteria/Api/MiniProgram',
//            'stubsOverridePath' => app_path()
//        ]
//    ],

//    'generator' => [
//        'basePath' => app_path(),
//        'rootNamespace' => 'App\\',
//        'paths' => [
//            'models' => 'Entities',
//            'repositories' => 'Repositories/Api/OfficialAccount',
//            'interfaces' => 'Repositories/Api/OfficialAccount',
//            'transformers' => 'Transformers/Api/OfficialAccount',
//            'presenters' => 'Presenters/Api/OfficialAccount',
//            'validators' => 'Validators/Api/OfficialAccount',
//            'controllers' => 'Http/Controllers/Api/OfficialAccount',
//            'provider' => 'RepositoryServiceProvider',
//            'criteria' => 'Criteria/Api/OfficialAccount',
//            'stubsOverridePath' => app_path()
//        ]
//    ],

    'generator' => [
        'basePath' => app_path(),
        'rootNamespace' => 'App\\',
        'paths' => [
            'models' => 'Entities',
            'repositories' => 'Repositories/Api',
            'interfaces' => 'Repositories/Api',
            'transformers' => 'Transformers/Api',
            'presenters' => 'Presenters/Api',
            'validators' => 'Validators/Api',
            'controllers' => 'Http/Controllers/Api',
            'provider' => 'RepositoryServiceProvider',
            'criteria' => 'Criteria/Api',
            'stubsOverridePath' => app_path()
        ]
    ],
];
