# prestashop-product-shortcode
Display product information with the shortcode [presta-product] in Wordpress.

Currently needs testing and finetuning. For example, what is the XML response if the website has
only one language ?

## Permissions

While in Prestashop backoffice, enable Web service in `Advanced parameters > Web service`. Permissions needed (only GET) are :
 * categories
 * images
 * languages
 * products
 * tax_rule_groups
 * tax_rules
 * taxes