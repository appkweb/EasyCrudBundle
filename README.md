## Get started with Symfony 4 flex

    composer require appkweb/easy-crud-bundle "dev-master"

## Step 1 - Add route

In your config/routes.yaml file add this lines :

    appkweb_easy_crud:
          resource: '@EasyCrudBundle/Controller/'
          type: annotation
          prefix: /your-prefix
          
Go to http://localhost:you-port/your-prefix/generator/add.html