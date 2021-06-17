# www.dismoi.io

DisMoi’s Website


### Development

Install NVM. You make need to install node.js 8.9.3 if you dont have it already

Navigate to themes/divi-child-theme. Run `nvm use`

Install packages by running `yarn install`

`yarn run watch` for development

`yarn run build` for production

`docker-compose up` for docker

### Wordpress migration

1) Download production uploads directory
2) Get production SQL dump 
3) Update option_value "siteurl" and "home" in wp_options table
4) Drop assets into the local uploads directory
5) Search and replace in domains in SQL
6) Import modified data dump

More information here -> https://wordpress.org/support/article/moving-wordpress/

### Update the js profiles app

To force cache refresh : change the value in _Version JS Bundle pour la page "Les Contributeurs"_ in /wp-admin/customize.php?theme=divi-child-bulle _Configuration Bulles/Dismoi_

### Wishlist

* Add a Uglification to the CSS output without removing the style header comments

### URL and Redirection

`rewrite_rules` are used for `eclaireur` and `en/guides` path and redirection profiles pages.
To do that the function `function dismoi_profiler_rewrite_url( $wp_rewrite )` is used in `functions.php`
For any changes, Wordpress have to flush the `rewrite_rules` to apply the new change, to do that :
- go on backoffice
- find permalink page
- save with the button (no need to change anything else)

> saving will flush rewrite_rules and generate the new one
