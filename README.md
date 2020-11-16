# Dismoi.io

Bulle’s Website

## Development

### Backend

Put your mysql dump in the `initdb.d/` folder.
> It would be loaded when the mysql container start.

If the dump come from production you should clean it first.
Running the following command will replace the domain in any `.sql` file present in `initdb.d/` folder:
```
bin/replace-domain.sh
```

Then start the containers:
```
docker-compose up
```

> A phpmyadmin is available at: http://localhost:8081

> If anything goes wrong, you can try to clean everything docker-compose related:
> ```
> docker-compose rm
> ```

### Frontend
Install NVM. You make need to install node.js 8.9.3 if you dont have it already

Navigate to themes/divi-child-theme. Run `nvm use`

Install packages by running `yarn install`

`yarn run watch` for development

`yarn run build` for production

## Wordpress migration

1) Download production uploads directory
2) Get production SQL dump 
3) Update option_value "siteurl" and "home" in wp_options table
4) Drop assets into the local uploads directory
5) Search and replace in domains in SQL
6) Import modified data dump

More information here -> https://wordpress.org/support/article/moving-wordpress/


## Wishlist

* Add a Uglification to the CSS output without removing the style header comments


