<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Gestion de dettes et creances',
            ],

            'routes' => [
                /*
                 * Route pour accéder à l'interface de la documentation de l'API
                 */
                'api' => 'api/documentation',
            ],
            'paths' => [
                /*
                 * Modifiez pour inclure l'URL complète dans l'interface utilisateur pour les assets
                 */
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),

                /*
                 * Nom du fichier JSON généré pour la documentation
                 */
                'docs_json' => 'api-docs.json',

                /*
                 * Nom du fichier YAML généré pour la documentation
                 */
                'docs_yaml' => 'api-docs.yaml',

                /*
                 * Définir sur `json` ou `yaml` pour déterminer quel fichier de documentation utiliser dans l'interface utilisateur
                 */
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                /*
                 * Chemins absolus vers les répertoires contenant les annotations Swagger
                 */
                'annotations' => [
                    base_path('app'),
                ],
            ],
        ],
    ],
    'defaults' => [
        'routes' => [
            /*
             * Route pour accéder aux annotations Swagger analysées
             */
            'docs' => 'docs',

            /*
             * Route pour le rappel d'authentification Oauth2
             */
            'oauth2_callback' => 'api/oauth2-callback',

            /*
             * Middleware pour prévenir un accès non autorisé à la documentation API
             */
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],

            /*
             * Options du groupe de routes
             */
            'group_options' => [],
        ],

        'paths' => [
            /*
             * Chemin absolu vers l'emplacement où les annotations analysées seront stockées
             */
            'docs' => storage_path('api-docs'),

            /*
             * Chemin absolu vers le répertoire où exporter les vues
             */
            'views' => base_path('resources/views/vendor/l5-swagger'),

            /*
             * Modifiez pour définir le chemin de base de l'API
             */
            'base' => env('L5_SWAGGER_BASE_PATH', null),

            /*
             * Modifiez pour définir le chemin où les assets Swagger UI doivent être stockés
             */
            'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),

            /*
             * Chemins absolus vers les répertoires à exclure de l'analyse
             */
            'excludes' => [],
        ],

        'scanOptions' => [
            /**
             * Configuration des analyseurs par défaut pour swagger-php.
             */
            'default_processors_configuration' => [],

            /**
             * Exclure certains répertoires lors de l'analyse.
             */
            'exclude' => [],

            /*
             * Permet de générer des spécifications pour OpenAPI 3.0.0 ou OpenAPI 3.1.0
             */
            'open_api_spec_version' => env('L5_SWAGGER_OPEN_API_SPEC_VERSION', \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION),
        ],

        /*
         * Définitions de sécurité de l'API à générer dans le fichier de documentation.
        */
        'securityDefinitions' => [
            'securitySchemes' => [
                /*
                 * Exemples de schémas de sécurité
                 */
                'constants' => [
                    'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://mon-hôte-par-défaut.com'),
                ],
            ],
            'security' => [
                /*
                 * Exemples de sécurités
                 */
                [
                    // Exemple : 'passport' => []
                ],
            ],
        ],

        /*
         * Régénérer la documentation à chaque demande en mode développement
         * Désactiver en production
         */
        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),

        /*
         * Générer une copie de la documentation au format YAML
         */
        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),

        /*
         * Définir les adresses IP de proxy de confiance
         */
        'proxy' => false,

        /*
         * Configurations supplémentaires pour Swagger UI
         */
        'additional_config_url' => null,

        /*
         * Appliquer un tri à la liste des opérations de chaque API
         */
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

        /*
         * Paramètre pour valider l'URL Swagger UI côté JS
         */
        'validator_url' => null,

        /*
         * Configuration des paramètres Swagger UI
         */
        'ui' => [
            'display' => [
                'dark_mode' => env('L5_SWAGGER_UI_DARK_MODE', false),
                /*
                 * Contrôle l'extension par défaut des opérations et des balises
                 */
                'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),

                /**
                 * Activer/désactiver le filtrage dans la barre supérieure
                 */
                'filter' => env('L5_SWAGGER_UI_FILTERS', true),
            ],

            'authorization' => [
                /*
                 * Persiste les données d'autorisation même après fermeture du navigateur
                 */
                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', false),

                'oauth2' => [
                    /*
                     * Ajouter PKCE au flux AuthorizationCodeGrant si activé
                     */
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],
        /*
         * Constantes à utiliser dans les annotations
         */
        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://mon-hôte-par-défaut.com'),
        ],
    ],
];
