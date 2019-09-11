## Get started with Symfony 4 flex

    composer require appkweb/easy-crud-bundle "dev-master"

## Step 1 - Add route

In your config/routes.yaml file add this lines for generator component with your custom prefix :

    appkweb_easy_crud_generator_annotions:
          resource: '@EasyCrudBundle/Controller/'
          type: annotation
          prefix: /your-prefix
        
In the same file add crud views routing :

    appkweb_easy_crud_views:
      resource: '@EasyCrudBundle/Resources/config/'
      type:     directory
      prefix: /your-prefix
            